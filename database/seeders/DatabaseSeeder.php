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
            SekolahSeeder::class,
            StatistikSekolahSeeder::class,
            KurikulumUtilitasSeeder::class,
            JarakSekolahLokasiSeeder::class,
        ]);
    }
}