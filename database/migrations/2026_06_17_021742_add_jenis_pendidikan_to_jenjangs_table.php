<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenjang', function (Blueprint $table) {
            if (!Schema::hasColumn('jenjang', 'jenis_pendidikan')) {
                $table->string('jenis_pendidikan', 50)->nullable()->after('nama_jenjang');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jenjang', function (Blueprint $table) {
            if (Schema::hasColumn('jenjang', 'jenis_pendidikan')) {
                $table->dropColumn('jenis_pendidikan');
            }
        });
    }
};