<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class JarakSekolahLokasi extends Model
{
    protected $table = 'jaraksekolahlokasi';

    /**
     * Kolom yang dapat diisi massal.
     * Ditambahkan: walk_mnt, drive_mnt, boat_mnt, jarak_laut, mode_transport, route_geojson
     */
    protected $fillable = [
        'sekolah_id',
        'wilayah_id',
        'jarak',
        'walk_mnt',
        'drive_mnt',
        'boat_mnt',
        'jarak_laut',
        'mode_transport',
        'route_geojson',
    ];

    /**
     * Cast otomatis tipe data kolom.
     */
    protected $casts = [
        'jarak'         => 'decimal:3',
        'walk_mnt'      => 'decimal:2',
        'drive_mnt'     => 'decimal:2',
        'boat_mnt'      => 'decimal:2',
        'jarak_laut'    => 'decimal:3',
        'mode_transport' => 'string',
        // route_geojson dibiarkan string; parse manual JSON::decode di controller/view
    ];

    // -------------------------------------------------------------------------
    // RELASI
    // -------------------------------------------------------------------------

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    /**
     * Relasi ke WilayahDesa — nama diseragamkan ke wilayahDesa().
     */
    public function wilayahDesa()
    {
        return $this->belongsTo(WilayahDesa::class, 'wilayah_id');
    }

    // -------------------------------------------------------------------------
    // LOCAL SCOPES
    // -------------------------------------------------------------------------

    /**
     * Filter hanya rute darat murni.
     */
    public function scopeDarat(Builder $query): Builder
    {
        return $query->where('mode_transport', 'darat');
    }

    /**
     * Filter hanya rute multimoda (darat + perahu).
     */
    public function scopeMultimoda(Builder $query): Builder
    {
        return $query->where('mode_transport', 'multimoda');
    }

    /**
     * Filter berdasarkan wilayah/desa tertentu.
     */
    public function scopeByWilayah(Builder $query, int $wilayahId): Builder
    {
        return $query->where('wilayah_id', $wilayahId);
    }

    /**
     * Filter berdasarkan sekolah tertentu.
     */
    public function scopeBySekolah(Builder $query, int $sekolahId): Builder
    {
        return $query->where('sekolah_id', $sekolahId);
    }

    /**
     * Urutkan dari jarak terdekat.
     */
    public function scopeTerdekat(Builder $query): Builder
    {
        return $query->orderBy('jarak');
    }

    // -------------------------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------------------------

    /**
     * Kembalikan route_geojson sebagai array PHP (sudah di-decode dari JSON).
     * Gunakan: $jarak->route_array
     */
    public function getRouteArrayAttribute(): ?array
    {
        return $this->route_geojson
            ? json_decode($this->route_geojson, true)
            : null;
    }

    /**
     * Label human-readable mode transportasi.
     * Gunakan: $jarak->mode_label
     */
    public function getModeLabelAttribute(): string
    {
        return match ($this->mode_transport) {
            'multimoda' => 'Darat + Perahu',
            default     => 'Darat',
        };
    }

    /**
     * Waktu tempuh terlama (jalan kaki) diformat sebagai jam:menit.
     * Gunakan: $jarak->walk_label
     */
    public function getWalkLabelAttribute(): ?string
    {
        if ($this->walk_mnt === null) return null;
        $jam  = intdiv((int) $this->walk_mnt, 60);
        $mnt  = (int) $this->walk_mnt % 60;
        return $jam > 0
            ? "{$jam} jam {$mnt} menit"
            : "{$mnt} menit";
    }

    /**
     * Waktu tempuh berkendara diformat sebagai jam:menit.
     * Gunakan: $jarak->drive_label
     */
    public function getDriveLabelAttribute(): ?string
    {
        if ($this->drive_mnt === null) return null;
        $jam = intdiv((int) $this->drive_mnt, 60);
        $mnt = (int) $this->drive_mnt % 60;
        return $jam > 0
            ? "{$jam} jam {$mnt} menit"
            : "{$mnt} menit";
    }

    /**
     * Waktu perahu diformat sebagai jam:menit (null jika rute darat murni).
     * Gunakan: $jarak->boat_label
     */
    public function getBoatLabelAttribute(): ?string
    {
        if ($this->boat_mnt === null) return null;
        $jam = intdiv((int) $this->boat_mnt, 60);
        $mnt = (int) $this->boat_mnt % 60;
        return $jam > 0
            ? "{$jam} jam {$mnt} menit"
            : "{$mnt} menit";
    }

    // -------------------------------------------------------------------------
    // HELPER STATIS
    // -------------------------------------------------------------------------

    /**
     * Hitung walk_mnt dari jarak (km) dengan kecepatan 5 km/jam.
     */
    public static function hitungWalkMnt(float $jarakKm): float
    {
        return round(($jarakKm / 5) * 60, 2);
    }

    /**
     * Hitung drive_mnt dari jarak (km) dengan kecepatan 30 km/jam.
     */
    public static function hitungDriveMnt(float $jarakKm): float
    {
        return round(($jarakKm / 30) * 60, 2);
    }

    /**
     * Hitung boat_mnt dari jarak laut (km) dengan kecepatan 25 km/jam.
     */
    public static function hitungBoatMnt(float $jarakLautKm): float
    {
        return round(($jarakLautKm / 25) * 60, 2);
    }
}