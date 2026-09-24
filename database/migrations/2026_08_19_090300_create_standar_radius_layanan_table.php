<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel referensi standar radius pelayanan sekolah per jenjang, dipakai
 * sebagai acuan/cross-check SEKUNDER perencanaan fasilitas pendidikan
 * (bukan zonasi PPDB). Default awal memakai SNI 03-1733-2004 (Tata Cara
 * Perencanaan Lingkungan Perumahan Perkotaan, Kementerian PU).
 *
 * Khusus desa pesisir/kepulauan (Bajo), radius garis lurus (km) kurang
 * representatif kalau akses harus lewat laut — karena itu status
 * "terlayani/tidak" pada fitur density tetap DIPRIORITASKAN dari data waktu
 * tempuh riil (walk_mnt/drive_mnt/boat_mnt) di jaraksekolahlokasi bila
 * tersedia, dan radius di tabel ini hanya jadi pembanding sekunder.
 *
 * Dibuat sebagai tabel yang bisa diedit admin, supaya begitu ada SK/Perbup/
 * data BAPPEDA yang lebih akurat untuk wilayah Bajo, tinggal diubah dari
 * dashboard tanpa perlu ubah kode.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standar_radius_layanan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jenjang_id')
                  ->unique()
                  ->constrained('jenjang')
                  ->onDelete('cascade');

            $table->integer('radius_meter');
            $table->integer('penduduk_pendukung_ideal')->nullable();

            // Sumber, mis. "SNI 03-1733-2004"
            $table->string('sumber', 150)->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standar_radius_layanan');
    }
};
