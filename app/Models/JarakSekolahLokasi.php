<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class JarakSekolahLokasi extends Model
{
    protected $table = 'jaraksekolahlokasi';

    /**
     * Kolom yang dapat diisi massal.
     * Struktur baru (lihat migration restructure_jaraksekolahlokasi_pisah_moda):
     * satu baris = satu leg perjalanan dalam SATU moda saja.
     */
    protected $fillable = [
        'sekolah_id',
        'wilayah_id',
        'jarak',
        'moda',
        'segmen',
        'waktu_tempuh_mnt',
        'tujuan_label',
        'route_geojson',
    ];

    protected $casts = [
        'jarak'            => 'decimal:3',
        'waktu_tempuh_mnt' => 'decimal:2',
        'moda'             => 'string',
        'segmen'           => 'string',
        // route_geojson dibiarkan string; parse manual JSON::decode di controller/view
    ];

    // -------------------------------------------------------------------------
    // RELASI
    // -------------------------------------------------------------------------

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function wilayahDesa()
    {
        return $this->belongsTo(WilayahDesa::class, 'wilayah_id');
    }

    // -------------------------------------------------------------------------
    // LOCAL SCOPES
    // -------------------------------------------------------------------------

    /** Filter berdasarkan moda tertentu: 'jalan_kaki' | 'kendaraan' | 'perahu'. */
    public function scopeModa(Builder $query, string $moda): Builder
    {
        return $query->where('moda', $moda);
    }

    /** Filter berdasarkan jenis segmen perjalanan. */
    public function scopeSegmen(Builder $query, string $segmen): Builder
    {
        return $query->where('segmen', $segmen);
    }

    /** Leg utuh desa->sekolah dalam satu jalur (tanpa transit dermaga/perahu). */
    public function scopeLangsung(Builder $query): Builder
    {
        return $query->where('segmen', 'langsung');
    }

    /** Semua leg yang merupakan bagian dari perjalanan multimoda (desa pulau ke sekolah daratan). */
    public function scopeMultimoda(Builder $query): Builder
    {
        return $query->whereIn('segmen', ['ke_dermaga', 'penyeberangan', 'dermaga_ke_sekolah']);
    }

    public function scopeByWilayah(Builder $query, int $wilayahId): Builder
    {
        return $query->where('wilayah_id', $wilayahId);
    }

    public function scopeBySekolah(Builder $query, int $sekolahId): Builder
    {
        return $query->where('sekolah_id', $sekolahId);
    }

    public function scopeTerdekat(Builder $query): Builder
    {
        return $query->orderBy('jarak');
    }

    // -------------------------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------------------------

    public function getRouteArrayAttribute(): ?array
    {
        return $this->route_geojson
            ? json_decode($this->route_geojson, true)
            : null;
    }

    /** Label human-readable moda transportasi. */
    public function getModaLabelAttribute(): string
    {
        return match ($this->moda) {
            'jalan_kaki' => 'Jalan Kaki',
            'kendaraan'  => 'Kendaraan',
            'perahu'     => 'Perahu/Speedboat',
            default      => ucfirst($this->moda),
        };
    }

    /** Label tujuan: nama sekolah jika ada, kalau tidak pakai tujuan_label (mis. nama dermaga). */
    public function getTujuanAttribute(): ?string
    {
        return $this->sekolah->nama_sekolah ?? $this->tujuan_label;
    }

    /** Waktu tempuh diformat sebagai jam:menit. */
    public function getWaktuLabelAttribute(): ?string
    {
        if ($this->waktu_tempuh_mnt === null) return null;
        $jam = intdiv((int) $this->waktu_tempuh_mnt, 60);
        $mnt = (int) $this->waktu_tempuh_mnt % 60;
        return $jam > 0 ? "{$jam} jam {$mnt} menit" : "{$mnt} menit";
    }

    // -------------------------------------------------------------------------
    // PENYUSUN RUTE (gabungan leg -> opsi perjalanan desa -> sekolah)
    // -------------------------------------------------------------------------

    /**
     * Kolom ringan untuk keperluan analisis (tanpa route_geojson yang besar).
     */
    public const KOLOM_RINGAN = [
        'id', 'sekolah_id', 'wilayah_id', 'jarak', 'moda', 'segmen',
        'waktu_tempuh_mnt', 'tujuan_label',
    ];

    /**
     * Susun baris-baris jaraksekolahlokasi SATU desa menjadi daftar OPSI
     * perjalanan per sekolah, karena struktur tabel sekarang "1 baris = 1 leg
     * dalam 1 moda":
     *
     *  - segmen 'langsung'           -> 1 opsi (tipe 'langsung') per moda.
     *  - segmen 'dermaga_ke_sekolah' -> digabung dengan leg 'ke_dermaga' (moda
     *    darat yang sama) + leg 'penyeberangan' (perahu) menjadi 1 opsi
     *    (tipe 'multimoda') per moda darat. Jarak & waktu = jumlah semua leg.
     *
     * Leg 'ke_dermaga' dipilih yang tujuannya sama dengan dermaga asal
     * penyeberangan (label sebelum " -> "), sehingga jalur alternatif/ambigu
     * (mis. "jalur darat arah Tinanggea") tidak ikut terjumlah.
     *
     * @param  Collection  $rows  baris JarakSekolahLokasi milik SATU wilayah_id
     * @return Collection  keyed by sekolah_id => list opsi:
     *   ['tipe','moda','jarak_km','waktu_mnt','boat_km','boat_mnt','pakai_perahu','leg_ids']
     */
    public static function susunOpsiRute(Collection $rows): Collection
    {
        $opsi = collect();
        $tambah = function (int $sekolahId, array $o) use (&$opsi) {
            $list = $opsi->get($sekolahId, []);
            $list[] = $o;
            $opsi->put($sekolahId, $list);
        };

        // 1) Rute langsung
        foreach ($rows->where('segmen', 'langsung')->whereNotNull('sekolah_id')->groupBy('sekolah_id') as $sid => $group) {
            foreach ($group->groupBy('moda') as $moda => $legs) {
                $leg = $legs->sortBy('waktu_tempuh_mnt')->first();
                if ($leg->waktu_tempuh_mnt === null) {
                    continue;
                }
                $tambah((int) $sid, [
                    'tipe'         => 'langsung',
                    'moda'         => (string) $moda,
                    'jarak_km'     => (float) $leg->jarak,
                    'waktu_mnt'    => (float) $leg->waktu_tempuh_mnt,
                    'boat_km'      => 0.0,
                    'boat_mnt'     => 0.0,
                    'pakai_perahu' => $moda === 'perahu',
                    'leg_ids'      => [(int) $leg->id],
                ]);
            }
        }

        // 2) Rute multimoda (ke_dermaga + penyeberangan + dermaga_ke_sekolah)
        $penyeberangan = $rows->where('segmen', 'penyeberangan')->where('moda', 'perahu')->sortBy('waktu_tempuh_mnt')->first();
        $tail = $rows->where('segmen', 'dermaga_ke_sekolah')->whereNotNull('sekolah_id');

        if ($penyeberangan && $tail->isNotEmpty()) {
            $dermagaAsal = trim(explode('->', (string) $penyeberangan->tujuan_label)[0]);
            $keDermaga = $rows->where('segmen', 'ke_dermaga');

            foreach ($tail->groupBy('sekolah_id') as $sid => $group) {
                foreach ($group->groupBy('moda') as $moda => $legs) {
                    $akhir = $legs->sortBy('waktu_tempuh_mnt')->first();

                    $awalKandidat = $keDermaga->where('moda', $moda);
                    $awal = $awalKandidat->first(fn ($r) => $dermagaAsal !== '' && str_starts_with((string) $r->tujuan_label, $dermagaAsal));
                    $awal = $awal ?: $awalKandidat->sortBy('waktu_tempuh_mnt')->first();
                    if (!$awal || $akhir->waktu_tempuh_mnt === null || $awal->waktu_tempuh_mnt === null) {
                        continue;
                    }

                    $tambah((int) $sid, [
                        'tipe'         => 'multimoda',
                        'moda'         => (string) $moda,
                        'jarak_km'     => (float) $awal->jarak + (float) $penyeberangan->jarak + (float) $akhir->jarak,
                        'waktu_mnt'    => (float) $awal->waktu_tempuh_mnt + (float) $penyeberangan->waktu_tempuh_mnt + (float) $akhir->waktu_tempuh_mnt,
                        'boat_km'      => (float) $penyeberangan->jarak,
                        'boat_mnt'     => (float) $penyeberangan->waktu_tempuh_mnt,
                        'pakai_perahu' => true,
                        'leg_ids'      => [(int) $awal->id, (int) $penyeberangan->id, (int) $akhir->id],
                    ]);
                }
            }
        }

        return $opsi;
    }

    /**
     * Pilih satu opsi dari daftar opsi satu sekolah. Default memprioritaskan
     * jalan kaki (skenario siswa tanpa kendaraan = kondisi terburuk yang
     * relevan untuk analisis akses), fallback ke opsi tercepat.
     */
    public static function pilihOpsi(array $opsiSekolah, string $modaUtama = 'jalan_kaki'): ?array
    {
        if (empty($opsiSekolah)) {
            return null;
        }
        $utama = array_values(array_filter($opsiSekolah, fn ($o) => $o['moda'] === $modaUtama));
        $pool = $utama ?: $opsiSekolah;
        usort($pool, fn ($a, $b) => $a['waktu_mnt'] <=> $b['waktu_mnt']);

        return $pool[0];
    }

    /** Label sumber jarak yang ramah dibaca, mis. "riil (jalan kaki + perahu)". */
    public static function labelOpsi(array $opsi): string
    {
        $moda = match ($opsi['moda']) {
            'jalan_kaki' => 'jalan kaki',
            'kendaraan'  => 'kendaraan',
            'perahu'     => 'perahu',
            default      => $opsi['moda'],
        };

        return 'riil (' . $moda . (($opsi['tipe'] === 'multimoda') ? ' + perahu' : '') . ')';
    }

    // -------------------------------------------------------------------------
    // HELPER STATIS
    // -------------------------------------------------------------------------

    public static function hitungWalkMnt(float $jarakKm): float
    {
        return round(($jarakKm / 5) * 60, 2);
    }

    public static function hitungDriveMnt(float $jarakKm): float
    {
        return round(($jarakKm / 30) * 60, 2);
    }

    public static function hitungBoatMnt(float $jarakLautKm): float
    {
        return round(($jarakLautKm / 25) * 60, 2);
    }
}
