<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JarakSekolahLokasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JarakSekolahLokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Bersihkan tabel sebelum injeksi data agar terhindar dari duplikasi / primary key crash
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        JarakSekolahLokasi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Definisi Array 125 Data Matriks Jarak Sekolah ke Wilayah Desa Secara Lengkap
        $dataJarak = [
            ['sekolah_id' => 2, 'wilayah_id' => 1, 'jarak' => 1.0],
            ['sekolah_id' => 2, 'wilayah_id' => 7, 'jarak' => 0.11],
            ['sekolah_id' => 2, 'wilayah_id' => 8, 'jarak' => 1.28],
            ['sekolah_id' => 2, 'wilayah_id' => 2, 'jarak' => 2.03],
            ['sekolah_id' => 2, 'wilayah_id' => 9, 'jarak' => 17.58],
            ['sekolah_id' => 18, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 31, 'wilayah_id' => 4, 'jarak' => 1.44],
            ['sekolah_id' => 31, 'wilayah_id' => 5, 'jarak' => 1.22],
            ['sekolah_id' => 31, 'wilayah_id' => 6, 'jarak' => 1.07],
            ['sekolah_id' => 4, 'wilayah_id' => 1, 'jarak' => 0.7],
            ['sekolah_id' => 4, 'wilayah_id' => 7, 'jarak' => 1.32],
            ['sekolah_id' => 4, 'wilayah_id' => 8, 'jarak' => 0.01],
            ['sekolah_id' => 4, 'wilayah_id' => 2, 'jarak' => 0.74],
            ['sekolah_id' => 4, 'wilayah_id' => 9, 'jarak' => 17.58],
            ['sekolah_id' => 19, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 32, 'wilayah_id' => 4, 'jarak' => 1.12],
            ['sekolah_id' => 32, 'wilayah_id' => 5, 'jarak' => 0.53],
            ['sekolah_id' => 32, 'wilayah_id' => 6, 'jarak' => 0.87],
            ['sekolah_id' => 5, 'wilayah_id' => 1, 'jarak' => 2.04],
            ['sekolah_id' => 5, 'wilayah_id' => 7, 'jarak' => 2.67],
            ['sekolah_id' => 5, 'wilayah_id' => 8, 'jarak' => 1.36],
            ['sekolah_id' => 5, 'wilayah_id' => 2, 'jarak' => 0.72],
            ['sekolah_id' => 5, 'wilayah_id' => 9, 'jarak' => 17.58],
            ['sekolah_id' => 20, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 33, 'wilayah_id' => 4, 'jarak' => 0.99],
            ['sekolah_id' => 33, 'wilayah_id' => 5, 'jarak' => 0.78],
            ['sekolah_id' => 33, 'wilayah_id' => 6, 'jarak' => 0.63],
            ['sekolah_id' => 6, 'wilayah_id' => 1, 'jarak' => 1.39],
            ['sekolah_id' => 6, 'wilayah_id' => 7, 'jarak' => 2.01],
            ['sekolah_id' => 6, 'wilayah_id' => 8, 'jarak' => 0.7],
            ['sekolah_id' => 6, 'wilayah_id' => 2, 'jarak' => 0.19],
            ['sekolah_id' => 6, 'wilayah_id' => 9, 'jarak' => 17.57],
            ['sekolah_id' => 46, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 34, 'wilayah_id' => 4, 'jarak' => 1.87],
            ['sekolah_id' => 34, 'wilayah_id' => 5, 'jarak' => 1.66],
            ['sekolah_id' => 34, 'wilayah_id' => 6, 'jarak' => 1.5],
            ['sekolah_id' => 7, 'wilayah_id' => 1, 'jarak' => 3.64],
            ['sekolah_id' => 7, 'wilayah_id' => 7, 'jarak' => 2.74],
            ['sekolah_id' => 7, 'wilayah_id' => 8, 'jarak' => 3.65],
            ['sekolah_id' => 7, 'wilayah_id' => 2, 'jarak' => 4.4],
            ['sekolah_id' => 7, 'wilayah_id' => 9, 'jarak' => 17.57],
            ['sekolah_id' => 21, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 35, 'wilayah_id' => 4, 'jarak' => 0.85],
            ['sekolah_id' => 35, 'wilayah_id' => 5, 'jarak' => 0.64],
            ['sekolah_id' => 35, 'wilayah_id' => 6, 'jarak' => 0.48],
            ['sekolah_id' => 1, 'wilayah_id' => 1, 'jarak' => 2.6],
            ['sekolah_id' => 1, 'wilayah_id' => 7, 'jarak' => 1.7],
            ['sekolah_id' => 1, 'wilayah_id' => 8, 'jarak' => 2.87],
            ['sekolah_id' => 1, 'wilayah_id' => 2, 'jarak' => 3.63],
            ['sekolah_id' => 1, 'wilayah_id' => 9, 'jarak' => 17.57],
            ['sekolah_id' => 17, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 36, 'wilayah_id' => 4, 'jarak' => 2.42],
            ['sekolah_id' => 36, 'wilayah_id' => 5, 'jarak' => 2.21],
            ['sekolah_id' => 36, 'wilayah_id' => 6, 'jarak' => 2.05],
            ['sekolah_id' => 42, 'wilayah_id' => 1, 'jarak' => 6.54],
            ['sekolah_id' => 42, 'wilayah_id' => 7, 'jarak' => 7.17],
            ['sekolah_id' => 42, 'wilayah_id' => 8, 'jarak' => 5.86],
            ['sekolah_id' => 42, 'wilayah_id' => 2, 'jarak' => 5.22],
            ['sekolah_id' => 42, 'wilayah_id' => 9, 'jarak' => 17.57],
            ['sekolah_id' => 22, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 37, 'wilayah_id' => 4, 'jarak' => 2.52],
            ['sekolah_id' => 37, 'wilayah_id' => 5, 'jarak' => 2.3],
            ['sekolah_id' => 37, 'wilayah_id' => 6, 'jarak' => 2.15],
            ['sekolah_id' => 8, 'wilayah_id' => 1, 'jarak' => 4.61],
            ['sekolah_id' => 8, 'wilayah_id' => 7, 'jarak' => 5.23],
            ['sekolah_id' => 8, 'wilayah_id' => 8, 'jarak' => 3.92],
            ['sekolah_id' => 8, 'wilayah_id' => 2, 'jarak' => 3.28],
            ['sekolah_id' => 8, 'wilayah_id' => 9, 'jarak' => 17.57],
            ['sekolah_id' => 23, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 45, 'wilayah_id' => 4, 'jarak' => 0.88],
            ['sekolah_id' => 45, 'wilayah_id' => 5, 'jarak' => 0.67],
            ['sekolah_id' => 45, 'wilayah_id' => 6, 'jarak' => 0.51],
            ['sekolah_id' => 43, 'wilayah_id' => 1, 'jarak' => 4.53],
            ['sekolah_id' => 43, 'wilayah_id' => 7, 'jarak' => 5.15],
            ['sekolah_id' => 43, 'wilayah_id' => 8, 'jarak' => 3.84],
            ['sekolah_id' => 43, 'wilayah_id' => 2, 'jarak' => 3.2],
            ['sekolah_id' => 43, 'wilayah_id' => 9, 'jarak' => 17.57],
            ['sekolah_id' => 25, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 38, 'wilayah_id' => 4, 'jarak' => 3.78],
            ['sekolah_id' => 38, 'wilayah_id' => 5, 'jarak' => 3.57],
            ['sekolah_id' => 38, 'wilayah_id' => 6, 'jarak' => 3.42],
            ['sekolah_id' => 15, 'wilayah_id' => 1, 'jarak' => 11.91],
            ['sekolah_id' => 15, 'wilayah_id' => 7, 'jarak' => 12.54],
            ['sekolah_id' => 15, 'wilayah_id' => 8, 'jarak' => 11.23],
            ['sekolah_id' => 15, 'wilayah_id' => 2, 'jarak' => 10.59],
            ['sekolah_id' => 15, 'wilayah_id' => 9, 'jarak' => 17.57],
            ['sekolah_id' => 24, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 39, 'wilayah_id' => 4, 'jarak' => 7.19],
            ['sekolah_id' => 39, 'wilayah_id' => 5, 'jarak' => 6.6],
            ['sekolah_id' => 39, 'wilayah_id' => 6, 'jarak' => 6.94],
            ['sekolah_id' => 9, 'wilayah_id' => 1, 'jarak' => 14.9],
            ['sekolah_id' => 9, 'wilayah_id' => 7, 'jarak' => 15.53],
            ['sekolah_id' => 9, 'wilayah_id' => 8, 'jarak' => 14.22],
            ['sekolah_id' => 9, 'wilayah_id' => 2, 'jarak' => 13.58],
            ['sekolah_id' => 9, 'wilayah_id' => 9, 'jarak' => 17.57],
            ['sekolah_id' => 30, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 40, 'wilayah_id' => 4, 'jarak' => 4.91],
            ['sekolah_id' => 40, 'wilayah_id' => 5, 'jarak' => 4.7],
            ['sekolah_id' => 40, 'wilayah_id' => 6, 'jarak' => 4.55],
            ['sekolah_id' => 10, 'wilayah_id' => 1, 'jarak' => 4.5],
            ['sekolah_id' => 10, 'wilayah_id' => 7, 'jarak' => 5.13],
            ['sekolah_id' => 10, 'wilayah_id' => 8, 'jarak' => 3.82],
            ['sekolah_id' => 10, 'wilayah_id' => 2, 'jarak' => 3.18],
            ['sekolah_id' => 10, 'wilayah_id' => 9, 'jarak' => 17.86],
            ['sekolah_id' => 29, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 41, 'wilayah_id' => 4, 'jarak' => 2.02],
            ['sekolah_id' => 41, 'wilayah_id' => 5, 'jarak' => 1.8],
            ['sekolah_id' => 41, 'wilayah_id' => 6, 'jarak' => 1.65],
            ['sekolah_id' => 11, 'wilayah_id' => 1, 'jarak' => 15.5],
            ['sekolah_id' => 11, 'wilayah_id' => 7, 'jarak' => 16.12],
            ['sekolah_id' => 11, 'wilayah_id' => 8, 'jarak' => 14.82],
            ['sekolah_id' => 11, 'wilayah_id' => 2, 'jarak' => 14.18],
            ['sekolah_id' => 11, 'wilayah_id' => 9, 'jarak' => 17.57],
            ['sekolah_id' => 44, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 12, 'wilayah_id' => 1, 'jarak' => 14.54],
            ['sekolah_id' => 12, 'wilayah_id' => 7, 'jarak' => 15.16],
            ['sekolah_id' => 12, 'wilayah_id' => 8, 'jarak' => 13.86],
            ['sekolah_id' => 12, 'wilayah_id' => 2, 'jarak' => 13.21],
            ['sekolah_id' => 12, 'wilayah_id' => 9, 'jarak' => 17.57],
            ['sekolah_id' => 28, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 13, 'wilayah_id' => 9, 'jarak' => 0.14],
            ['sekolah_id' => 27, 'wilayah_id' => 3, 'jarak' => 3.65],
            ['sekolah_id' => 14, 'wilayah_id' => 9, 'jarak' => 0.16],
            ['sekolah_id' => 16, 'wilayah_id' => 3, 'jarak' => 0.47],
            ['sekolah_id' => 26, 'wilayah_id' => 3, 'jarak' => 0.47],
        ];

        // 3. Iterasi massal menggunakan Model Eloquent Laravel dengan timestamp yang presisi
        $now = Carbon::now();
        foreach ($dataJarak as $item) {
            JarakSekolahLokasi::create([
                'sekolah_id' => $item['sekolah_id'],
                'wilayah_id' => $item['wilayah_id'],
                'jarak'      => $item['jarak'],
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        $this->command->info('Sukses! Seluruh 125 baris matriks data jarak sekolah-lokasi berhasil dimasukkan.');
    }
}