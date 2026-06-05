<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jenjang', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); 
            $table->string('nama_jenjang'); 
            $table->timestamps();
        });
    }

    public function down(): void {
        // FIX: Ubah dari wilayah_desa menjadi jenjang
        Schema::dropIfExists('jenjang'); 
    }
};