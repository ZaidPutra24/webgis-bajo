<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Kebutuhan PKBM (Paket A/B/C) per desa — jumlah BPB (Belum Pernah
 * Bersekolah) usia 19-24 & 25+ yang sudah lewat usia sekolah formal.
 *
 * SENGAJA TERPISAH dari PendudukJenjang: lihat komentar migration
 * create_kebutuhan_pkbm_table untuk alasan lengkapnya (PKBM tidak punya
 * kohort usia sekolah formal seperti PAUD/SD/SMP/SMA).
 */
class KebutuhanPkbm extends Model
{
    protected $table = 'kebutuhan_pkbm';

    protected $fillable = [
        'wilayah_id',
        'bpb_19_24',
        'bpb_25_plus',
        'sumber_data',
    ];

    protected $casts = [
        'bpb_19_24'   => 'integer',
        'bpb_25_plus' => 'integer',
    ];

    public function wilayahDesa()
    {
        return $this->belongsTo(WilayahDesa::class, 'wilayah_id');
    }

    /**
     * Total kebutuhan PKBM (BPB 19-24 + BPB 25+) di desa ini.
     * CATATAN: ini TOTAL orang yang butuh kesetaraan, BUKAN breakdown per
     * Paket A/B/C — data sumber tidak memerincinya sampai level itu.
     */
    public function getTotalKebutuhanAttribute(): int
    {
        return (int) $this->bpb_19_24 + (int) $this->bpb_25_plus;
    }
}
