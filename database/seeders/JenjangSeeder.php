<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JenjangSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('jenjang')->truncate();
        Schema::enableForeignKeyConstraints();

        $jenjang = [
            ['id' => 1, 'kode' => 'TK', 'nama_jenjang' => 'Taman Kanak-Kanak', 'jenis_pendidikan' => 'Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'kode' => 'RA', 'nama_jenjang' => 'Raudhatul Athfal', 'jenis_pendidikan' => 'Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'kode' => 'SD', 'nama_jenjang' => 'Sekolah Dasar', 'jenis_pendidikan' => 'Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'kode' => 'MI', 'nama_jenjang' => 'Madrasah Ibtidaiyah', 'jenis_pendidikan' => 'Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'kode' => 'SMP', 'nama_jenjang' => 'Sekolah Menengah Pertama', 'jenis_pendidikan' => 'Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'kode' => 'MTS', 'nama_jenjang' => 'Madrasah Tsanawiyah', 'jenis_pendidikan' => 'Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'kode' => 'SMA', 'nama_jenjang' => 'Sekolah Menengah Atas', 'jenis_pendidikan' => 'Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'kode' => 'MA', 'nama_jenjang' => 'Madrasah Aliyah', 'jenis_pendidikan' => 'Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'kode' => 'SMK', 'nama_jenjang' => 'Sekolah Menengah Kejuruan', 'jenis_pendidikan' => 'Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'kode' => 'PAUD', 'nama_jenjang' => 'Pendidikan Anak Usia Dini', 'jenis_pendidikan' => 'Anak Usia Dini', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'kode' => 'KB', 'nama_jenjang' => 'Kelompok Belajar', 'jenis_pendidikan' => 'Non-Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'kode' => 'PKBM', 'nama_jenjang' => 'Pusat Kegiatan Belajar Masyarakat', 'jenis_pendidikan' => 'Non-Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'kode' => 'SKB', 'nama_jenjang' => 'Sanggar Kegiatan Belajar', 'jenis_pendidikan' => 'Non-Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'kode' => 'LKP', 'nama_jenjang' => 'Lembaga Kursus dan Pelatihan', 'jenis_pendidikan' => 'Non-Formal', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'kode' => 'TBM', 'nama_jenjang' => 'Taman Baca Masyarakat', 'jenis_pendidikan' => 'Non-Formal', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('jenjang')->insert($jenjang);

        $this->command->info('Sukses! 15 data jenjang berhasil dimasukkan.');
    }
}