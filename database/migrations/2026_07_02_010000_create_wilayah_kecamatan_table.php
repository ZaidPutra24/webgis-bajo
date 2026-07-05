<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel wilayah_kecamatan menyimpan boundary/ROI (Region of Interest) tingkat
     * kecamatan (lebih luas dari wilayah_desa). Digunakan pada menu "Wilayah Kecamatan"
     * untuk menghitung jumlah sekolah yang berada dalam jangkauan satu kecamatan.
     */
    public function up(): void
    {
        Schema::create('wilayah_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kecamatan');
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->longText('geojson');
            $table->decimal('luas_wilayah', 12, 2)->nullable();
            // Warna outline/fill ROI di peta (hex), agar tiap kecamatan bisa dibedakan
            $table->string('warna', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_kecamatan');
    }
};
