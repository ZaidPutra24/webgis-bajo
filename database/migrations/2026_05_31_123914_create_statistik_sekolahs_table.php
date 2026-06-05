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
        Schema::create('statistik_sekolah', function (Blueprint $table) {
            $table->id();
            
            // Relasi One-to-One ke tabel sekolah
            $table->foreignId('sekolah_id')
                  ->unique()
                  ->constrained('sekolah') // Pastikan nama tabel sekolah Anda benar 'sekolah'
                  ->onDelete('cascade');
            
            // Metrik & Fasilitas (Default 0 sesuai desain Anda
            $table->integer('jumlah_siswa')->default(0);
            $table->integer('jumlah_guru')->default(0);
            $table->integer('jumlah_rombel')->default(0);
            $table->integer('ruang_kelas')->default(0);
            $table->integer('laboratorium')->default(0);
            $table->integer('perpustakaan')->default(0);
            
            $table->timestamps(); // Otomatis membuat created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistik_sekolah');
    }
};