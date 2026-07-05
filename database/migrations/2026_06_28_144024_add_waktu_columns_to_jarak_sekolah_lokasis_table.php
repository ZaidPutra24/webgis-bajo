<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom waktu tempuh, rute multimoda, dan geometri rute
     * ke tabel jaraksekolahlokasi.
     *
     * Kolom baru:
     *   walk_mnt      — estimasi waktu jalan kaki (menit), ~5 km/jam
     *   drive_mnt     — estimasi waktu berkendara (menit), ~30 km/jam
     *   boat_mnt      — estimasi waktu perahu (menit), ~25 km/jam — null untuk rute darat
     *   jarak_laut    — jarak segmen laut (km) — null untuk rute darat murni
     *   mode_transport— 'darat' atau 'multimoda'
     *   route_geojson — geometry LineString WGS84 (JSON string) untuk ditampilkan di peta Leaflet
     */
    public function up(): void
    {
        Schema::table('jaraksekolahlokasi', function (Blueprint $table) {
            $table->decimal('walk_mnt', 8, 2)->nullable()->after('jarak')
                  ->comment('Estimasi waktu jalan kaki (menit), kecepatan 5 km/jam');

            $table->decimal('drive_mnt', 8, 2)->nullable()->after('walk_mnt')
                  ->comment('Estimasi waktu berkendara (menit), kecepatan 30 km/jam');

            $table->decimal('boat_mnt', 8, 2)->nullable()->after('drive_mnt')
                  ->comment('Estimasi waktu perahu (menit), kecepatan 25 km/jam. NULL untuk rute darat');

            $table->decimal('jarak_laut', 8, 3)->nullable()->after('boat_mnt')
                  ->comment('Jarak segmen laut (km). NULL untuk rute darat murni');

            $table->enum('mode_transport', ['darat', 'multimoda'])
                  ->default('darat')->after('jarak_laut')
                  ->comment('Jenis moda transportasi: darat atau multimoda (darat+perahu)');

            // Geometry rute sebagai GeoJSON string (LineString atau MultiLineString WGS84)
            // Dirender di peta dengan: L.geoJSON(JSON.parse(row.route_geojson))
            $table->longText('route_geojson')->nullable()->after('mode_transport')
                  ->comment('Geometry rute LineString/MultiLineString dalam format GeoJSON WGS84');
        });
    }

    public function down(): void
    {
        Schema::table('jaraksekolahlokasi', function (Blueprint $table) {
            $table->dropColumn([
                'walk_mnt', 'drive_mnt', 'boat_mnt',
                'jarak_laut', 'mode_transport', 'route_geojson'
            ]);
        });
    }
};