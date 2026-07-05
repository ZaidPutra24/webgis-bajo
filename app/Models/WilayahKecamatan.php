<?php

namespace App\Models;

use App\Support\GeoHelper;
use Illuminate\Database\Eloquent\Model;

class WilayahKecamatan extends Model
{
    protected $table = 'wilayah_kecamatan';

    protected $fillable = [
        'nama_kecamatan',
        'kabupaten',
        'provinsi',
        'geojson',
        'luas_wilayah',
        'warna',
    ];

    /**
     * Hitung jumlah sekolah yang koordinatnya berada di dalam ROI kecamatan ini.
     * $sekolahs opsional: kirim koleksi yang sudah di-load sebelumnya agar tidak
     * query berulang saat dipanggil dalam loop (mis. di halaman index/peta).
     */
    public function hitungJumlahSekolah($sekolahs = null): int
    {
        $sekolahs = $sekolahs ?? Sekolah::whereNotNull('latitude')->whereNotNull('longitude')->get();

        return GeoHelper::countPointsInGeoJson($this->geojson, $sekolahs);
    }

    /**
     * Ambil daftar sekolah (Collection) yang berada di dalam ROI kecamatan ini.
     */
    public function daftarSekolahDalamRoi($sekolahs = null)
    {
        $sekolahs = $sekolahs ?? Sekolah::with('jenjang')
            ->whereNotNull('latitude')->whereNotNull('longitude')->get();

        return GeoHelper::filterPointsInGeoJson($this->geojson, $sekolahs);
    }
}
