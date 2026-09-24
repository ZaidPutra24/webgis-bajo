<?php

namespace Database\Seeders;

use App\Models\WilayahDesa;
use App\Support\GeoHelper;
use Illuminate\Database\Seeder;

/**
 * Mengisi otomatis luas_wilayah (km²) dan titik balai desa (latitude_balai/
 * longitude_balai) dari geojson polygon tiap desa, memakai GeoHelper.
 *
 * Ini menutup temuan sebelumnya: kolom luas_wilayah di 9 desa semuanya NULL
 * padahal dibutuhkan sebagai pembagi rumus kepadatan. Dijalankan SETELAH
 * WilayahSeeder. Tidak menimpa luas_wilayah/koordinat balai yang sudah pernah
 * diisi manual oleh admin (idempotent & aman dijalankan ulang).
 *
 * PENGECUALIAN (REVISI luas_wilayah resmi BPS): sejak WilayahSeeder mengisi
 * luas_wilayah 10 dari 11 desa langsung dari data BPS, seeder ini praktis
 * jadi fallback utk desa yg belum/tidak ada angka BPS-nya saja. Utk 'Desa
 * Saponda Laut', tabel BPS eksplisit menyatakan data tidak tersedia ("...")
 * - NULL di kolom ini SENGAJA, bukan "belum sempat dihitung". Jangan sampai
 * auto-calc ini malah mengisinya dgn luas geometri polygon (yg beda makna
 * dari luas administratif BPS), makanya desa ini dikecualikan secara
 * eksplisit di bawah.
 */
class WilayahAutoCalcSeeder extends Seeder
{
    /**
     * Nama desa yang luas_wilayah-nya SENGAJA NULL (data BPS tidak tersedia)
     * dan tidak boleh diisi otomatis dari geometri polygon.
     */
    private const KECUALIKAN_DARI_AUTO_LUAS = [
        'Desa Saponda Laut',
    ];

    public function run(): void
    {
        $diperbarui = 0;

        WilayahDesa::whereNull('luas_wilayah')
            ->orWhereNull('latitude_balai')
            ->get()
            ->each(function (WilayahDesa $desa) use (&$diperbarui) {
                $dirty = false;
                $dikecualikan = in_array($desa->nama_wilayah, self::KECUALIKAN_DARI_AUTO_LUAS, true);

                if ($desa->luas_wilayah === null && !$dikecualikan) {
                    $luas = GeoHelper::polygonAreaKm2($desa->geojson);
                    if ($luas !== null) {
                        $desa->luas_wilayah = $luas;
                        $dirty = true;
                    }
                }

                if ($desa->latitude_balai === null || $desa->longitude_balai === null) {
                    $centroid = GeoHelper::centroid($desa->geojson);
                    if ($centroid !== null) {
                        $desa->latitude_balai = $centroid['latitude'];
                        $desa->longitude_balai = $centroid['longitude'];
                        $desa->sumber_titik_balai = 'centroid';
                        $dirty = true;
                    }
                }

                if ($dirty) {
                    $desa->save();
                    $diperbarui++;
                }
            });

        $this->command->info("Sukses! {$diperbarui} desa diperbarui (luas_wilayah & titik balai otomatis dari polygon).");
    }
}
