<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Menggunakan updateOrCreate agar jika email sudah ada, data hanya diupdate, bukan ditambah baru
        User::updateOrCreate(
            ['email' => 'admin@webgis.com'], // Tolok ukur keunikan
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        // Memanggil call untuk seeder lainnya
        $this->call([
            JenjangSeeder::class,
            WilayahKecamatanSeeder::class,
            WilayahSeeder::class,
            // Hitung otomatis luas_wilayah & titik balai desa dari polygon geojson
            // (menutup temuan: luas_wilayah sebelumnya semua NULL).
            WilayahAutoCalcSeeder::class,
            SekolahSeeder::class,
            StatistikSekolahSeeder::class,
            KurikulumUtilitasSeeder::class,
            // Backfill kolom `paket` (A/B/C) untuk sekolah PKBM dari teks
            // kurikulum yang sudah ada. Idempotent (hanya isi yang NULL,
            // tidak pernah truncate) karena kurikulum_utilitas punya form
            // edit admin sungguhan. Lihat KurikulumUtilitasPaketSeeder.
            KurikulumUtilitasPaketSeeder::class,
            JarakSekolahLokasiSeeder::class,
            // Tabel referensi standar (bisa diedit admin) untuk fitur
            // School-Age Density per Village.
            StandarRombelSeeder::class,
            // Standar rombel KHUSUS Paket A/B/C — tabel terpisah dari
            // StandarRombel karena PKBM cuma 1 jenjang_id tapi 3 standar
            // berbeda (Paket A/B/C). Lihat StandarRombelPaketSeeder.
            StandarRombelPaketSeeder::class,
            StandarRadiusLayananSeeder::class,
            // Data dummy penduduk per jenjang & putus sekolah (ATS), diturunkan
            // dari agregat wilayah_desa — jalan terakhir karena butuh WilayahSeeder.
            // FIX: seeder ini sekarang idempotent (hanya mengisi baris yang
            // masih kosong, tidak pernah truncate) supaya aman dipanggil ulang
            // lewat `migrate:fresh --seed` tanpa menghapus data ATS asli yang
            // sudah diinput admin lewat form. Lihat PendudukJenjangSeeder.
            PendudukJenjangSeeder::class,
            // DATA RIIL ATS per jenjang (11 desa wilayah Bajo) dari analisis
            // Dinas — menimpa baris DUMMY di atas (bukan input manual admin)
            // dengan angka ATS yang sesungguhnya. Dijalankan SETELAH
            // PendudukJenjangSeeder supaya ada baseline dummy untuk desa/
            // jenjang yang belum tercakup data riil ini. Lihat
            // PendudukJenjangAtsRiilSeeder untuk rincian apa yang real vs
            // estimasi.
            PendudukJenjangAtsRiilSeeder::class,
            // Kebutuhan PKBM (BPB usia 19-24 & 25+) riil per desa — SENGAJA
            // di tabel terpisah (kebutuhan_pkbm), bukan baris penduduk_jenjang
            // jenjang_id=12, karena PKBM tidak punya kohort usia sekolah
            // formal. Lihat KebutuhanPkbmSeeder & migration
            // create_kebutuhan_pkbm_table untuk analisis lengkapnya.
            KebutuhanPkbmSeeder::class,
        ]);
    }
}