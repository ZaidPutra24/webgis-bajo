<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KurikulumUtilitas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KurikulumUtilitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel kurikulum_utilitas terlebih dahulu agar bersih & tidak duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KurikulumUtilitas::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. 45 Baris Data Utilitas Sekolah Lengkap & Hasil Normalisasi Integritas Data
        $dataUtilitas = [
            ['sekolah_id' => 1, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Double Shift/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 4200.0],
            ['sekolah_id' => 2, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 2236.0],
            ['sekolah_id' => 4, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2400.0],
            ['sekolah_id' => 5, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 3300.0],
            ['sekolah_id' => 6, 'kurikulum' => null, 'penyelenggara' => null, 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 7, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2000.0],
            ['sekolah_id' => 8, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2142.0],
            ['sekolah_id' => 9, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 1875.0],
            ['sekolah_id' => 10, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 5500.0],
            ['sekolah_id' => 11, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 8320.0],
            ['sekolah_id' => 12, 'kurikulum' => 'Kurikulum SMA Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 10000.0],
            ['sekolah_id' => 13, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2196.0],
            ['sekolah_id' => 14, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2196.0],
            ['sekolah_id' => 15, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 3410.0],
            ['sekolah_id' => 16, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 20000.0],
            ['sekolah_id' => 17, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'Diesel', 'daya_listrik' => 3800, 'luas_tanah' => 3062.0],
            ['sekolah_id' => 18, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 9000.0],
            ['sekolah_id' => 19, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 4000.0],
            ['sekolah_id' => 20, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 3834.0],
            ['sekolah_id' => 21, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 4615.0],
            ['sekolah_id' => 22, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 8000.0],
            ['sekolah_id' => 23, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2737.0],
            ['sekolah_id' => 24, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 1500.0],
            ['sekolah_id' => 25, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 127000.0],
            ['sekolah_id' => 26, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 30000.0],
            ['sekolah_id' => 27, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 3500, 'luas_tanah' => 8320.0],
            ['sekolah_id' => 28, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 5500.0],
            ['sekolah_id' => 29, 'kurikulum' => 'Kurikulum SMA Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 10000.0],
            ['sekolah_id' => 30, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 1720.0],
            ['sekolah_id' => 31, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 2850.0],
            ['sekolah_id' => 32, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 1800.0],
            ['sekolah_id' => 33, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 2464.0],
            ['sekolah_id' => 34, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 2856.0],
            ['sekolah_id' => 35, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 10000.0],
            ['sekolah_id' => 36, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 1452.0],
            ['sekolah_id' => 37, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 16193.0],
            ['sekolah_id' => 38, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 2250.0],
            ['sekolah_id' => 39, 'kurikulum' => 'Kurikulum SMA Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 10000.0],
            ['sekolah_id' => 40, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 220, 'luas_tanah' => 2223.0],
            ['sekolah_id' => 41, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2223.0],
            ['sekolah_id' => 42, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2223.0],
            ['sekolah_id' => 43, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 9315.0],
            ['sekolah_id' => 44, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 4400, 'luas_tanah' => 2223.0],
            ['sekolah_id' => 45, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 3500, 'luas_tanah' => 8760.0],
            ['sekolah_id' => 46, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 6000.0],
        ];

        // 3. Suntikkan massal ke database dengan timestamps Laravel yang seragam
        $now = Carbon::now();
        foreach ($dataUtilitas as $item) {
            KurikulumUtilitas::create(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now
            ]));
        }

        $this->command->info('Sukses! Seluruh 45 data kurikulum & utilitas sekolah telah dibersihkan dan berhasil diimpor.');
    }
}