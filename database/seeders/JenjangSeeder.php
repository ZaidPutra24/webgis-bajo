<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenjangSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel dulu agar idempotent — aman dijalankan berulang kali
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('jenjang')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $jenjang = [
            ['kode' => 'SD',  'nama_jenjang' => 'Sekolah Dasar',              'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'MI',  'nama_jenjang' => 'Madrasah Ibtidaiyah',         'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'SMP', 'nama_jenjang' => 'Sekolah Menengah Pertama',    'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'MTS', 'nama_jenjang' => 'Madrasah Tsanawiyah',         'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'SMA', 'nama_jenjang' => 'Sekolah Menengah Atas',       'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'MA',  'nama_jenjang' => 'Madrasah Aliyah',             'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'SMK', 'nama_jenjang' => 'Sekolah Menengah Kejuruan',   'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('jenjang')->insert($jenjang);

        $this->command->info('Sukses! 7 data jenjang berhasil dimasukkan.');
    }
}
