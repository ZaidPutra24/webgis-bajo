<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table = 'sekolah';

    protected $fillable = [
        'id', // FIX: wajib ada agar Sekolah::create() dengan id eksplisit (lihat SekolahSeeder)
              // tidak diabaikan oleh mass assignment. Tanpa ini, id auto-increment akan
              // bergeser dan merusak relasi sekolah_id di jaraksekolahlokasi.
        'jenjang_id',
        'nama_sekolah',
        'npsn',
        'status',
        'alamat',
        'akreditasi',
        'latitude',
        'longitude',
        'img'
    ];

    // Relasi ke model Jenjang
    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }

    // Relasi One-to-One ke StatistikSekolah
    public function statistik()
    {
        return $this->hasOne(StatistikSekolah::class, 'sekolah_id');
    }

    // Relasi One-to-One ke KurikulumUtilitas
    public function utilitas()
    {
        return $this->hasOne(KurikulumUtilitas::class, 'sekolah_id', 'id');
    }

    // FIX: Relasi Many-to-Many ke WilayahDesa melalui tabel pivot jaraksekolahlokasi
    public function semuaJarakLokasi()
    {
        return $this->belongsToMany(WilayahDesa::class, 'jaraksekolahlokasi', 'sekolah_id', 'wilayah_id')
                    ->withPivot('jarak', 'moda', 'segmen', 'waktu_tempuh_mnt', 'tujuan_label', 'route_geojson');
    }
}
