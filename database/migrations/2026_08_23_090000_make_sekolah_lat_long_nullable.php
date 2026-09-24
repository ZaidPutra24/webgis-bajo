<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FIX: Kolom latitude & longitude pada tabel sekolah awalnya NOT NULL,
     * padahal SekolahSeeder.php sejak awal didesain untuk mengisi null pada
     * sekolah yang belum diketahui titik koordinatnya (lihat komentar
     * "Sekolah tanpa koordinat dibiarkan null" di seeder tersebut).
     *
     * Tanpa migration ini, seeder akan gagal (SQLSTATE 23000: Column
     * 'latitude'/'longitude' cannot be null) setiap kali menyisipkan
     * sekolah yang titiknya belum tersedia.
     */
    public function up(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->change();
            $table->decimal('longitude', 11, 8)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable(false)->change();
            $table->decimal('longitude', 11, 8)->nullable(false)->change();
        });
    }
};
