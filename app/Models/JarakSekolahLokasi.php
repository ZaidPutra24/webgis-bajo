<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JarakSekolahLokasi extends Model
{
    protected $table = 'jaraksekolahlokasi';
    protected $fillable = ['sekolah_id', 'wilayah_id', 'jarak'];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    // FIX: Nama method diseragamkan dengan relasi yang dipakai di controller
    public function wilayahDesa()
    {
        return $this->belongsTo(WilayahDesa::class, 'wilayah_id');
    }
}
