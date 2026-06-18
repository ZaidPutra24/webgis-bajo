<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KurikulumUtilitas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KurikulumUtilitasSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KurikulumUtilitas::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $dataUtilitas = [
            ['sekolah_id' => 1, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Double Shift/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 4200.0],
            ['sekolah_id' => 2, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 2236.0],
            ['sekolah_id' => 4, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2400.0],
            ['sekolah_id' => 5, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 3300.0],
            ['sekolah_id' => 6, 'kurikulum' => '-', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 7, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2000.0],
            ['sekolah_id' => 8, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2142.0],
            ['sekolah_id' => 9, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 1875.0],
            ['sekolah_id' => 10, 'kurikulum' => 'Lainnya', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 5500.0],
            ['sekolah_id' => 11, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 8320.0],
            ['sekolah_id' => 12, 'kurikulum' => 'Kurikulum SMA Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 10000.0],
            ['sekolah_id' => 13, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'Diesel', 'daya_listrik' => 3800, 'luas_tanah' => 2196.0],
            ['sekolah_id' => 14, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'Diesel', 'daya_listrik' => 3800, 'luas_tanah' => 2196.0],
            ['sekolah_id' => 15, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 3410.0],
            ['sekolah_id' => 16, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 20000.0],
            ['sekolah_id' => 17, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Double Shift/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 3062.0],
            ['sekolah_id' => 18, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 9000.0],
            ['sekolah_id' => 19, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 4000.0],
            ['sekolah_id' => 20, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 9000.0],
            ['sekolah_id' => 21, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 3834.0],
            ['sekolah_id' => 22, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 4615.0],
            ['sekolah_id' => 23, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 8000.0],
            ['sekolah_id' => 24, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 2737.0],
            ['sekolah_id' => 25, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 1500.0],
            ['sekolah_id' => 26, 'kurikulum' => 'Lainnya', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 10000.0],
            ['sekolah_id' => 27, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 127000.0],
            ['sekolah_id' => 28, 'kurikulum' => '-', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => 10000.0],
            ['sekolah_id' => 29, 'kurikulum' => 'Kurikulum SMA Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 3500, 'luas_tanah' => 10000.0],
            ['sekolah_id' => 30, 'kurikulum' => 'Kurikulum SMA Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 3500, 'luas_tanah' => 30000.0],
            ['sekolah_id' => 31, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 1720.0],
            ['sekolah_id' => 32, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 2850.0],
            ['sekolah_id' => 33, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 1800.0],
            ['sekolah_id' => 34, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 2464.0],
            ['sekolah_id' => 35, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 2856.0],
            ['sekolah_id' => 36, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 10000.0],
            ['sekolah_id' => 37, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 1452.0],
            ['sekolah_id' => 38, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => 16193.0],
            ['sekolah_id' => 39, 'kurikulum' => 'Lainnya', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => 10000.0],
            ['sekolah_id' => 40, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 1800.0],
            ['sekolah_id' => 41, 'kurikulum' => 'Kurikulum SMA Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 3500, 'luas_tanah' => 20000.0],
            ['sekolah_id' => 42, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 220, 'luas_tanah' => 2250.0],
            ['sekolah_id' => 43, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 2223.0],
            ['sekolah_id' => 44, 'kurikulum' => 'Lainnya', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 4400, 'luas_tanah' => 9315.0],
            ['sekolah_id' => 45, 'kurikulum' => 'Kurikulum SMP Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 3500, 'luas_tanah' => 8760.0],
            ['sekolah_id' => 46, 'kurikulum' => 'Kurikulum SD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => 6000.0],
            ['sekolah_id' => 47, 'kurikulum' => '-', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 48, 'kurikulum' => '-', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 49, 'kurikulum' => 'Kurikulum 2013', 'penyelenggara' => 'Sehari Penuh/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 5500, 'luas_tanah' => null],
            ['sekolah_id' => 50, 'kurikulum' => 'Kurikulum Paket C IPS 2013', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => null],
            ['sekolah_id' => 51, 'kurikulum' => 'Kurikulum Paket C IPS 2013', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'Menumpang', 'daya_listrik' => 1300, 'luas_tanah' => null],
            ['sekolah_id' => 52, 'kurikulum' => 'Kurikulum 2013', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => null],
            ['sekolah_id' => 53, 'kurikulum' => 'Kurikulum Paket C IPS 2013', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 54, 'kurikulum' => 'Kurikulum Paket B Merdeka', 'penyelenggara' => 'Sore/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => null],
            ['sekolah_id' => 55, 'kurikulum' => 'Kurikulum Paket B Merdeka', 'penyelenggara' => 'Sehari Penuh/3 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => null],
            ['sekolah_id' => 56, 'kurikulum' => 'Kurikulum Paket A Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => null],
            ['sekolah_id' => 57, 'kurikulum' => 'Kurikulum 2013', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Tidak Ada', 'sumber_listrik' => 'Tidak Ada', 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 58, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'Menumpang', 'daya_listrik' => 900, 'luas_tanah' => null],
            ['sekolah_id' => 59, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => null],
            ['sekolah_id' => 60, 'kurikulum' => '-', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 61, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'Menumpang', 'daya_listrik' => 450, 'luas_tanah' => null],
            ['sekolah_id' => 62, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => null],
            ['sekolah_id' => 63, 'kurikulum' => 'Kurikulum 2013', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 1300, 'luas_tanah' => null],
            ['sekolah_id' => 64, 'kurikulum' => 'Kurikulum 2013', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 65, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 220, 'luas_tanah' => null],
            ['sekolah_id' => 66, 'kurikulum' => 'Kurikulum 2013', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 990, 'luas_tanah' => null],
            ['sekolah_id' => 67, 'kurikulum' => 'Kurikulum 2013', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => null],
            ['sekolah_id' => 68, 'kurikulum' => 'Kurikulum 2013', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => null],
            ['sekolah_id' => 69, 'kurikulum' => '-', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 70, 'kurikulum' => '-', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 71, 'kurikulum' => '-', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 72, 'kurikulum' => '-', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 73, 'kurikulum' => '-', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 74, 'kurikulum' => '-', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 75, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/6 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => null],
            ['sekolah_id' => 76, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 5500, 'luas_tanah' => null],
            ['sekolah_id' => 77, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 78, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 79, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Tidak ada', 'sumber_listrik' => 'Tidak ada', 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 80, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Shared', 'sumber_listrik' => 'PLN', 'daya_listrik' => 2200, 'luas_tanah' => null],
            ['sekolah_id' => 81, 'kurikulum' => '-', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 82, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'Menumpang', 'daya_listrik' => 200, 'luas_tanah' => null],
            ['sekolah_id' => 83, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Sehari Penuh/5 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => null],
            ['sekolah_id' => 84, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => '-', 'akses_internet' => null, 'sumber_listrik' => null, 'daya_listrik' => null, 'luas_tanah' => null],
            ['sekolah_id' => 85, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => null],
            ['sekolah_id' => 86, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Broadband', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => null],
            ['sekolah_id' => 87, 'kurikulum' => 'Kurikulum PAUD Merdeka', 'penyelenggara' => 'Pagi/5 hari', 'akses_internet' => 'Dedicated', 'sumber_listrik' => 'PLN', 'daya_listrik' => 900, 'luas_tanah' => null],
        ];

        $now = Carbon::now();
        foreach ($dataUtilitas as $item) {
            KurikulumUtilitas::create(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now
            ]));
        }

        $this->command->info('Sukses! 86 data kurikulum & utilitas sekolah berhasil dimasukkan.');
    }
}