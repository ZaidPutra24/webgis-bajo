<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\WilayahDesa;
use App\Support\GeoHelper;
use App\Support\KMeans;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class KMeansClusteringController extends Controller
{
    /**
     * Palet warna tetap untuk tiap index cluster (dipakai di map & badge),
     * supaya warna cluster 0 selalu sama tiap kali halaman dibuka (selama
     * urutan cluster tidak berubah drastis).
     */
    protected const CLUSTER_COLORS = [
        '#4F46E5', '#dc2626', '#059669', '#d97706',
        '#0891b2', '#7c3aed', '#db2777', '#65a30d',
    ];

    public function index(Request $request)
    {
        // Jumlah cluster (k) dipilih admin, dibatasi 2–8 supaya tetap masuk
        // akal untuk jumlah desa yang biasanya tidak terlalu banyak.
        $k = (int) $request->input('k', 4);
        $k = max(2, min(8, $k));

        // ------------------------------------------------------------
        // 1) Siapkan data mentah: desa (dengan titik balai) & sekolah
        //    (dengan koordinat + jumlah siswa dari statistik_sekolah).
        // ------------------------------------------------------------
        $desas = WilayahDesa::orderBy('nama_wilayah')->get();

        $sekolahs = Sekolah::with(['jenjang', 'statistik'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $dataPoints = collect();   // baris data siap-cluster per desa
        $desaTanpaData = collect(); // desa yang tidak bisa diikutkan (data tidak lengkap)

        foreach ($desas as $desa) {
            $balai = $desa->titik_balai;

            if (!$balai || $sekolahs->isEmpty()) {
                $desaTanpaData->push($desa->nama_wilayah);
                continue;
            }

            $terdekat = $this->sekolahTerdekatDariTitik($balai['latitude'], $balai['longitude'], $sekolahs);

            if ($terdekat === null) {
                $desaTanpaData->push($desa->nama_wilayah);
                continue;
            }

            $jumlahSiswa = optional($terdekat['sekolah']->statistik)->jumlah_siswa ?? 0;

            $dataPoints->push([
                'desa_id'          => $desa->id,
                'nama_wilayah'     => $desa->nama_wilayah,
                'lat_desa'         => (float) $balai['latitude'],
                'lng_desa'         => (float) $balai['longitude'],
                'sumber_titik'     => $balai['sumber'],
                'sekolah_id'       => $terdekat['sekolah']->id,
                'nama_sekolah'     => $terdekat['sekolah']->nama_sekolah,
                'jenjang_sekolah'  => optional($terdekat['sekolah']->jenjang)->nama_jenjang,
                'lat_sekolah'      => (float) $terdekat['sekolah']->latitude,
                'lng_sekolah'      => (float) $terdekat['sekolah']->longitude,
                'jarak_km'         => $terdekat['jarak_km'],
                'jumlah_siswa'     => (int) $jumlahSiswa,
                'jumlah_sekolah_roi' => $desa->hitungJumlahSekolah($sekolahs),
            ]);
        }

        $totalDianalisis = $dataPoints->count();

        // K tidak boleh melebihi jumlah desa yang datanya lengkap.
        $k = max(2, min($k, max(2, $totalDianalisis)));

        $clusterStats = collect();
        $desaHasil = collect();
        $rekomendasi = collect();
        $inertiaPerK = collect();

        if ($totalDianalisis >= 2) {
            // ------------------------------------------------------------
            // 2) Susun feature matrix: [lat_desa, lng_desa, lat_sekolah,
            //    lng_sekolah, jumlah_siswa] lalu standarisasi (z-score)
            //    supaya skala koordinat & jumlah siswa sebanding.
            // ------------------------------------------------------------
            $rawVectors = $dataPoints->map(fn ($p) => [
                $p['lat_desa'], $p['lng_desa'], $p['lat_sekolah'], $p['lng_sekolah'], $p['jumlah_siswa'],
            ])->values()->all();

            $standardized = KMeans::standardize($rawVectors);
            $result = KMeans::fit($standardized['data'], $k);

            // Pakai array PHP biasa (bukan Collection) untuk tahap ini supaya
            // penulisan $dataPointsIndexed[$i]['cluster'] = ... aman — Collection
            // mengimplementasikan ArrayAccess tanpa referensi, jadi modifikasi
            // bersarang seperti itu tidak akan tersimpan balik ke Collection-nya.
            $dataPointsIndexed = $dataPoints->values()->all();
            foreach ($result['assignments'] as $i => $clusterIdx) {
                $dataPointsIndexed[$i]['cluster'] = $clusterIdx;
            }
            $dataPointsIndexed = collect($dataPointsIndexed);

            // ------------------------------------------------------------
            // 3) Statistik & label prioritas per cluster.
            // ------------------------------------------------------------
            $clusterStats = collect(range(0, $k - 1))->map(function ($c) use ($dataPointsIndexed) {
                $members = $dataPointsIndexed->where('cluster', $c);
                return [
                    'cluster'        => $c,
                    'warna'          => self::CLUSTER_COLORS[$c % count(self::CLUSTER_COLORS)],
                    'jumlah_desa'    => $members->count(),
                    'avg_jarak_km'   => $members->count() ? round($members->avg('jarak_km'), 2) : 0,
                    'avg_siswa'      => $members->count() ? round($members->avg('jumlah_siswa'), 1) : 0,
                    'total_siswa'    => $members->sum('jumlah_siswa'),
                    'desa'           => $members->pluck('nama_wilayah')->values(),
                ];
            });

            // Composite priority score: normalisasi 0..1 dari avg_jarak dan
            // avg_siswa lalu dijumlah — cluster desa yang JAUH dari sekolah
            // DAN sekolah terdekatnya sudah PADAT siswa = prioritas utama
            // untuk diusulkan lokasi sekolah baru.
            $maxJarak = $clusterStats->max('avg_jarak_km') ?: 1;
            $maxSiswa = $clusterStats->max('avg_siswa') ?: 1;

            $clusterStats = $clusterStats->map(function ($stat) use ($maxJarak, $maxSiswa) {
                $skorJarak = $maxJarak > 0 ? $stat['avg_jarak_km'] / $maxJarak : 0;
                $skorSiswa = $maxSiswa > 0 ? $stat['avg_siswa'] / $maxSiswa : 0;
                $stat['skor_prioritas'] = round(($skorJarak + $skorSiswa) / 2, 3);
                return $stat;
            })->sortByDesc('skor_prioritas')->values();

            $jumlahCluster = $clusterStats->count();
            $clusterStats = $clusterStats->map(function ($stat, $rank) use ($jumlahCluster) {
                if ($jumlahCluster <= 2) {
                    $stat['kategori'] = $rank === 0 ? 'Prioritas Tinggi' : 'Prioritas Rendah';
                } else {
                    $ambangTinggi = max(1, (int) ceil($jumlahCluster * 0.25));
                    $ambangSedang = max($ambangTinggi + 1, (int) ceil($jumlahCluster * 0.5));
                    if ($rank < $ambangTinggi) {
                        $stat['kategori'] = 'Prioritas Tinggi';
                    } elseif ($rank < $ambangSedang) {
                        $stat['kategori'] = 'Prioritas Sedang';
                    } else {
                        $stat['kategori'] = 'Prioritas Rendah';
                    }
                }
                return $stat;
            });

            $kategoriByCluster = $clusterStats->keyBy('cluster')->map(fn ($s) => $s['kategori']);
            $warnaByCluster = $clusterStats->keyBy('cluster')->map(fn ($s) => $s['warna']);

            $desaHasil = $dataPointsIndexed->map(function ($p) use ($kategoriByCluster, $warnaByCluster) {
                $p['kategori'] = $kategoriByCluster[$p['cluster']] ?? '–';
                $p['warna'] = $warnaByCluster[$p['cluster']] ?? '#94a3b8';
                return $p;
            })->sortBy(function ($p) {
                // Urutkan: kategori prioritas dulu (Tinggi > Sedang > Rendah),
                // lalu di dalam kategori yang sama, jarak ke sekolah terjauh dulu.
                $rankKategori = match ($p['kategori']) {
                    'Prioritas Tinggi' => 0,
                    'Prioritas Sedang' => 1,
                    default => 2,
                };
                return $rankKategori * 100000 - $p['jarak_km'];
            })->values();

            // ------------------------------------------------------------
            // 4) Usulan lokasi sekolah baru: untuk cluster berkategori
            //    "Prioritas Tinggi", pecah lagi secara geografis (murni
            //    koordinat desa, tanpa memakai skor siswa) supaya titik
            //    usulan tersebar mengikuti sebaran desa yang sebenarnya,
            //    bukan cuma satu centroid tunggal yang bisa menyesatkan
            //    kalau desa-desa prioritas tersebar jauh satu sama lain.
            // ------------------------------------------------------------
            $clusterPrioritasTinggi = $clusterStats->where('kategori', 'Prioritas Tinggi')->pluck('cluster');

            foreach ($clusterPrioritasTinggi as $c) {
                $anggota = $desaHasil->where('cluster', $c)->values();
                if ($anggota->isEmpty()) {
                    continue;
                }

                $subK = min(3, $anggota->count());
                $geoVectors = $anggota->map(fn ($p) => [$p['lat_desa'], $p['lng_desa']])->values()->all();
                $subResult = KMeans::fit($geoVectors, $subK, 200, 42 + $c);

                foreach (array_unique($subResult['assignments']) as $subC) {
                    $indices = array_keys($subResult['assignments'], $subC);
                    $anggotaSub = collect($indices)->map(fn ($i) => $anggota[$i]);

                    $rekomendasi->push([
                        'lat'              => round(collect($indices)->avg(fn ($i) => $geoVectors[$i][0]), 6),
                        'lng'              => round(collect($indices)->avg(fn ($i) => $geoVectors[$i][1]), 6),
                        'jumlah_desa'      => $anggotaSub->count(),
                        'desa_terlayani'   => $anggotaSub->pluck('nama_wilayah')->values(),
                        'total_siswa_terdampak' => $anggotaSub->sum('jumlah_siswa'),
                        'avg_jarak_saat_ini_km' => round($anggotaSub->avg('jarak_km'), 2),
                        'cluster_asal'     => $c,
                    ]);
                }
            }

            $rekomendasi = $rekomendasi->sortByDesc('avg_jarak_saat_ini_km')->values();

            // ------------------------------------------------------------
            // 5) Elbow method: hitung inertia untuk beberapa nilai k (2..min(8,n))
            //    supaya admin punya panduan visual memilih k yang "cukup baik"
            //    (titik di mana penurunan inertia mulai melandai).
            // ------------------------------------------------------------
            $maxKUntukElbow = max(2, min(8, $totalDianalisis));
            for ($ki = 2; $ki <= $maxKUntukElbow; $ki++) {
                $r = KMeans::fit($standardized['data'], $ki);
                $inertiaPerK->push(['k' => $ki, 'inertia' => $r['inertia']]);
            }
        }

        return view('admin.clustering.index', [
            'k'                => $k,
            'totalDesaSemua'   => $desas->count(),
            'totalDianalisis'  => $totalDianalisis,
            'desaTanpaData'    => $desaTanpaData->values(),
            'desaHasil'        => $desaHasil,
            'clusterStats'     => $clusterStats,
            'rekomendasi'      => $rekomendasi,
            'inertiaPerK'      => $inertiaPerK,
        ]);
    }

    /**
     * Cari sekolah terdekat (lintas semua jenjang) dari satu titik koordinat,
     * memakai jarak garis lurus (haversine). Dipakai sebagai representasi
     * "koordinat sekolah" pasangan tiap desa dalam data masukan K-Means.
     *
     * @return array{sekolah: Sekolah, jarak_km: float}|null
     */
    protected function sekolahTerdekatDariTitik(float $lat, float $lng, Collection $sekolahs): ?array
    {
        $terdekat = null;
        $jarakTerdekat = INF;

        foreach ($sekolahs as $sekolah) {
            $jarak = GeoHelper::haversineKm($lat, $lng, (float) $sekolah->latitude, (float) $sekolah->longitude);
            if ($jarak < $jarakTerdekat) {
                $jarakTerdekat = $jarak;
                $terdekat = $sekolah;
            }
        }

        return $terdekat ? ['sekolah' => $terdekat, 'jarak_km' => round($jarakTerdekat, 3)] : null;
    }
}
