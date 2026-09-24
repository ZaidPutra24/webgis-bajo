<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Standar maksimum murid per rombel KHUSUS Paket A/B/C (pendidikan
 * kesetaraan di PKBM), terpisah dari tabel `standar_rombel`.
 *
 * KENAPA TABEL BARU, BUKAN PAKAI `standar_rombel` YANG SUDAH ADA:
 * `standar_rombel` di-keyed oleh jenjang_id dengan constraint UNIQUE
 * (1 baris per jenjang). PKBM cuma py 1 jenjang_id (12) di tabel `jenjang`
 * — tapi Paket A, B, dan C punya standar maks murid/rombel yang BERBEDA
 * (20 / 25 / 30). Kalau dipaksa masuk ke `standar_rombel` dengan jenjang_id
 * yang sama, hanya bisa tersimpan SATU angka untuk PKBM, padahal butuh 3.
 *
 * Catatan: migration `create_standar_rombel_table` & `create_standar_radius_
 * layanan_table` sebelumnya punya komentar yang menyebut "jenjang_id 16/17/18
 * untuk Paket A/B/C" — itu rencana lama yang TIDAK JADI dipakai (jenjang_id
 * tsb tidak pernah dibuat di JenjangSeeder, dan keputusan terbaru: jenjang
 * PKBM tetap satu, tidak dipecah). Tabel ini menggantikan rencana lama itu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standar_rombel_paket', function (Blueprint $table) {
            $table->id();

            // 'A', 'B', atau 'C'
            $table->string('paket', 1)->unique();

            $table->integer('maks_murid_per_rombel');

            $table->string('dasar_hukum', 150)->nullable();
            $table->string('nomor_dokumen', 100)->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standar_rombel_paket');
    }
};
