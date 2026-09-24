<?php

namespace Database\Seeders;

use App\Models\KebutuhanPkbm;
use App\Models\WilayahDesa;
use Illuminate\Database\Seeder;

/**
 * DATA RIIL Kebutuhan PKBM (BPB usia 19-24 & 25+) per desa, dari
 * "Analisis_ATS_PKBM_Wilayah_Bajo.xlsx" (sheet 'Fitur 1 - ATS per Jenjang',
 * kolom "BPB Usia 19-24" & "BPB Usia 25+"). Data ini SUDAH real dari hasil
 * analisis Dinas — bukan dummy/estimasi.
 *
 * Nama desa di sini adalah string PERSIS sama dengan `nama_wilayah` di
 * WilayahSeeder (sudah dicek cocok 1:1 untuk 11 desa wilayah Bajo, termasuk
 * 2 desa tambahan "Desa Langara Tanjung Batu" & "Desa Langkowala" / id
 * 10-11 dari revisi "DATA HASIL ANALISI RUTE").
 *
 * ⚠️ IDEMPOTENT: hanya mengisi desa yang BELUM punya baris di kebutuhan_pkbm
 * sama sekali. Tidak pernah truncate/menimpa — begitu ada data resmi lebih
 * baru dari Dinas, update lewat proses yang menjaga baris existing (mis.
 * form admin, kalau nanti dibuat) alih-alih menjalankan ulang seeder ini.
 */
class KebutuhanPkbmSeeder extends Seeder
{
    private const SUMBER = 'ATS Riil - Analisis ATS & PKBM Wilayah Bajo 2026 (BPB usia 19-24 & 25+)';

    /**
     * [nama_wilayah => [bpb_19_24, bpb_25_plus]]
     */
    private const DATA = [
        'Desa Langara Indah' => [2, 0],
        'Desa Bajo Indah'    => [6, 0],
        'Desa Bungin Permai' => [3, 0],
        'Desa Saponda Laut'  => [8, 0],
        'Desa Mekar'         => [7, 0],
        'Desa Bajoe'         => [4, 0],
        'Desa Langara Bajo'  => [4, 0],
        'Desa Lappe'         => [7, 0],
        'Desa Langara Laut'  => [2, 0],
        'Desa Langara Tanjung Batu' => [3, 0],
        'Desa Langkowala'    => [2, 0],
    ];

    public function run(): void
    {
        $sudahAda = KebutuhanPkbm::pluck('wilayah_id')->flip();

        $dimasukkan = 0;
        $tidakCocok = [];

        foreach (self::DATA as $namaWilayah => [$bpb1924, $bpb25plus]) {
            $desa = WilayahDesa::where('nama_wilayah', $namaWilayah)->first();

            if (!$desa) {
                $tidakCocok[] = $namaWilayah;
                continue;
            }

            if (isset($sudahAda[$desa->id])) {
                // Sudah ada baris untuk desa ini — jangan ditimpa.
                continue;
            }

            KebutuhanPkbm::create([
                'wilayah_id'  => $desa->id,
                'bpb_19_24'   => $bpb1924,
                'bpb_25_plus' => $bpb25plus,
                'sumber_data' => self::SUMBER,
            ]);
            $dimasukkan++;
        }

        $this->command->info("Sukses! {$dimasukkan} desa diisi data Kebutuhan PKBM riil.");

        if (!empty($tidakCocok)) {
            $this->command->warn('Nama desa berikut tidak ditemukan di wilayah_desa (cek ejaan nama_wilayah): ' . implode(', ', $tidakCocok));
        }
    }
}