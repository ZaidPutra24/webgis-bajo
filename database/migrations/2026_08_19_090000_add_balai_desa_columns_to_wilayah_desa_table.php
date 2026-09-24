<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah kolom titik "balai desa" (representatif) ke wilayah_desa.
 *
 * Default awal: centroid otomatis dari polygon geojson desa (dihitung via
 * App\Support\GeoHelper::centroid() saat seeding / lewat perintah
 * `php artisan wilayah:hitung-ulang`). Admin bisa override manual dengan
 * koordinat balai desa sebenarnya kapan saja lewat form Village Areas.
 *
 * Titik ini dipakai sebagai origin untuk menghitung jarak ke SD/SMP/SMA
 * terdekat pada fitur School-Age Density per Village.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wilayah_desa', function (Blueprint $table) {
            if (!Schema::hasColumn('wilayah_desa', 'latitude_balai')) {
                $table->decimal('latitude_balai', 10, 8)->nullable()->after('luas_wilayah');
            }
            if (!Schema::hasColumn('wilayah_desa', 'longitude_balai')) {
                $table->decimal('longitude_balai', 11, 8)->nullable()->after('latitude_balai');
            }
            if (!Schema::hasColumn('wilayah_desa', 'sumber_titik_balai')) {
                // 'centroid' (default otomatis) atau 'manual' (sudah dioverride admin)
                $table->string('sumber_titik_balai', 20)->default('centroid')->after('longitude_balai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wilayah_desa', function (Blueprint $table) {
            foreach (['latitude_balai', 'longitude_balai', 'sumber_titik_balai'] as $col) {
                if (Schema::hasColumn('wilayah_desa', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
