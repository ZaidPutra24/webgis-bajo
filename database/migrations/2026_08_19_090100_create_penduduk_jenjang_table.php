<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rincian penduduk usia sekolah PER JENJANG per desa (bukan agregat seperti
 * kolom penduduk_usia_sekolah_l/p di wilayah_desa), plus jumlah Anak Tidak
 * Sekolah / putus sekolah per jenjang dari data ATS.
 *
 * Ini adalah data inti untuk fitur "School-Age Density per Village" versi
 * baru, supaya kepadatan bisa dihitung dan dibandingkan terhadap standar
 * rombel & radius layanan per jenjang (bukan cuma kepadatan kasar).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penduduk_jenjang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wilayah_id')
                  ->constrained('wilayah_desa')
                  ->onDelete('cascade');

            $table->foreignId('jenjang_id')
                  ->constrained('jenjang')
                  ->onDelete('cascade');

            // Total penduduk usia sekolah jenjang ini (L/P) — basis kepadatan
            $table->integer('penduduk_l')->default(0);
            $table->integer('penduduk_p')->default(0);

            // Anak Tidak Sekolah (ATS) / putus sekolah jenjang ini (L/P).
            // Ini SUBSET dari penduduk_l/penduduk_p di atas, BUKAN tambahan
            // di luar itu — yaitu berapa dari total penduduk usia jenjang ini
            // yang tidak/belum bersekolah.
            $table->integer('putus_sekolah_l')->default(0);
            $table->integer('putus_sekolah_p')->default(0);

            // Sumber data, mis. "Dummy - agregat WilayahSeeder" atau "ATS Dinas Dikbud 2026"
            $table->string('sumber_data', 100)->nullable();

            $table->timestamps();

            $table->unique(['wilayah_id', 'jenjang_id'], 'penduduk_jenjang_wilayah_jenjang_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penduduk_jenjang');
    }
};
