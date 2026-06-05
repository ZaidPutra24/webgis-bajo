<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatistikSekolah;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistikSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel statistik_sekolah terlebih dahulu agar tidak duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        StatistikSekolah::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Seluruh 45 data sekolah lengkap dari file excel Anda tanpa potongan
        $dataStatistik = [
            ['sekolah_id' => 1, 'siswa_l' => 23, 'siswa_p' => 20, 'jumlah_siswa' => 43, 'jumlah_guru' => 5, 'jumlah_rombel' => 6, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 2, 'siswa_l' => 87, 'siswa_p' => 93, 'jumlah_siswa' => 180, 'jumlah_guru' => 12, 'jumlah_rombel' => 9, 'ruang_kelas' => 8, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 4, 'siswa_l' => 76, 'siswa_p' => 75, 'jumlah_siswa' => 151, 'jumlah_guru' => 10, 'jumlah_rombel' => 7, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 5, 'siswa_l' => 41, 'siswa_p' => 34, 'jumlah_siswa' => 75, 'jumlah_guru' => 9, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 6, 'siswa_l' => 0, 'siswa_p' => 0, 'jumlah_siswa' => 0, 'jumlah_guru' => 0, 'jumlah_rombel' => 0, 'ruang_kelas' => 0, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 7, 'siswa_l' => 48, 'siswa_p' => 43, 'jumlah_siswa' => 91, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 8, 'siswa_l' => 42, 'siswa_p' => 27, 'jumlah_siswa' => 69, 'jumlah_guru' => 9, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 9, 'siswa_l' => 36, 'siswa_p' => 25, 'jumlah_siswa' => 61, 'jumlah_guru' => 11, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 10, 'siswa_l' => 42, 'siswa_p' => 52, 'jumlah_siswa' => 94, 'jumlah_guru' => 14, 'jumlah_rombel' => 4, 'ruang_kelas' => 12, 'laboratorium' => 2, 'perpustakaan' => 1],
            ['sekolah_id' => 11, 'siswa_l' => 50, 'siswa_p' => 38, 'jumlah_siswa' => 88, 'jumlah_guru' => 9, 'jumlah_rombel' => 4, 'ruang_kelas' => 5, 'laboratorium' => 2, 'perpustakaan' => 1],
            ['sekolah_id' => 12, 'siswa_l' => 59, 'siswa_p' => 56, 'jumlah_siswa' => 115, 'jumlah_guru' => 14, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 7, 'perpustakaan' => 1],
            ['sekolah_id' => 13, 'siswa_l' => 32, 'siswa_p' => 30, 'jumlah_siswa' => 62, 'jumlah_guru' => 13, 'jumlah_rombel' => 3, 'ruang_kelas' => 3, 'laboratorium' => 1, 'perpustakaan' => 2],
            ['sekolah_id' => 14, 'siswa_l' => 32, 'siswa_p' => 30, 'jumlah_siswa' => 62, 'jumlah_guru' => 13, 'jumlah_rombel' => 3, 'ruang_kelas' => 3, 'laboratorium' => 1, 'perpustakaan' => 2],
            ['sekolah_id' => 15, 'siswa_l' => 34, 'siswa_p' => 37, 'jumlah_siswa' => 71, 'jumlah_guru' => 8, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 16, 'siswa_l' => 107, 'siswa_p' => 100, 'jumlah_siswa' => 207, 'jumlah_guru' => 12, 'jumlah_rombel' => 9, 'ruang_kelas' => 10, 'laboratorium' => 1, 'perpustakaan' => 1],
            ['sekolah_id' => 17, 'siswa_l' => 49, 'siswa_p' => 47, 'jumlah_siswa' => 96, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 18, 'siswa_l' => 28, 'siswa_p' => 28, 'jumlah_siswa' => 56, 'jumlah_guru' => 9, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 19, 'siswa_l' => 40, 'siswa_p' => 44, 'jumlah_siswa' => 84, 'jumlah_guru' => 12, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 20, 'siswa_l' => 37, 'siswa_p' => 48, 'jumlah_siswa' => 85, 'jumlah_guru' => 13, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 21, 'siswa_l' => 28, 'siswa_p' => 28, 'jumlah_siswa' => 56, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 22, 'siswa_l' => 33, 'siswa_p' => 24, 'jumlah_siswa' => 57, 'jumlah_guru' => 11, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 1, 'perpustakaan' => 1],
            ['sekolah_id' => 23, 'siswa_l' => 26, 'siswa_p' => 15, 'jumlah_siswa' => 41, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 24, 'siswa_l' => 37, 'siswa_p' => 41, 'jumlah_siswa' => 78, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 25, 'siswa_l' => 26, 'siswa_p' => 28, 'jumlah_siswa' => 54, 'jumlah_guru' => 9, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 26, 'siswa_l' => 44, 'siswa_p' => 24, 'jumlah_siswa' => 68, 'jumlah_guru' => 9, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 27, 'siswa_l' => 53, 'siswa_p' => 49, 'jumlah_siswa' => 102, 'jumlah_guru' => 9, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 28, 'siswa_l' => 33, 'siswa_p' => 31, 'jumlah_siswa' => 64, 'jumlah_guru' => 9, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 29, 'siswa_l' => 44, 'siswa_p' => 42, 'jumlah_siswa' => 86, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 30, 'siswa_l' => 32, 'siswa_p' => 30, 'jumlah_siswa' => 62, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 31, 'siswa_l' => 39, 'siswa_p' => 37, 'jumlah_siswa' => 76, 'jumlah_guru' => 9, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 32, 'siswa_l' => 61, 'siswa_p' => 48, 'jumlah_siswa' => 109, 'jumlah_guru' => 11, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 33, 'siswa_l' => 59, 'siswa_p' => 58, 'jumlah_siswa' => 117, 'jumlah_guru' => 11, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 34, 'siswa_l' => 39, 'siswa_p' => 33, 'jumlah_siswa' => 72, 'jumlah_guru' => 9, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 35, 'siswa_l' => 61, 'siswa_p' => 67, 'jumlah_siswa' => 128, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 36, 'siswa_l' => 68, 'siswa_p' => 64, 'jumlah_siswa' => 132, 'jumlah_guru' => 12, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 37, 'siswa_l' => 60, 'siswa_p' => 56, 'jumlah_siswa' => 116, 'jumlah_guru' => 14, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 38, 'siswa_l' => 55, 'siswa_p' => 53, 'jumlah_siswa' => 108, 'jumlah_guru' => 14, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 2, 'perpustakaan' => 1],
            ['sekolah_id' => 39, 'siswa_l' => 77, 'siswa_p' => 74, 'jumlah_siswa' => 151, 'jumlah_guru' => 13, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 1, 'perpustakaan' => 1],
            ['sekolah_id' => 40, 'siswa_l' => 45, 'siswa_p' => 50, 'jumlah_siswa' => 95, 'jumlah_guru' => 13, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 2, 'perpustakaan' => 1],
            ['sekolah_id' => 41, 'siswa_l' => 39, 'siswa_p' => 45, 'jumlah_siswa' => 84, 'jumlah_guru' => 12, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 1, 'perpustakaan' => 1],
            ['sekolah_id' => 42, 'siswa_l' => 37, 'siswa_p' => 31, 'jumlah_siswa' => 68, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 43, 'siswa_l' => 35, 'siswa_p' => 25, 'jumlah_siswa' => 60, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 44, 'siswa_l' => 114, 'siswa_p' => 96, 'jumlah_siswa' => 210, 'jumlah_guru' => 13, 'jumlah_rombel' => 8, 'ruang_kelas' => 8, 'laboratorium' => 7, 'perpustakaan' => 3],
            ['sekolah_id' => 45, 'siswa_l' => 167, 'siswa_p' => 171, 'jumlah_siswa' => 338, 'jumlah_guru' => 26, 'jumlah_rombel' => 12, 'ruang_kelas' => 19, 'laboratorium' => 2, 'perpustakaan' => 1],
            ['sekolah_id' => 46, 'siswa_l' => 61, 'siswa_p' => 56, 'jumlah_siswa' => 117, 'jumlah_guru' => 7, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 2],
        ];

        // 3. Simpan data masal dan sematkan timestamps real-time
        $now = Carbon::now();
        foreach ($dataStatistik as $item) {
            StatistikSekolah::create(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now
            ]));
        }

        $this->command->info('Sukses Mutlak! Seluruh 45 data statistik sekolah tanpa terkecuali telah berhasil disuntikkan ke database.');
    }
}