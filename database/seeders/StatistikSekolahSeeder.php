<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatistikSekolah;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistikSekolahSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        StatistikSekolah::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

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
            ['sekolah_id' => 17, 'siswa_l' => 153, 'siswa_p' => 176, 'jumlah_siswa' => 329, 'jumlah_guru' => 14, 'jumlah_rombel' => 12, 'ruang_kelas' => 12, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 18, 'siswa_l' => 50, 'siswa_p' => 46, 'jumlah_siswa' => 96, 'jumlah_guru' => 7, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 19, 'siswa_l' => 63, 'siswa_p' => 60, 'jumlah_siswa' => 123, 'jumlah_guru' => 9, 'jumlah_rombel' => 7, 'ruang_kelas' => 6, 'laboratorium' => 1, 'perpustakaan' => 2],
            ['sekolah_id' => 20, 'siswa_l' => 81, 'siswa_p' => 77, 'jumlah_siswa' => 158, 'jumlah_guru' => 9, 'jumlah_rombel' => 7, 'ruang_kelas' => 8, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 21, 'siswa_l' => 118, 'siswa_p' => 103, 'jumlah_siswa' => 221, 'jumlah_guru' => 10, 'jumlah_rombel' => 10, 'ruang_kelas' => 8, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 22, 'siswa_l' => 86, 'siswa_p' => 84, 'jumlah_siswa' => 170, 'jumlah_guru' => 9, 'jumlah_rombel' => 7, 'ruang_kelas' => 7, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 23, 'siswa_l' => 102, 'siswa_p' => 110, 'jumlah_siswa' => 212, 'jumlah_guru' => 8, 'jumlah_rombel' => 7, 'ruang_kelas' => 10, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 24, 'siswa_l' => 77, 'siswa_p' => 72, 'jumlah_siswa' => 149, 'jumlah_guru' => 7, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 25, 'siswa_l' => 58, 'siswa_p' => 68, 'jumlah_siswa' => 126, 'jumlah_guru' => 8, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 26, 'siswa_l' => 39, 'siswa_p' => 48, 'jumlah_siswa' => 87, 'jumlah_guru' => 8, 'jumlah_rombel' => 4, 'ruang_kelas' => 4, 'laboratorium' => 1, 'perpustakaan' => 0],
            ['sekolah_id' => 27, 'siswa_l' => 21, 'siswa_p' => 10, 'jumlah_siswa' => 31, 'jumlah_guru' => 5, 'jumlah_rombel' => 3, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 28, 'siswa_l' => 0, 'siswa_p' => 0, 'jumlah_siswa' => 0, 'jumlah_guru' => 0, 'jumlah_rombel' => 0, 'ruang_kelas' => 0, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 29, 'siswa_l' => 75, 'siswa_p' => 114, 'jumlah_siswa' => 189, 'jumlah_guru' => 15, 'jumlah_rombel' => 7, 'ruang_kelas' => 8, 'laboratorium' => 7, 'perpustakaan' => 2],
            ['sekolah_id' => 30, 'siswa_l' => 337, 'siswa_p' => 347, 'jumlah_siswa' => 684, 'jumlah_guru' => 41, 'jumlah_rombel' => 21, 'ruang_kelas' => 22, 'laboratorium' => 7, 'perpustakaan' => 1],
            ['sekolah_id' => 31, 'siswa_l' => 70, 'siswa_p' => 73, 'jumlah_siswa' => 143, 'jumlah_guru' => 10, 'jumlah_rombel' => 7, 'ruang_kelas' => 9, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 32, 'siswa_l' => 142, 'siswa_p' => 153, 'jumlah_siswa' => 295, 'jumlah_guru' => 18, 'jumlah_rombel' => 12, 'ruang_kelas' => 12, 'laboratorium' => 1, 'perpustakaan' => 2],
            ['sekolah_id' => 33, 'siswa_l' => 64, 'siswa_p' => 65, 'jumlah_siswa' => 129, 'jumlah_guru' => 8, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 34, 'siswa_l' => 89, 'siswa_p' => 89, 'jumlah_siswa' => 178, 'jumlah_guru' => 10, 'jumlah_rombel' => 7, 'ruang_kelas' => 7, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 35, 'siswa_l' => 57, 'siswa_p' => 52, 'jumlah_siswa' => 109, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 36, 'siswa_l' => 40, 'siswa_p' => 31, 'jumlah_siswa' => 71, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 37, 'siswa_l' => 50, 'siswa_p' => 51, 'jumlah_siswa' => 101, 'jumlah_guru' => 8, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 1, 'perpustakaan' => 1],
            ['sekolah_id' => 38, 'siswa_l' => 41, 'siswa_p' => 27, 'jumlah_siswa' => 68, 'jumlah_guru' => 11, 'jumlah_rombel' => 3, 'ruang_kelas' => 7, 'laboratorium' => 2, 'perpustakaan' => 1],
            ['sekolah_id' => 39, 'siswa_l' => 17, 'siswa_p' => 20, 'jumlah_siswa' => 37, 'jumlah_guru' => 14, 'jumlah_rombel' => 3, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 40, 'siswa_l' => 21, 'siswa_p' => 9, 'jumlah_siswa' => 30, 'jumlah_guru' => 10, 'jumlah_rombel' => 3, 'ruang_kelas' => 3, 'laboratorium' => 1, 'perpustakaan' => 0],
            ['sekolah_id' => 41, 'siswa_l' => 207, 'siswa_p' => 207, 'jumlah_siswa' => 414, 'jumlah_guru' => 28, 'jumlah_rombel' => 13, 'ruang_kelas' => 16, 'laboratorium' => 8, 'perpustakaan' => 2],
            ['sekolah_id' => 42, 'siswa_l' => 37, 'siswa_p' => 31, 'jumlah_siswa' => 68, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 43, 'siswa_l' => 35, 'siswa_p' => 25, 'jumlah_siswa' => 60, 'jumlah_guru' => 10, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 44, 'siswa_l' => 114, 'siswa_p' => 96, 'jumlah_siswa' => 210, 'jumlah_guru' => 13, 'jumlah_rombel' => 8, 'ruang_kelas' => 8, 'laboratorium' => 7, 'perpustakaan' => 3],
            ['sekolah_id' => 45, 'siswa_l' => 167, 'siswa_p' => 171, 'jumlah_siswa' => 338, 'jumlah_guru' => 26, 'jumlah_rombel' => 12, 'ruang_kelas' => 19, 'laboratorium' => 2, 'perpustakaan' => 1],
            ['sekolah_id' => 46, 'siswa_l' => 61, 'siswa_p' => 56, 'jumlah_siswa' => 117, 'jumlah_guru' => 7, 'jumlah_rombel' => 6, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 2],
            ['sekolah_id' => 47, 'siswa_l' => 0, 'siswa_p' => 0, 'jumlah_siswa' => 0, 'jumlah_guru' => 0, 'jumlah_rombel' => 0, 'ruang_kelas' => 0, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 48, 'siswa_l' => 0, 'siswa_p' => 0, 'jumlah_siswa' => 0, 'jumlah_guru' => 0, 'jumlah_rombel' => 0, 'ruang_kelas' => 0, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 49, 'siswa_l' => 33, 'siswa_p' => 40, 'jumlah_siswa' => 73, 'jumlah_guru' => 18, 'jumlah_rombel' => 9, 'ruang_kelas' => 1, 'laboratorium' => 1, 'perpustakaan' => 0],
            ['sekolah_id' => 50, 'siswa_l' => 18, 'siswa_p' => 19, 'jumlah_siswa' => 27, 'jumlah_guru' => 0, 'jumlah_rombel' => 3, 'ruang_kelas' => 4, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 51, 'siswa_l' => 24, 'siswa_p' => 18, 'jumlah_siswa' => 42, 'jumlah_guru' => 0, 'jumlah_rombel' => 2, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 52, 'siswa_l' => 99, 'siswa_p' => 41, 'jumlah_siswa' => 140, 'jumlah_guru' => 0, 'jumlah_rombel' => 7, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 53, 'siswa_l' => 47, 'siswa_p' => 24, 'jumlah_siswa' => 71, 'jumlah_guru' => 6, 'jumlah_rombel' => 3, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 54, 'siswa_l' => 69, 'siswa_p' => 37, 'jumlah_siswa' => 106, 'jumlah_guru' => 1, 'jumlah_rombel' => 8, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 55, 'siswa_l' => 43, 'siswa_p' => 23, 'jumlah_siswa' => 66, 'jumlah_guru' => 6, 'jumlah_rombel' => 5, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 1],
            ['sekolah_id' => 56, 'siswa_l' => 47, 'siswa_p' => 52, 'jumlah_siswa' => 99, 'jumlah_guru' => 6, 'jumlah_rombel' => 7, 'ruang_kelas' => 8, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 57, 'siswa_l' => 11, 'siswa_p' => 16, 'jumlah_siswa' => 27, 'jumlah_guru' => 1, 'jumlah_rombel' => 2, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 58, 'siswa_l' => 10, 'siswa_p' => 8, 'jumlah_siswa' => 18, 'jumlah_guru' => 2, 'jumlah_rombel' => 2, 'ruang_kelas' => 1, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 59, 'siswa_l' => 30, 'siswa_p' => 26, 'jumlah_siswa' => 56, 'jumlah_guru' => 3, 'jumlah_rombel' => 4, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 60, 'siswa_l' => 7, 'siswa_p' => 12, 'jumlah_siswa' => 19, 'jumlah_guru' => 1, 'jumlah_rombel' => 2, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 61, 'siswa_l' => 9, 'siswa_p' => 15, 'jumlah_siswa' => 24, 'jumlah_guru' => 2, 'jumlah_rombel' => 2, 'ruang_kelas' => 1, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 62, 'siswa_l' => 16, 'siswa_p' => 9, 'jumlah_siswa' => 25, 'jumlah_guru' => 2, 'jumlah_rombel' => 2, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 63, 'siswa_l' => 5, 'siswa_p' => 10, 'jumlah_siswa' => 15, 'jumlah_guru' => 2, 'jumlah_rombel' => 2, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 64, 'siswa_l' => 14, 'siswa_p' => 13, 'jumlah_siswa' => 27, 'jumlah_guru' => 0, 'jumlah_rombel' => 2, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 65, 'siswa_l' => 5, 'siswa_p' => 9, 'jumlah_siswa' => 14, 'jumlah_guru' => 2, 'jumlah_rombel' => 2, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 66, 'siswa_l' => 34, 'siswa_p' => 24, 'jumlah_siswa' => 58, 'jumlah_guru' => 5, 'jumlah_rombel' => 4, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 67, 'siswa_l' => 14, 'siswa_p' => 22, 'jumlah_siswa' => 36, 'jumlah_guru' => 2, 'jumlah_rombel' => 2, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 68, 'siswa_l' => 8, 'siswa_p' => 8, 'jumlah_siswa' => 16, 'jumlah_guru' => 1, 'jumlah_rombel' => 1, 'ruang_kelas' => 1, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 69, 'siswa_l' => 0, 'siswa_p' => 0, 'jumlah_siswa' => 0, 'jumlah_guru' => 0, 'jumlah_rombel' => 0, 'ruang_kelas' => 0, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 70, 'siswa_l' => 0, 'siswa_p' => 0, 'jumlah_siswa' => 0, 'jumlah_guru' => 0, 'jumlah_rombel' => 0, 'ruang_kelas' => 0, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 71, 'siswa_l' => 0, 'siswa_p' => 0, 'jumlah_siswa' => 0, 'jumlah_guru' => 0, 'jumlah_rombel' => 0, 'ruang_kelas' => 0, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 72, 'siswa_l' => 0, 'siswa_p' => 0, 'jumlah_siswa' => 0, 'jumlah_guru' => 0, 'jumlah_rombel' => 0, 'ruang_kelas' => 0, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 73, 'siswa_l' => 0, 'siswa_p' => 0, 'jumlah_siswa' => 0, 'jumlah_guru' => 0, 'jumlah_rombel' => 0, 'ruang_kelas' => 0, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 74, 'siswa_l' => 0, 'siswa_p' => 0, 'jumlah_siswa' => 0, 'jumlah_guru' => 0, 'jumlah_rombel' => 0, 'ruang_kelas' => 0, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 75, 'siswa_l' => 12, 'siswa_p' => 11, 'jumlah_siswa' => 23, 'jumlah_guru' => 2, 'jumlah_rombel' => 2, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 76, 'siswa_l' => 32, 'siswa_p' => 30, 'jumlah_siswa' => 62, 'jumlah_guru' => 4, 'jumlah_rombel' => 5, 'ruang_kelas' => 4, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 77, 'siswa_l' => 14, 'siswa_p' => 22, 'jumlah_siswa' => 36, 'jumlah_guru' => 2, 'jumlah_rombel' => 3, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 78, 'siswa_l' => 2, 'siswa_p' => 3, 'jumlah_siswa' => 5, 'jumlah_guru' => 0, 'jumlah_rombel' => 2, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 79, 'siswa_l' => 7, 'siswa_p' => 6, 'jumlah_siswa' => 13, 'jumlah_guru' => 1, 'jumlah_rombel' => 2, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 80, 'siswa_l' => 26, 'siswa_p' => 20, 'jumlah_siswa' => 46, 'jumlah_guru' => 4, 'jumlah_rombel' => 4, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 81, 'siswa_l' => 0, 'siswa_p' => 0, 'jumlah_siswa' => 0, 'jumlah_guru' => 0, 'jumlah_rombel' => 0, 'ruang_kelas' => 0, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 82, 'siswa_l' => 6, 'siswa_p' => 11, 'jumlah_siswa' => 17, 'jumlah_guru' => 1, 'jumlah_rombel' => 1, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 83, 'siswa_l' => 23, 'siswa_p' => 35, 'jumlah_siswa' => 58, 'jumlah_guru' => 3, 'jumlah_rombel' => 4, 'ruang_kelas' => 4, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 84, 'siswa_l' => 14, 'siswa_p' => 13, 'jumlah_siswa' => 17, 'jumlah_guru' => 2, 'jumlah_rombel' => 2, 'ruang_kelas' => 2, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 85, 'siswa_l' => 20, 'siswa_p' => 21, 'jumlah_siswa' => 41, 'jumlah_guru' => 3, 'jumlah_rombel' => 3, 'ruang_kelas' => 3, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 86, 'siswa_l' => 45, 'siswa_p' => 48, 'jumlah_siswa' => 93, 'jumlah_guru' => 5, 'jumlah_rombel' => 5, 'ruang_kelas' => 6, 'laboratorium' => 0, 'perpustakaan' => 0],
            ['sekolah_id' => 87, 'siswa_l' => 9, 'siswa_p' => 12, 'jumlah_siswa' => 21, 'jumlah_guru' => 2, 'jumlah_rombel' => 2, 'ruang_kelas' => 1, 'laboratorium' => 0, 'perpustakaan' => 0],
        ];

        $now = Carbon::now();
        foreach ($dataStatistik as $item) {
            StatistikSekolah::create(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now
            ]));
        }

        $this->command->info('Sukses! 86 data statistik sekolah berhasil dimasukkan.');
    }
}