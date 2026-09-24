<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FIX presisi luas_wilayah: decimal(10,2) -> decimal(12,4).
 *
 * luas_wilayah dipakai sebagai PEMBAGI rumus kepadatan
 * (total_penduduk / luas_wilayah). Untuk desa kepulauan kecil, luas dalam
 * km2 bisa berupa angka desimal kecil (mis. 0,3456 km2) yang terpotong jadi
 * 0,35 pada presisi (10,2). Selisih pembulatan sekecil itu tetap bisa
 * menggeser signifikan hasil kepadatan pada desa berluas kecil. Menaikkan
 * presisi ke (12,4) supaya begitu data ATS riil (pembilang) masuk lewat
 * form, pembaginya juga tidak jadi sumber galat.
 *
 * Aman dijalankan berulang & tidak mengubah nilai yang sudah tersimpan
 * (hanya memperbesar skala kolom).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wilayah_desa', function (Blueprint $table) {
            $table->decimal('luas_wilayah', 12, 4)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('wilayah_desa', function (Blueprint $table) {
            $table->decimal('luas_wilayah', 10, 2)->nullable()->change();
        });
    }
};
