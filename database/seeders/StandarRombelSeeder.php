<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Standar maksimum murid per rombel per jenjang.
 *
 * Sumber: Keputusan Kementerian Dikdasmen No. 14 Tahun 2026, sesuai angka
 * yang diberikan pengguna. CATATAN: belum diverifikasi ulang secara
 * independen (tidak ada akses browsing saat data ini dimasukkan) — karena
 * itu disimpan di tabel referensi yang bisa diedit admin, bukan hardcode.
 * Simpan dokumen aslinya sebagai arsip dasar hukum di sistem.
 */
class StandarRombelSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('standar_rombel')->truncate();

        $dasarHukum = 'Kepmendikdasmen No. 14 Tahun 2026';
        $catatan = 'Data dimasukkan sesuai keterangan pengguna, belum diverifikasi ulang secara independen. Mohon lampirkan dokumen aslinya sebagai arsip.';

// jenjang_id sesuai JenjangSeeder: PAUD=10, SD=3, SMP=5, SMA=7.
// CATATAN (koreksi): baris ini sebelumnya menyebut "jenjang_id 16/17/18
// untuk Paket A/B/C" — itu rencana lama yang TIDAK JADI dipakai.
// Standar rombel Paket A/B/C sekarang ada di tabel terpisah
// `standar_rombel_paket` — lihat StandarRombelPaketSeeder.
        $data = [
            ['jenjang_id' => 10, 'maks_murid_per_rombel' => 15],
            ['jenjang_id' => 3,  'maks_murid_per_rombel' => 28],
            ['jenjang_id' => 5,  'maks_murid_per_rombel' => 32],
            ['jenjang_id' => 7,  'maks_murid_per_rombel' => 36],
        ];

        foreach ($data as $row) {
            DB::table('standar_rombel')->insert(array_merge($row, [
                'dasar_hukum'   => $dasarHukum,
                'nomor_dokumen' => null,
                'keterangan'    => $catatan,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]));
        }

        $this->command->info('Sukses! Standar rombel 4 jenjang ...');
    }
}
