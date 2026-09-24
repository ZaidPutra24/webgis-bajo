<?php

namespace App\Support;

use App\Models\JarakSekolahLokasi;
use Illuminate\Support\Collection;

/**
 * AksesibilitasAnalyzer
 *
 * Menghitung akses desa -> sekolah terdekat PER JENJANG memakai data rute riil
 * di tabel jaraksekolahlokasi (struktur "1 baris = 1 leg 1 moda"). Sengaja
 * TIDAK memakai fallback jarak garis lurus: untuk desa pulau/pesisir Bajo,
 * garis lurus melewati laut sehingga akan tampak dekat padahal sebenarnya
 * butuh penyeberangan. Jenjang yang belum punya rute riil ditandai "tanpa data".
 *
 * Kelas ini hanya bergantung pada Collection/Eloquent model (tanpa request/
 * session) supaya mudah diuji terpisah dari controller.
 */
class AksesibilitasAnalyzer
{
    /**
     * Jenjang inti + jenjang_id sekolah yang dianggap penyedia layanannya.
     * (Sama dengan KepadatanUsiaSekolahController::JENJANG_INTI.)
     */
    public const JENJANG_INTI = [
        'PAUD' => [1, 2, 10, 11],
        'SD'   => [3, 4],
        'SMP'  => [5, 6],
        'SMA'  => [7, 8, 9],
    ];

    /** jenjang_id di tabel penduduk_jenjang untuk tiap jenjang inti. */
    public const PENDUDUK_JENJANG_ID = [
        'PAUD' => 10,
        'SD'   => 3,
        'SMP'  => 5,
        'SMA'  => 7,
    ];

    public const STATUS_LABEL = [
        'kritis'  => 'Critical',
        'sedang'  => 'Moderate',
        'baik'    => 'Good',
        'no_data' => 'No route data',
    ];

    /**
     * @param  Collection  $wilayahs   koleksi WilayahDesa
     * @param  Collection  $sekolahs   koleksi Sekolah (berkoordinat)
     * @param  Collection  $jarakRows  semua baris JarakSekolahLokasi (tanpa route_geojson)
     * @param  float       $ambangMnt  ambang waktu tempuh "terlalu jauh" (default 60 menit)
     * @param  string      $modaBasis  moda yang dipakai menilai ambang: 'jalan_kaki' (skenario
     *                                  tanpa kendaraan) atau 'kendaraan'
     * @param  Collection|null $pendudukJenjang  baris PendudukJenjang (opsional) untuk
     *                                           menghitung jumlah anak terdampak per jenjang
     */
    public static function analisis(Collection $wilayahs, Collection $sekolahs, Collection $jarakRows, float $ambangMnt = 60.0, ?Collection $pendudukJenjang = null, string $modaBasis = 'jalan_kaki'): array
    {
        $pendudukByWilayah = ($pendudukJenjang ?? collect())->groupBy('wilayah_id');
        $sekolahById = $sekolahs->keyBy('id');
        $rowsByWilayah = $jarakRows->groupBy('wilayah_id');

        $desaHasil = $wilayahs->map(function ($desa) use ($sekolahById, $rowsByWilayah, $pendudukByWilayah, $ambangMnt, $modaBasis) {
            $opsiPerSekolah = JarakSekolahLokasi::susunOpsiRute($rowsByWilayah->get($desa->id, collect()));

            $jenjangHasil = [];
            foreach (self::JENJANG_INTI as $label => $jenjangIds) {
                $jenjangHasil[$label] = self::sekolahTerdekatJenjang($opsiPerSekolah, $sekolahById, $jenjangIds);
            }

            return self::ringkasDesa($desa, $jenjangHasil, $ambangMnt, $pendudukByWilayah->get($desa->id, collect()), $modaBasis);
        })->values();

        return [
            'desa'      => $desaHasil,
            'ringkasan' => self::ringkasan($desaHasil, $ambangMnt),
            'per_moda'  => self::statistikPerModa($desaHasil),
        ];
    }

    /**
     * Sekolah terdekat (waktu jalan kaki terkecil) dari satu jenjang.
     * Return null jika desa belum punya rute riil ke sekolah jenjang tsb.
     */
    protected static function sekolahTerdekatJenjang(Collection $opsiPerSekolah, Collection $sekolahById, array $jenjangIds): ?array
    {
        $kandidat = [];

        foreach ($opsiPerSekolah as $sekolahId => $opsiList) {
            $sekolah = $sekolahById->get($sekolahId);
            if (!$sekolah || !in_array((int) $sekolah->jenjang_id, $jenjangIds, true)) {
                continue;
            }

            $jalan = JarakSekolahLokasi::pilihOpsi($opsiList, 'jalan_kaki');
            if ($jalan === null) {
                continue;
            }
            // pilihOpsi() jatuh ke opsi tercepat kalau tak ada jalan kaki; kita butuh
            // waktu jalan kaki eksplisit agar perbandingan antar desa adil.
            $walkOpsi = $jalan['moda'] === 'jalan_kaki' ? $jalan : null;

            $driveOpsi = null;
            $tipeAcuan = $jalan['tipe'];
            $drives = array_filter($opsiList, fn ($o) => $o['moda'] === 'kendaraan' && $o['tipe'] === $tipeAcuan);
            if ($drives) {
                usort($drives, fn ($a, $b) => $a['waktu_mnt'] <=> $b['waktu_mnt']);
                $driveOpsi = $drives[0];
            }

            $kandidat[] = [
                'sekolah'   => $sekolah,
                'walk'      => $walkOpsi,
                'drive'     => $driveOpsi,
                'acuan'     => $jalan,
                'ada_darat' => count(array_filter($opsiList, fn ($o) => !$o['pakai_perahu'])) > 0,
            ];
        }

        if (empty($kandidat)) {
            return null;
        }

        // Terdekat = waktu acuan (jalan kaki) terkecil.
        usort($kandidat, fn ($a, $b) => $a['acuan']['waktu_mnt'] <=> $b['acuan']['waktu_mnt']);
        $best = $kandidat[0];

        return [
            'sekolah_id'          => (int) $best['sekolah']->id,
            'nama_sekolah'        => $best['sekolah']->nama_sekolah,
            'jarak_km'            => round($best['acuan']['jarak_km'], 2),
            'walk_mnt'            => $best['walk'] ? round($best['walk']['waktu_mnt'], 1) : null,
            'drive_mnt'           => $best['drive'] ? round($best['drive']['waktu_mnt'], 1) : null,
            'boat_mnt'            => $best['acuan']['pakai_perahu'] ? round($best['acuan']['boat_mnt'], 1) : null,
            'butuh_perahu'        => (bool) $best['acuan']['pakai_perahu'],
            // ada sekolah jenjang ini yang bisa dicapai TANPA perahu?
            'ada_alternatif_darat' => collect($kandidat)->contains(fn ($k) => $k['ada_darat']),
            'jumlah_sekolah_terhubung' => count($kandidat),
            'leg_ids'             => $best['acuan']['leg_ids'],
        ];
    }

    /** Satu baris ringkasan per desa. */
    protected static function ringkasDesa($desa, array $jenjangHasil, float $ambangMnt, Collection $pendudukDesa, string $modaBasis): array
    {
        $balai = $desa->titik_balai;

        $terisi = array_filter($jenjangHasil);
        $lebihAmbang = [];
        $butuhPerahu = [];
        $tanpaData   = [];
        $tanpaDarat  = [];
        $terburuk    = null; // cell jenjang dgn waktu jalan kaki terlama

        foreach ($jenjangHasil as $label => $cell) {
            if ($cell === null) {
                $tanpaData[] = $label;
                continue;
            }
            $waktu = $modaBasis === 'kendaraan'
                ? ($cell['drive_mnt'] ?? $cell['walk_mnt'])
                : ($cell['walk_mnt'] ?? $cell['drive_mnt']);
            if ($waktu !== null && $waktu > $ambangMnt) {
                $lebihAmbang[] = $label;
            }
            if ($cell['butuh_perahu']) {
                $butuhPerahu[] = $label;
            }
            if (!$cell['ada_alternatif_darat']) {
                $tanpaDarat[] = $label;
            }
            if ($terburuk === null || ($waktu ?? 0) > ($terburuk['waktu'] ?? 0)) {
                $terburuk = ['label' => $label, 'waktu' => $waktu, 'cell' => $cell];
            }
        }

        // Penduduk usia jenjang & ATS per jenjang (kalau data tersedia)
        $pendudukPerJenjang = [];
        foreach (self::PENDUDUK_JENJANG_ID as $label => $jenjangId) {
            $pj = $pendudukDesa->firstWhere('jenjang_id', $jenjangId);
            $pendudukPerJenjang[$label] = [
                'penduduk' => $pj ? (int) $pj->penduduk_l + (int) $pj->penduduk_p : null,
                'ats'      => $pj ? (int) $pj->putus_sekolah_l + (int) $pj->putus_sekolah_p : null,
            ];
        }
        // Anak yang benar-benar terdampak = penduduk usia JENJANG yang aksesnya > ambang
        // (bukan seluruh penduduk usia sekolah desa).
        $anakTerdampak = 0;
        foreach ($lebihAmbang as $label) {
            $anakTerdampak += (int) ($pendudukPerJenjang[$label]['penduduk'] ?? 0);
        }

        if ($terburuk === null) {
            $status = 'no_data';
        } elseif (($terburuk['waktu'] ?? 0) > $ambangMnt) {
            $status = 'kritis';
        } elseif (($terburuk['waktu'] ?? 0) > $ambangMnt / 2) {
            $status = 'sedang';
        } else {
            $status = 'baik';
        }

        return [
            'id'                  => (int) $desa->id,
            'nama_wilayah'        => $desa->nama_wilayah,
            'lat'                 => $balai['latitude'] ?? null,
            'lng'                 => $balai['longitude'] ?? null,
            'usia_sekolah'        => (int) $desa->total_penduduk_usia_sekolah,
            'jenjang'             => $jenjangHasil,
            'penduduk_jenjang'    => $pendudukPerJenjang,
            'anak_terdampak'      => $anakTerdampak,
            'terburuk_jenjang'    => $terburuk['label'] ?? null,
            'terburuk_waktu_mnt'  => $terburuk['waktu'] ?? null,
            'terburuk_walk_mnt'   => $terburuk['cell']['walk_mnt'] ?? null,
            'terburuk_drive_mnt'  => $terburuk['cell']['drive_mnt'] ?? null,
            'terburuk_boat_mnt'   => $terburuk['cell']['boat_mnt'] ?? null,
            'status'              => $status,
            'status_label'        => self::STATUS_LABEL[$status],
            'jenjang_lebih_ambang' => $lebihAmbang,
            'jenjang_butuh_perahu' => $butuhPerahu,
            'jenjang_tanpa_darat'  => $tanpaDarat,
            'jenjang_tanpa_data'   => $tanpaData,
            'leg_ids_kritis'      => $terburuk['cell']['leg_ids'] ?? [],
        ];
    }

    protected static function ringkasan(Collection $desa, float $ambangMnt): array
    {
        return [
            'total_desa'        => $desa->count(),
            'ambang_mnt'        => $ambangMnt,
            'desa_lebih_ambang' => $desa->filter(fn ($d) => !empty($d['jenjang_lebih_ambang']))->count(),
            'desa_butuh_perahu' => $desa->filter(fn ($d) => !empty($d['jenjang_butuh_perahu']))->count(),
            'desa_tanpa_darat'  => $desa->filter(fn ($d) => !empty($d['jenjang_tanpa_darat']))->count(),
            'desa_data_kurang'  => $desa->filter(fn ($d) => !empty($d['jenjang_tanpa_data']))->count(),
            'anak_terdampak'    => (int) $desa->sum('anak_terdampak'),
        ];
    }

    /** Statistik sederhana per moda dari semua sel (desa x jenjang) yang punya data. */
    protected static function statistikPerModa(Collection $desa): array
    {
        // array_values dulu: key string jenjang (PAUD/SD/..) akan saling menimpa antar desa
        // kalau langsung di-flatMap.
        $cells = $desa->flatMap(fn ($d) => array_values(array_filter($d['jenjang'])))->values();

        $hitung = function (Collection $nilai): array {
            $nilai = $nilai->filter(fn ($v) => $v !== null)->sort()->values();
            $n = $nilai->count();
            if ($n === 0) {
                return ['n' => 0, 'rata' => null, 'median' => null, 'maks' => null];
            }
            $median = $n % 2 ? $nilai[intdiv($n, 2)] : ($nilai[$n / 2 - 1] + $nilai[$n / 2]) / 2;

            return [
                'n'      => $n,
                'rata'   => round($nilai->avg(), 1),
                'median' => round($median, 1),
                'maks'   => round($nilai->max(), 1),
            ];
        };

        return [
            'jalan_kaki' => $hitung($cells->pluck('walk_mnt')),
            'kendaraan'  => $hitung($cells->pluck('drive_mnt')),
            'perahu'     => $hitung($cells->pluck('boat_mnt')),
        ];
    }
}
