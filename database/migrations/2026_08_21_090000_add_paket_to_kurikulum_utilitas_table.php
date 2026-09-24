<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FIX arsitektur PKBM: jenjang PKBM (jenjang_id = 12) TETAP SATU baris di
 * tabel jenjang — TIDAK dipecah jadi jenjang terpisah per Paket A/B/C.
 * Sesuai keputusan pengguna: yang membedakan Paket A/B/C pada satu sekolah
 * PKBM adalah KURIKULUM yang dipakai (mis. "Kurikulum Paket C IPS 2013"),
 * bukan jenjangnya.
 *
 * Masalahnya: sebelum migration ini, info paket itu HANYA ada sebagai teks
 * bebas di kolom `kurikulum` (mis. "Kurikulum Paket C IPS 2013" vs "Kurikulum
 * 2013" vs "Kurikulum Paket B Merdeka") — tidak ada cara query/filter yang
 * andal untuk "cari semua PKBM yang menyelenggarakan Paket A" karena harus
 * pattern-matching teks bebas yang formatnya tidak konsisten.
 *
 * Solusi: tambah kolom `paket` (nullable, 'A'/'B'/'C') di SAMPING kolom
 * `kurikulum` yang sudah ada — bukan menggantikannya. `kurikulum` tetap teks
 * bebas untuk keperluan tampilan/histori/detail ("Kurikulum Paket C IPS
 * 2013"), sedangkan `paket` adalah nilai terstruktur untuk query/logika
 * (dipakai fitur rekomendasi sekolah PKBM per Paket A/B/C ke depannya).
 *
 * Nullable & tidak divalidasi berat karena hanya relevan untuk sekolah
 * jenjang PKBM (id 12) — untuk sekolah formal (SD/SMP/SMA/dst.) kolom ini
 * dibiarkan kosong.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kurikulum_utilitas', function (Blueprint $table) {
            $table->string('paket', 1)->nullable()->after('kurikulum');
        });
    }

    public function down(): void
    {
        Schema::table('kurikulum_utilitas', function (Blueprint $table) {
            $table->dropColumn('paket');
        });
    }
};
