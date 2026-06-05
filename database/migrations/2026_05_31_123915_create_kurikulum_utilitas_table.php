<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurikulum_utilitas', function (Blueprint $table) {
            $table->id();
            
            // Relasi One-to-One dengan tabel sekolah (Ditambahkan unique)
            $table->foreignId('sekolah_id')
                  ->unique()
                  ->constrained('sekolah')
                  ->onDelete('cascade');
            
            // Kolom Kurikulum & Penyelenggara
            $table->string('kurikulum', 100)->nullable();
            $table->string('penyelenggara', 150)->nullable();
            
            // Kolom Utilitas Dasar
            $table->string('akses_internet', 150)->nullable();
            $table->string('sumber_listrik', 150)->nullable();
            $table->integer('daya_listrik')->nullable();
            $table->decimal('luas_tanah', 15, 2)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurikulum_utilitas');
    }
};