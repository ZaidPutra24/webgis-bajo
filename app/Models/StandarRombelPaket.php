<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabel referensi standar maksimum murid per rombel, KHUSUS Paket A/B/C
 * (kesetaraan di PKBM). Terpisah dari StandarRombel (yang di-keyed per
 * jenjang_id) karena PKBM hanya 1 jenjang_id tapi punya 3 standar berbeda.
 */
class StandarRombelPaket extends Model
{
    protected $table = 'standar_rombel_paket';

    protected $fillable = [
        'paket',
        'maks_murid_per_rombel',
        'dasar_hukum',
        'nomor_dokumen',
        'keterangan',
    ];

    protected $casts = [
        'maks_murid_per_rombel' => 'integer',
    ];

    public const LABEL = [
        'A' => 'Paket A (Setara SD)',
        'B' => 'Paket B (Setara SMP)',
        'C' => 'Paket C (Setara SMA)',
    ];

    public function getLabelAttribute(): string
    {
        return self::LABEL[$this->paket] ?? $this->paket;
    }
}
