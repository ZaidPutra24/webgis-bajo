<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restrukturisasi tabel jaraksekolahlokasi: dari 1 baris = 1 pasang
     * wilayah-sekolah (menyimpan walk_mnt + drive_mnt + boat_mnt sekaligus)
     * menjadi 1 baris = 1 leg perjalanan dalam SATU moda saja.
     *
     * Alasan:
     * - Data walking dan driving sekarang dianalisis & disimpan terpisah di
     *   sumber (tidak lagi digabung per pasangan wilayah-sekolah).
     * - Rute pulau (Bungin, dan berpotensi Saponda) perlu direpresentasikan
     *   sebagai beberapa leg berurutan (desa->dermaga, penyeberangan perahu,
     *   dermaga->sekolah), bukan satu baris gabungan dengan jarak_laut/
     *   jarak darat dijumlahkan.
     */
    public function up(): void
    {
        Schema::table('jaraksekolahlokasi', function (Blueprint $table) {
            // sekolah_id harus boleh NULL: leg antara (mis. desa -> dermaga,
            // atau penyeberangan perahu dermaga -> dermaga) belum tentu punya
            // sekolah tujuan.
            $table->foreignId('sekolah_id')->nullable()->change();

            $table->enum('moda', ['jalan_kaki', 'kendaraan', 'perahu'])
                  ->after('jarak')
                  ->comment("Moda perjalanan untuk baris ini SAJA — setiap moda adalah baris terpisah, tidak digabung");

            $table->enum('segmen', ['langsung', 'ke_dermaga', 'penyeberangan', 'dermaga_ke_sekolah'])
                  ->default('langsung')
                  ->after('moda')
                  ->comment("langsung=desa->sekolah utuh; ke_dermaga=desa->dermaga; penyeberangan=dermaga->dermaga (perahu); dermaga_ke_sekolah=dermaga->sekolah setelah menyeberang");

            $table->decimal('waktu_tempuh_mnt', 8, 2)->nullable()->after('segmen')
                  ->comment('Estimasi waktu tempuh (menit) untuk moda pada baris ini saja');

            $table->string('tujuan_label')->nullable()->after('waktu_tempuh_mnt')
                  ->comment('Label tujuan human-readable saat sekolah_id NULL, mis. "Dermaga Pulau Bungin"');
        });

        // Kolom lama sudah tidak relevan dengan struktur baru (satu baris = satu moda):
        // walk_mnt/drive_mnt/boat_mnt digantikan waktu_tempuh_mnt + moda,
        // jarak_laut sudah tercakup langsung di kolom jarak pada baris bermoda 'perahu',
        // mode_transport digantikan kolom moda+segmen yang lebih granular.
        Schema::table('jaraksekolahlokasi', function (Blueprint $table) {
            $table->dropColumn(['walk_mnt', 'drive_mnt', 'boat_mnt', 'jarak_laut', 'mode_transport']);
        });
    }

    public function down(): void
    {
        Schema::table('jaraksekolahlokasi', function (Blueprint $table) {
            $table->dropColumn(['moda', 'segmen', 'waktu_tempuh_mnt', 'tujuan_label']);

            $table->decimal('walk_mnt', 8, 2)->nullable()->after('jarak');
            $table->decimal('drive_mnt', 8, 2)->nullable()->after('walk_mnt');
            $table->decimal('boat_mnt', 8, 2)->nullable()->after('drive_mnt');
            $table->decimal('jarak_laut', 8, 3)->nullable()->after('boat_mnt');
            $table->enum('mode_transport', ['darat', 'multimoda'])->default('darat')->after('jarak_laut');

            $table->foreignId('sekolah_id')->nullable(false)->change();
        });
    }
};
