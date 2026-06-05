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
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenjang_id')->constrained('jenjang')->onDelete('cascade');
            $table->string('nama_sekolah');
            $table->string('npsn')->nullable();
            $table->enum('status', ['Negeri', 'Swasta']);
            $table->text('alamat')->nullable();
            $table->string('akreditasi', 5)->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // FIX: Matikan pengecekan foreign key constraint sementara sebelum drop tabel
        Schema::disableForeignKeyConstraints();
        
        Schema::dropIfExists('sekolah');
        
        // Aktifkan kembali pengecekan foreign key setelah drop selesai
        Schema::enableForeignKeyConstraints();
    }
};