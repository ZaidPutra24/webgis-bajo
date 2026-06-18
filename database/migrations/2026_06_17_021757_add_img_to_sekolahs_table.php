<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            // Tambah kolom img
            if (!Schema::hasColumn('sekolah', 'img')) {
                $table->string('img')->nullable()->after('longitude');
            }

            // Ubah latitude & longitude menjadi nullable
            // agar sekolah tanpa koordinat (TK, PKBM, LKP, dll) tetap bisa di-insert
            $table->decimal('latitude', 10, 8)->nullable()->change();
            $table->decimal('longitude', 11, 8)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            if (Schema::hasColumn('sekolah', 'img')) {
                $table->dropColumn('img');
            }
            $table->decimal('latitude', 10, 8)->nullable(false)->change();
            $table->decimal('longitude', 11, 8)->nullable(false)->change();
        });
    }
};