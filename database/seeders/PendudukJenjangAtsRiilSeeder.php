<?php

namespace Database\Seeders;

use App\Models\PendudukJenjang;
use App\Models\WilayahDesa;
use Illuminate\Database\Seeder;

/**
 * DATA RIIL ATS per jenjang (PAUD/SD/SMP/SMA) untuk 11 desa wilayah Bajo,
 * dari "Analisis_ATS_PKBM_Wilayah_Bajo.xlsx" (sheet 'Fitur 1 - ATS per
 * Jenjang'). ATS = DO (putus sekolah) + LTM (lulus tidak melanjutkan) + BPB
 * (belum pernah sekolah, usia sesuai jenjang) — lihat sheet 'Ringkasan' pada
 * file sumber untuk metodologi lengkap.
 *
 * PENTING — apa yang REAL dan apa yang masih ESTIMASI di sini:
 *
 * 1. TOTAL ATS per jenjang (kolom ATS_* di bawah) = REAL, angka pasti dari
 *    hasil analisis Dinas. Ini yang paling penting & paling akurat dari
 *    seluruh proses seeding penduduk_jenjang sejauh ini.
 *
 * 2. Split L/P dari total ATS tsb = ESTIMASI, dihitung proporsional memakai
 *    rasio gender agregat desa (penduduk_usia_sekolah_l/p di wilayah_desa,
 *    yang REAL). Data sumber ATS TIDAK memerinci ATS per jenjang menurut
 *    gender — hanya total. Kalau ke depannya Dinas punya data ATS per
 *    gender, ganti seeder ini / input manual lewat form.
 *
 * 3. Penduduk usia jenjang (penduduk_l/p per PAUD/SD/SMP/SMA) = ESTIMASI,
 *    proporsional dari agregat desa REAL (sama seperti PendudukJenjangSeeder
 *    dummy) — data sumber TIDAK memerinci populasi per jenjang, hanya
 *    agregat total per desa. Proporsi memakai konstanta yang sama dengan
 *    PendudukJenjangSeeder supaya konsisten.
 *
 * 4. Desa "Desa Langara Laut" TIDAK punya data agregat penduduk usia
 *    sekolah (NULL di wilayah_desa) walau ATS-nya tetap tercatat riil (SD=1,
 *    SMA=2). Akibatnya penduduk_l/p jenjang untuk desa ini akan ter-isi 0
 *    (bukan null — kolomnya not-nullable), padahal ATS-nya tidak nol.
 *    Persentase ATS otomatis tidak dihitung (accessor mengembalikan null
 *    kalau total penduduk <= 0), tapi mohon dicatat: untuk desa ini, angka
 *    penduduk per jenjang TIDAK BOLEH dibaca sebagai 0 penduduk sungguhan —
 *    itu murni "belum ada data agregatnya".
 *
 * 5. "Desa Langara Tanjung Batu" & "Desa Langkowala" (id 10-11, ditambahkan
 *    di WilayahSeeder pada revisi "DATA HASIL ANALISI RUTE") masuk daftar
 *    ATS_RIIL di bawah ini dengan sumber yang sama persis dgn poin 4 di
 *    atas: 'penduduk_usia_sekolah_l/p' keduanya NULL di wilayah_desa (belum
 *    ada data agregat resmi), sedangkan ATS per jenjangnya SUDAH riil dari
 *    sheet 'Fitur 1 - ATS per Jenjang' (11 desa) pada file Excel yang sama.
 *    Sebelum seeder ini dijalankan ulang, kedua desa ini hanya punya baris
 *    dummy (sumber_data diawali "Dummy") di penduduk_jenjang — persis kondisi
 *    yang seeder ini didesain untuk menimpanya.
 *
 * "Kebutuhan PKBM" (BPB usia 19-24 & 25+) SENGAJA TIDAK dimasukkan di sini —
 * lihat KebutuhanPkbmSeeder & migration create_kebutuhan_pkbm_table untuk
 * alasannya (bukan kohort usia jenjang, jadi disimpan di tabel terpisah).
 *
 * ⚠️ PERILAKU OVERWRITE (berbeda dari PendudukJenjangSeeder dummy):
 * Karena ini DATA RIIL, seeder ini SENGAJA menimpa baris yang sumber_data-
 * nya masih dummy (diawali kata "Dummy") — supaya begitu dijalankan, data
 * dummy proporsional otomatis digantikan oleh ATS riil ini. TAPI seeder ini
 * TIDAK PERNAH menimpa baris yang sumber_data-nya BUKAN dummy (mis. sudah
 * diinput ulang manual oleh admin lewat form Penduduk & ATS per Jenjang) —
 * baris semacam itu dilewati & dicetak sebagai peringatan, supaya admin
 * sadar ada input manual yang berbeda dari data riil versi ini dan bisa
 * mengeceknya sendiri (bukan langsung ditimpa diam-diam).
 */
class PendudukJenjangAtsRiilSeeder extends Seeder
{
    // FIX: kolom sumber_data di tabel penduduk_jenjang cuma varchar(100) —
    // teks penjelasan lengkap (real vs estimasi) ada di komentar class ini,
    // bukan di sini, supaya tidak kena "Data too long for column" saat insert.
    private const SUMBER = 'ATS Riil - Analisis ATS & PKBM Bajo 2026 (total riil; L/P & per-jenjang estimasi)';

    // Sama dengan PendudukJenjangSeeder, supaya konsisten.
    private const PROPORSI_POPULASI = [
        10 => 0.1333, // PAUD
        3  => 0.3667, // SD
        5  => 0.2667, // SMP
        7  => 0.2333, // SMA
    ];

    /**
     * [nama_wilayah => [ats_paud, ats_sd, ats_smp, ats_sma]]
     * jenjang_id: PAUD=10, SD=3, SMP=5, SMA=7
     */
    private const ATS_RIIL = [
        'Desa Langara Indah' => [0, 3, 1, 1],
        'Desa Bajo Indah'    => [0, 4, 6, 6],
        'Desa Bungin Permai' => [0, 23, 15, 5],
        'Desa Saponda Laut'  => [0, 10, 6, 9],
        'Desa Mekar'         => [0, 6, 3, 4],
        'Desa Bajoe'         => [0, 0, 0, 2],
        'Desa Langara Bajo'  => [0, 1, 0, 2],
        'Desa Lappe'         => [0, 3, 2, 5],
        'Desa Langara Laut'  => [0, 1, 0, 2],
        'Desa Langara Tanjung Batu' => [0, 2, 2, 0],
        'Desa Langkowala'    => [0, 1, 1, 3],
    ];

    private const URUTAN_JENJANG = [10, 3, 5, 7]; // selaras urutan array ATS_RIIL

    public function run(): void
    {
        $ditimpa = 0;
        $dibuatBaru = 0;
        $dilewati = [];
        $tidakCocok = [];

        foreach (self::ATS_RIIL as $namaWilayah => $atsPerJenjang) {
            $desa = WilayahDesa::where('nama_wilayah', $namaWilayah)->first();

            if (!$desa) {
                $tidakCocok[] = $namaWilayah;
                continue;
            }

            $aggL = $desa->penduduk_usia_sekolah_l;
            $aggP = $desa->penduduk_usia_sekolah_p;
            $adaAgregat = $aggL !== null && $aggP !== null && ($aggL + $aggP) > 0;
            $rasioL = $adaAgregat ? $aggL / ($aggL + $aggP) : 0.5;

            foreach (self::URUTAN_JENJANG as $i => $jenjangId) {
                $atsTotal = $atsPerJenjang[$i];

                $existing = PendudukJenjang::where('wilayah_id', $desa->id)
                    ->where('jenjang_id', $jenjangId)
                    ->first();

                if ($existing && !str_starts_with((string) $existing->sumber_data, 'Dummy')) {
                    // Baris ini sudah punya sumber non-dummy (kemungkinan input
                    // manual admin) — JANGAN ditimpa otomatis oleh seeder.
                    $dilewati[] = "{$namaWilayah} / jenjang_id {$jenjangId} (sumber saat ini: \"{$existing->sumber_data}\")";
                    continue;
                }

                $pendudukL = $adaAgregat ? (int) round($aggL * self::PROPORSI_POPULASI[$jenjangId]) : 0;
                $pendudukP = $adaAgregat ? (int) round($aggP * self::PROPORSI_POPULASI[$jenjangId]) : 0;

                $putusL = (int) round($atsTotal * $rasioL);
                $putusP = $atsTotal - $putusL;

                PendudukJenjang::updateOrCreate(
                    ['wilayah_id' => $desa->id, 'jenjang_id' => $jenjangId],
                    [
                        'penduduk_l'      => $pendudukL,
                        'penduduk_p'      => $pendudukP,
                        'putus_sekolah_l' => $putusL,
                        'putus_sekolah_p' => $putusP,
                        'sumber_data'     => self::SUMBER,
                    ]
                );

                $existing ? $ditimpa++ : $dibuatBaru++;
            }
        }

        $this->command->info("Sukses! {$dibuatBaru} baris baru dibuat, {$ditimpa} baris dummy ditimpa dengan data ATS riil.");

        if (!empty($dilewati)) {
            $this->command->warn('Baris berikut DILEWATI karena sumber_data-nya sudah bukan dummy (kemungkinan input manual admin) — cek manual apakah perlu disamakan dengan data riil terbaru:');
            foreach ($dilewati as $item) {
                $this->command->warn('  - ' . $item);
            }
        }

        if (!empty($tidakCocok)) {
            $this->command->warn('Nama desa berikut tidak ditemukan di wilayah_desa (cek ejaan nama_wilayah): ' . implode(', ', $tidakCocok));
        }
    }
}