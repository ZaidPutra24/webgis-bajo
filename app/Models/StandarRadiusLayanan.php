<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabel referensi standar radius pelayanan sekolah per jenjang.
 * Sumber awal: SNI 03-1733-2004 (bisa diedit admin). Dipakai sebagai
 * cross-check SEKUNDER — untuk desa pesisir/kepulauan Bajo, status
 * "terlayani/tidak" tetap diprioritaskan dari waktu tempuh riil
 * (walk_mnt/drive_mnt/boat_mnt) bila datanya tersedia.
 */
class StandarRadiusLayanan extends Model
{
    protected $table = 'standar_radius_layanan';

    protected $fillable = [
        'jenjang_id',
        'radius_meter',
        'penduduk_pendukung_ideal',
        'sumber',
        'keterangan',
    ];

    protected $casts = [
        'radius_meter'             => 'integer',
        'penduduk_pendukung_ideal' => 'integer',
    ];

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }

    public function getRadiusKmAttribute(): float
    {
        return round($this->radius_meter / 1000, 2);
    }
}
