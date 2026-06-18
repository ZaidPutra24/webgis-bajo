<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wilayah_desa', function (Blueprint $table) {
            if (!Schema::hasColumn('wilayah_desa', 'gambar')) {
                $table->string('gambar')->nullable()->after('id');
            }
            if (!Schema::hasColumn('wilayah_desa', 'penduduk_usia_sekolah_l')) {
                $table->integer('penduduk_usia_sekolah_l')->nullable()->after('luas_wilayah');
            }
            if (!Schema::hasColumn('wilayah_desa', 'penduduk_usia_sekolah_p')) {
                $table->integer('penduduk_usia_sekolah_p')->nullable()->after('penduduk_usia_sekolah_l');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wilayah_desa', function (Blueprint $table) {
            foreach (['gambar', 'penduduk_usia_sekolah_l', 'penduduk_usia_sekolah_p'] as $col) {
                if (Schema::hasColumn('wilayah_desa', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};