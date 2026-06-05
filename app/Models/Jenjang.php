<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jenjang extends Model
{
    // Tambahkan baris ini untuk mengunci nama tabel database asli Anda
    protected $table = 'jenjang';

    protected $fillable = [
        'nama_jenjang',
        'kode'
    ];
}
