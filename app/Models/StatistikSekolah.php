<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatistikSekolah extends Model
{
    protected $table = 'statistik_sekolah';

    protected $fillable = [
        'sekolah_id',
        'siswa_l',
        'siswa_p',
        'jumlah_siswa',
        'daya_tampung',
        'jumlah_guru',
        'jumlah_rombel',
        'ruang_kelas',
        'laboratorium',
        'perpustakaan',
    ];

    // Relasi balik ke tabel sekolah
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }
}