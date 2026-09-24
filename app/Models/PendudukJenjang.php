<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendudukJenjang extends Model
{
    protected $table = 'penduduk_jenjang';

    protected $fillable = [
        'wilayah_id',
        'jenjang_id',
        'penduduk_l',
        'penduduk_p',
        'putus_sekolah_l',
        'putus_sekolah_p',
        'sumber_data',
    ];

    protected $casts = [
        'penduduk_l'      => 'integer',
        'penduduk_p'      => 'integer',
        'putus_sekolah_l' => 'integer',
        'putus_sekolah_p' => 'integer',
    ];

    public function wilayahDesa()
    {
        return $this->belongsTo(WilayahDesa::class, 'wilayah_id');
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }

    /**
     * Total penduduk usia sekolah jenjang ini (L + P). Ini adalah TOTAL
     * demografi usia jenjang tsb di desa (selaras dengan makna kolom agregat
     * penduduk_usia_sekolah_l/p yang sudah ada di wilayah_desa) — dipakai
     * sebagai basis pembilang rumus kepadatan.
     */
    public function getTotalPendudukAttribute(): int
    {
        return (int) $this->penduduk_l + (int) $this->penduduk_p;
    }

    /**
     * Total Anak Tidak Sekolah / putus sekolah (L + P) dari data ATS.
     * Ini adalah SUBSET dari total_penduduk (bukan tambahan di luar itu) —
     * yaitu berapa dari total penduduk usia jenjang ini yang TIDAK/BELUM
     * bersekolah.
     */
    public function getTotalPutusSekolahAttribute(): int
    {
        return (int) $this->putus_sekolah_l + (int) $this->putus_sekolah_p;
    }

    /**
     * Persentase Anak Tidak Sekolah / putus sekolah terhadap total penduduk
     * usia jenjang ini.
     */
    public function getPersenPutusSekolahAttribute(): ?float
    {
        $total = $this->total_penduduk;
        if ($total <= 0) {
            return null;
        }
        return round(($this->total_putus_sekolah / $total) * 100, 2);
    }
}
