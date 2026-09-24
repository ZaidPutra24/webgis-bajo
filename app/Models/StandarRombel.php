<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabel referensi standar maksimum murid per rombel, per jenjang.
 * Sumber awal: Kepmendikdasmen No. 14 Tahun 2026 (bisa diedit admin).
 */
class StandarRombel extends Model
{
    protected $table = 'standar_rombel';

    protected $fillable = [
        'jenjang_id',
        'maks_murid_per_rombel',
        'dasar_hukum',
        'nomor_dokumen',
        'keterangan',
    ];

    protected $casts = [
        'maks_murid_per_rombel' => 'integer',
    ];

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }
}
