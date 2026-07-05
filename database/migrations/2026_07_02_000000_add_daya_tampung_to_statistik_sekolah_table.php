<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kembali kolom 'daya_tampung' (kapasitas daya tampung siswa)
     * ke tabel statistik_sekolah. Kolom ini sempat dihapus di migration
     * 2026_06_04_030921_alter_statistik_sekolah_table.php karena saat itu
     * belum ada datanya. Sekarang datanya sudah tersedia sehingga kolom
     * ditambahkan kembali sebagai kolom baru.
     *
     * Nullable karena beberapa satuan pendidikan non-formal (LKP/PKBM/SKB)
     * memang tidak memiliki data daya tampung (bukan bernilai 0).
     */
    public function up(): void
    {
        Schema::table('statistik_sekolah', function (Blueprint $table) {
            if (!Schema::hasColumn('statistik_sekolah', 'daya_tampung')) {
                $table->integer('daya_tampung')->nullable()->after('jumlah_siswa');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statistik_sekolah', function (Blueprint $table) {
            if (Schema::hasColumn('statistik_sekolah', 'daya_tampung')) {
                $table->dropColumn('daya_tampung');
            }
        });
    }
};
