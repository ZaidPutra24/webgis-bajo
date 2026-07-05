<?php

namespace Database\Seeders;

use App\Models\WilayahKecamatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahKecamatanSeeder extends Seeder
{
    /**
     * Data batas administrasi kecamatan (ROI) bersumber dari data resmi batas
     * kecamatan (BIG/Lapak GIS). GeoJSON disimpan verbatim (per file) di
     * database/seeders/data/kecamatan/ dan dibaca saat seeding.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        WilayahKecamatan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $dataKecamatan = [
            [
                'file'           => 'Tinanggea.geojson',
                'nama_kecamatan' => 'Tinanggea',
                'kabupaten'      => 'Konawe Selatan',
                'provinsi'       => 'Sulawesi Tenggara',
                'warna'          => '#ea580c',
            ],
            [
                'file'           => 'Wawonii.geojson',
                'nama_kecamatan' => 'Wawonii Barat',
                'kabupaten'      => 'Konawe Kepulauan',
                'provinsi'       => 'Sulawesi Tenggara',
                'warna'          => '#0284c7',
            ],
            [
                'file'           => 'Soropia.geojson',
                'nama_kecamatan' => 'Soropia',
                'kabupaten'      => 'Konawe',
                'provinsi'       => 'Sulawesi Tenggara',
                'warna'          => '#16a34a',
            ],
        ];

        foreach ($dataKecamatan as $kec) {
            $path = database_path('seeders/data/kecamatan/' . $kec['file']);

            if (!file_exists($path)) {
                continue;
            }

            WilayahKecamatan::create([
                'nama_kecamatan' => $kec['nama_kecamatan'],
                'kabupaten'      => $kec['kabupaten'],
                'provinsi'       => $kec['provinsi'],
                'warna'          => $kec['warna'],
                'luas_wilayah'   => null,
                'geojson'        => file_get_contents($path),
            ]);
        }
    }
}
