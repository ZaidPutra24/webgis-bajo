<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KurikulumUtilitas extends Model
{
    protected $table = 'kurikulum_utilitas';

    protected $fillable = [
        'sekolah_id',
        'kurikulum',
        'paket',
        'penyelenggara',
        'akses_internet',
        'sumber_listrik',
        'daya_listrik',
        'luas_tanah',
    ];

    /**
     * Cast kolom numerik agar perbandingan nilai tidak ambigu (int vs string).
     */
    protected $casts = [
        'daya_listrik' => 'integer',
        'luas_tanah'   => 'float',
    ];

    // Relasi balik ke Sekolah
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    /**
     * Label Paket A/B/C yang mudah dibaca (mis. "Paket C (Setara SMA)").
     * `kurikulum` tetap teks bebas untuk tampilan detail/histori — kolom
     * `paket` ('A'/'B'/'C') adalah nilai terstruktur untuk query & logika
     * (mis. fitur rekomendasi sekolah PKBM). Null jika belum diklasifikasi
     * (mis. teks kurikulum tidak menyebut paket secara eksplisit).
     */
    public function getPaketLabelAttribute(): ?string
    {
        return $this->paket ? (StandarRombelPaket::LABEL[$this->paket] ?? $this->paket) : null;
    }

    /**
     * Helper: kembalikan true jika semua field konten kosong / nol.
     * Berguna di Blade: @if($sekolah->utilitas && !$sekolah->utilitas->isEmpty())
     */
    public function isEmpty(): bool
    {
        $strings  = ['kurikulum', 'penyelenggara', 'akses_internet', 'sumber_listrik'];
        $numerics = ['daya_listrik', 'luas_tanah'];

        foreach ($strings as $f) {
            if (!is_null($this->$f) && trim($this->$f) !== '') return false;
        }
        foreach ($numerics as $f) {
            if (!is_null($this->$f) && (float) $this->$f !== 0.0) return false;
        }
        return true;
    }
}
