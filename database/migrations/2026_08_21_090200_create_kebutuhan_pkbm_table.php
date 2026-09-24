<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kebutuhan PKBM (Paket A/B/C) per desa — SENGAJA DIPISAH dari tabel
 * `penduduk_jenjang`, bukan ditambahkan sebagai baris jenjang_id=12 di sana.
 *
 * ALASAN PEMISAHAN (lihat analisis yang diberikan ke pengguna):
 * `penduduk_jenjang` dirancang untuk jenjang dengan KOHORT USIA JELAS
 * (PAUD 3-6, SD 7-12, SMP 13-15, SMA 16-18) — kolom penduduk_l/p di sana
 * berarti "total penduduk pada rentang usia jenjang tsb di desa ini".
 *
 * PKBM/Paket A/B/C tidak punya kohort usia semacam itu: sasarannya adalah
 * BPB (Belum Pernah Bersekolah) usia 19-24 & 25+ — yaitu penduduk yang SUDAH
 * LEWAT usia sekolah formal. Memaksakan angka "penduduk usia jenjang PKBM"
 * akan berarti mengarang kohort usia yang tidak actually didefinisikan di
 * data sumber manapun (lihat metodologi sheet 'Ringkasan' pada file Analisis
 * ATS & PKBM: BPB 19-24/25+ sudah eksplisit dikeluarkan dari ATS per jenjang
 * dan dikelompokkan terpisah sebagai "Kebutuhan PKBM").
 *
 * Data ini juga TIDAK PERNAH menyebut Paket A/B/C mana yang dibutuhkan per
 * individu — hanya jumlah total BPB yang butuh kesetaraan. Jadi kolom di
 * sini adalah jumlah orang, bukan breakdown per paket (belum ada data
 * granular sampai level itu dari Dinas).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kebutuhan_pkbm', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wilayah_id')
                  ->unique()
                  ->constrained('wilayah_desa')
                  ->onDelete('cascade');

            // BPB (Belum Pernah Bersekolah) usia 19-24 tahun & 25+ tahun —
            // sudah lewat usia sekolah formal, calon peserta Paket A/B/C.
            $table->integer('bpb_19_24')->default(0);
            $table->integer('bpb_25_plus')->default(0);

            // Sumber data, mis. "ATS Riil - Analisis ATS & PKBM Wilayah Bajo 2026"
            $table->string('sumber_data', 150)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kebutuhan_pkbm');
    }
};
