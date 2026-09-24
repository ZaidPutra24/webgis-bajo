<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel referensi standar maksimum murid per rombongan belajar (rombel), per
 * jenjang. Dijadikan tabel yang bisa diedit admin (bukan angka hardcode di
 * kode) supaya kalau ada revisi SK/Kepmen, tinggal update di dashboard.
 *
 * Sumber awal: Keputusan Kementerian Dikdasmen No. 14 Tahun 2026 tentang
 * jumlah murid per rombel (PAUD 15, SD 28, SMP 32, SMA 36, Paket A 20,
 * Paket B 25, Paket C 30) — sesuai data yang diberikan pengguna. Belum
 * diverifikasi ulang secara independen, jadi disimpan sebagai data yang
 * bisa dikoreksi, lengkap dengan kolom dasar hukum & keterangan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standar_rombel', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jenjang_id')
                  ->unique()
                  ->constrained('jenjang')
                  ->onDelete('cascade');

            $table->integer('maks_murid_per_rombel');

            // Dasar hukum, mis. "Kepmendikdasmen No. 14 Tahun 2026"
            $table->string('dasar_hukum', 150)->nullable();
            $table->string('nomor_dokumen', 100)->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standar_rombel');
    }
};
