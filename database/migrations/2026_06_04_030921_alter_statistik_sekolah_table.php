<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pengecekan aman menggunakan Schema::hasColumn
        Schema::table('statistik_sekolah', function (Blueprint $table) {
            // Hanya tambah jika kolom siswa_l belum ada
            if (!Schema::hasColumn('statistik_sekolah', 'siswa_l')) {
                $table->integer('siswa_l')->nullable()->default(0)->after('sekolah_id');
            }
            
            // Hanya tambah jika kolom siswa_p belum ada
            if (!Schema::hasColumn('statistik_sekolah', 'siswa_p')) {
                $table->integer('siswa_p')->nullable()->default(0)->after('siswa_l');
            }
            
            // Hanya hapus jika kolom daya_tampung masih ada
            if (Schema::hasColumn('statistik_sekolah', 'daya_tampung')) {
                $table->dropColumn('daya_tampung');
            }
            
            // Hanya hapus jika kolom ruang_guru masih ada
            if (Schema::hasColumn('statistik_sekolah', 'ruang_guru')) {
                $table->dropColumn('ruang_guru');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statistik_sekolah', function (Blueprint $table) {
            if (!Schema::hasColumn('statistik_sekolah', 'daya_tampung')) {
                $table->integer('daya_tampung')->nullable()->default(0);
            }
            if (!Schema::hasColumn('statistik_sekolah', 'ruang_guru')) {
                $table->integer('ruang_guru')->nullable()->default(0);
            }
            
            if (Schema::hasColumn('statistik_sekolah', 'siswa_l')) {
                $table->dropColumn('siswa_l');
            }
            if (Schema::hasColumn('statistik_sekolah', 'siswa_p')) {
                $table->dropColumn('siswa_p');
            }
        });
    }
};