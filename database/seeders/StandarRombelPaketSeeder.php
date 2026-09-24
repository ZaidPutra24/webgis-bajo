<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Standar maksimum murid per rombel untuk Paket A/B/C (kesetaraan di PKBM).
 * Sumber: sesuai keterangan pengguna — Paket A 20, Paket B 25, Paket C 30
 * murid/rombel. Sama seperti StandarRombelSeeder (jenjang formal): belum
 * diverifikasi ulang secara independen, disimpan di tabel yang bisa diedit
 * admin, bukan hardcode di kode.
 */
class StandarRombelPaketSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('standar_rombel_paket')->truncate();

        $catatan = 'Data dimasukkan sesuai keterangan pengguna, belum diverifikasi ulang secara independen. Mohon lampirkan dokumen aslinya sebagai arsip.';

        $data = [
            ['paket' => 'A', 'maks_murid_per_rombel' => 20],
            ['paket' => 'B', 'maks_murid_per_rombel' => 25],
            ['paket' => 'C', 'maks_murid_per_rombel' => 30],
        ];

        foreach ($data as $row) {
            DB::table('standar_rombel_paket')->insert(array_merge($row, [
                'dasar_hukum'   => null,
                'nomor_dokumen' => null,
                'keterangan'    => $catatan,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]));
        }

        $this->command->info('Sukses! Standar rombel Paket A/B/C berhasil dimasukkan.');
    }
}
