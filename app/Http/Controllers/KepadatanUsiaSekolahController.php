<?php

namespace App\Http\Controllers;

use App\Models\PendudukJenjang;
use App\Models\Sekolah;
use App\Models\StandarRadiusLayanan;
use App\Models\StandarRombel;
use App\Models\WilayahDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class KepadatanUsiaSekolahController extends Controller
{
    /**
     * Ambang batas jumlah sekolah dalam ROI desa yang dianggap "sedikit"
     * ketika dipakai untuk menandai desa prioritas (kepadatan tinggi + sekolah minim).
     */
    protected const AMBANG_SEKOLAH_SEDIKIT = 1;

    /**
     * Jenjang inti yang dipakai fitur School-Age Density (sesuai standar
     * rombel Kepmendikdasmen No. 14/2026 & standar radius SNI 03-1733-2004),
     * plus daftar jenjang_id sekolah yang dianggap "penyedia" tiap jenjang
     * saat mencari sekolah terdekat / overlay jumlah sekolah dalam ROI.
     *
     * Catatan: Paket A/B/C diselenggarakan lewat PKBM/SKB (jenjang tsb tidak
     * dipecah per paket di tabel sekolah), jadi keduanya dipakai sebagai
     * "penyedia" untuk ketiga paket kesetaraan.
     */
    protected const JENJANG_INTI = [
        10 => ['label' => 'PAUD',    'sekolah_jenjang_ids' => [1, 2, 10, 11]],  // TK, RA, PAUD, KB
        3  => ['label' => 'SD',      'sekolah_jenjang_ids' => [3, 4]],          // SD, MI
        5  => ['label' => 'SMP',     'sekolah_jenjang_ids' => [5, 6]],          // SMP, MTs
        7  => ['label' => 'SMA',     'sekolah_jenjang_ids' => [7, 8, 9]],       // SMA, MA, SMK
    ];

    public function index(Request $request)
    {
        $wilayahs = WilayahDesa::orderBy('nama_wilayah')->get();

        // Ambil semua sekolah berkoordinat sekali saja agar tidak query berulang (N+1).
        $sekolahs = Sekolah::with(['jenjang', 'statistik'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $standarRombel = StandarRombel::pluck('maks_murid_per_rombel', 'jenjang_id');
        $standarRadius = StandarRadiusLayanan::get()->keyBy('jenjang_id');

        $pendudukJenjang = PendudukJenjang::whereIn('jenjang_id', array_keys(self::JENJANG_INTI))
            ->get()
            ->groupBy('wilayah_id');

        // -------------------------------------------------------------
        // 1) Hitung kepadatan TOTAL (semua jenjang inti) + overlay jumlah
        //    sekolah (point-in-polygon) per desa — dipakai untuk ringkasan
        //    & tabel utama (perilaku sama seperti versi sebelumnya).
        // -------------------------------------------------------------
        $wilayahs->each(function (WilayahDesa $desa) use ($sekolahs, $pendudukJenjang, $standarRombel, $standarRadius) {
            $desa->jumlah_sekolah = $desa->hitungJumlahSekolah($sekolahs);

            $rincianJenjang = $pendudukJenjang->get($desa->id, collect());
            $desa->rincian_jenjang = $this->hitungRincianPerJenjang(
                $desa, $rincianJenjang, $sekolahs, $standarRombel, $standarRadius
            );

            // Total penduduk usia sekolah versi baru = jumlah semua jenjang inti
            // (lebih representatif daripada agregat lama jika sudah ada rincian).
            $desa->total_penduduk_usia_sekolah_baru = $rincianJenjang->sum(fn ($p) => $p->total_penduduk);
            $desa->total_putus_sekolah = $rincianJenjang->sum(fn ($p) => $p->total_putus_sekolah);
        });

        // -------------------------------------------------------------
        // 2) Klasifikasi kepadatan memakai kuartil (Q1 & Q3), relatif antar desa.
        // -------------------------------------------------------------
        $nilaiKepadatan = $wilayahs
            ->pluck('kepadatan_usia_sekolah')
            ->filter(fn ($v) => $v !== null)
            ->sort()
            ->values();

        [$q1, $q3] = $this->hitungKuartil($nilaiKepadatan);

        $wilayahs->each(function ($desa) use ($q1, $q3) {
            $kepadatan = $desa->kepadatan_usia_sekolah;

            if ($kepadatan === null) {
                $desa->kategori_kepadatan = 'Data Belum Lengkap';
            } elseif ($kepadatan <= $q1) {
                $desa->kategori_kepadatan = 'Rendah';
            } elseif ($kepadatan <= $q3) {
                $desa->kategori_kepadatan = 'Sedang';
            } else {
                $desa->kategori_kepadatan = 'Tinggi';
            }

            $desa->prioritas = $desa->kategori_kepadatan === 'Tinggi'
                && $desa->jumlah_sekolah <= self::AMBANG_SEKOLAH_SEDIKIT;

            // Prioritas versi jenjang: desa yang untuk SALAH SATU jenjang inti
            // sudah di luar radius layanan standar DAN daya tampung sekolahnya
            // sudah tidak cukup.
            $desa->ada_jenjang_bermasalah = collect($desa->rincian_jenjang)
                ->contains(fn ($r) => ($r['terlayani'] === false) || ($r['kapasitas_cukup'] === false));
        });

        $totalDesa = $wilayahs->count();
        $totalDesaTinggi = $wilayahs->where('kategori_kepadatan', 'Tinggi')->count();
        $totalDesaPrioritas = $wilayahs->where('prioritas', true)->count();
        $totalDesaBermasalahJenjang = $wilayahs->where('ada_jenjang_bermasalah', true)->count();
        $rataKepadatan = round($nilaiKepadatan->avg() ?? 0, 2);

        $wilayahs = $wilayahs->sortByDesc(function ($desa) {
            return [$desa->prioritas ? 1 : 0, $desa->kepadatan_usia_sekolah ?? -1];
        })->values();

        $jenjangInti = collect(self::JENJANG_INTI)->map(function ($v, $id) use ($standarRombel, $standarRadius) {
            return [
                'jenjang_id'               => $id,
                'label'                    => $v['label'],
                'maks_murid_per_rombel'    => $standarRombel[$id] ?? null,
                'radius_meter'             => $standarRadius[$id]->radius_meter ?? null,
                'penduduk_pendukung_ideal' => $standarRadius[$id]->penduduk_pendukung_ideal ?? null,
                'sumber_radius'            => $standarRadius[$id]->sumber ?? null,
            ];
        })->values();

        return view('admin.kepadatan.index', compact(
            'wilayahs',
            'totalDesa',
            'totalDesaTinggi',
            'totalDesaPrioritas',
            'totalDesaBermasalahJenjang',
            'rataKepadatan',
            'q1',
            'q3',
            'jenjangInti'
        ));
    }

    /**
     * Hitung rincian per jenjang inti untuk satu desa: kepadatan jenjang
     * tsb, sekolah terdekat dari titik balai desa + jaraknya, status
     * terlayani/tidak (vs standar radius SNI, dengan prioritas data waktu
     * tempuh riil bila ada), dan cross-check kapasitas daya tampung vs
     * standar rombel (Kepmendikdasmen 14/2026).
     *
     * @return Collection<int, array>
     */
    protected function hitungRincianPerJenjang(
        WilayahDesa $desa,
        Collection $rincianJenjang,
        Collection $sekolahs,
        Collection $standarRombel,
        Collection $standarRadius
    ): Collection {
        $rincianByJenjang = $rincianJenjang->keyBy('jenjang_id');

        return collect(self::JENJANG_INTI)->map(function ($config, $jenjangId) use (
            $desa, $rincianByJenjang, $sekolahs, $standarRombel, $standarRadius
        ) {
            $penduduk = $rincianByJenjang->get($jenjangId);
            $totalPenduduk = $penduduk ? $penduduk->total_penduduk : 0;
            $totalPutusSekolah = $penduduk ? $penduduk->total_putus_sekolah : 0;

            $kepadatanJenjang = (!empty($desa->luas_wilayah) && (float) $desa->luas_wilayah > 0)
                ? round($totalPenduduk / (float) $desa->luas_wilayah, 2)
                : null;

            // Sekolah penyedia jenjang ini yang ada di dalam ROI desa (dipakai
            // untuk cross-check daya tampung).
            $sekolahJenjangIds = $config['sekolah_jenjang_ids'];
            $sekolahPenyedia = $sekolahs->whereIn('jenjang_id', $sekolahJenjangIds);
            $sekolahDalamRoi = $desa->daftarSekolahDalamRoi($sekolahPenyedia);

            $maksRombel = $standarRombel[$jenjangId] ?? null;
            $dayaTampungStandar = $sekolahDalamRoi->sum(function ($s) use ($maksRombel) {
                $jumlahRombel = optional($s->statistik)->jumlah_rombel ?? 0;
                return $maksRombel ? $jumlahRombel * $maksRombel : 0;
            });
            $dayaTampungManual = $sekolahDalamRoi->sum(fn ($s) => optional($s->statistik)->daya_tampung ?? 0);

            // Pakai yang lebih konservatif (kecil) antara hitungan standar rombel
            // vs input manual, supaya tidak melebih-lebihkan kapasitas riil.
            $kandidatDayaTampung = array_filter([$dayaTampungStandar, $dayaTampungManual], fn ($v) => $v > 0);
            $dayaTampungEfektif = $sekolahDalamRoi->isEmpty()
                ? 0
                : (empty($kandidatDayaTampung) ? 0 : min($kandidatDayaTampung));

            $kapasitasCukup = $sekolahDalamRoi->isEmpty() ? null : ($dayaTampungEfektif >= $totalPenduduk);

            // Sekolah terdekat dari titik balai desa (prioritas jarak riil,
            // fallback haversine) + status terlayani vs standar radius SNI.
            $sekolahTerdekat = $desa->sekolahTerdekat($jenjangId, $sekolahPenyedia);

            $radius = $standarRadius[$jenjangId] ?? null;
            $terlayani = null;
            if ($sekolahTerdekat && $radius) {
                $terlayani = $sekolahTerdekat['jarak_km'] <= $radius->radius_km;
            }

            return [
                'jenjang_id'              => $jenjangId,
                'label'                   => $config['label'],
                'total_penduduk'          => $totalPenduduk,
                'total_putus_sekolah'     => $totalPutusSekolah,
                'persen_putus_sekolah'    => $totalPenduduk > 0 ? round($totalPutusSekolah / $totalPenduduk * 100, 1) : null,
                'kepadatan'               => $kepadatanJenjang,
                'jumlah_sekolah_roi'      => $sekolahDalamRoi->count(),
                'daya_tampung'            => $sekolahDalamRoi->isEmpty() ? null : $dayaTampungEfektif,
                'kapasitas_cukup'         => $kapasitasCukup,
                'kekurangan_daya_tampung' => $kapasitasCukup === false ? max(0, $totalPenduduk - $dayaTampungEfektif) : 0,
                'sekolah_terdekat'        => $sekolahTerdekat,
                'radius_standar_km'       => $radius->radius_km ?? null,
                'terlayani'               => $terlayani,
            ];
        })->values();
    }

    /**
     * Hitung nilai kuartil 1 (25%) dan kuartil 3 (75%) dari koleksi angka yang
     * sudah terurut ascending, memakai metode interpolasi linear sederhana.
     * Mengembalikan [null, null] jika data kosong.
     */
    protected function hitungKuartil($sortedValues): array
    {
        $n = $sortedValues->count();

        if ($n === 0) {
            return [null, null];
        }

        if ($n === 1) {
            $only = $sortedValues->first();
            return [$only, $only];
        }

        $percentile = function (float $p) use ($sortedValues, $n) {
            $index = $p * ($n - 1);
            $lower = (int) floor($index);
            $upper = (int) ceil($index);
            $fraction = $index - $lower;

            $lowerVal = $sortedValues->get($lower);
            $upperVal = $sortedValues->get($upper);

            return round($lowerVal + ($upperVal - $lowerVal) * $fraction, 2);
        };

        return [$percentile(0.25), $percentile(0.75)];
    }
}
