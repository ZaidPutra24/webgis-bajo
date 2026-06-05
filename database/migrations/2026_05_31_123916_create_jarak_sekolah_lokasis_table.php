<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jaraksekolahlokasi', function (Blueprint $table) {
            $table->id();
            
            // Mengarah ke tabel 'sekolah' (Sudah ada di urutan 14, AMAN)
            $table->foreignId('sekolah_id')
                  ->constrained('sekolah')
                  ->onDelete('cascade');
            
            // Mengarah ke tabel 'wilayah_desa' (Sudah dipindah ke urutan 13, AMAN)
            $table->foreignId('wilayah_id')
                  ->constrained('wilayah_desa')
                  ->onDelete('cascade');
            
            $table->decimal('jarak', 8, 2)->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jaraksekolahlokasi');
    }
};