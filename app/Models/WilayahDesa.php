<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WilayahDesa extends Model
{
    // Mengarahkan ke nama tabel yang benar di MySQL
    protected $table = 'wilayah_desa';

    // Kolom yang diizinkan untuk diisi massal
    protected $fillable = [
        'nama_wilayah',
        'geojson',
        'luas_wilayah',
        'gambar',
        'penduduk_usia_sekolah_l',
        'penduduk_usia_sekolah_p',
    ];
}