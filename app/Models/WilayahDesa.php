<?php

namespace App\Models;

use App\Support\GeoHelper;
use Illuminate\Database\Eloquent\Model;

class WilayahDesa extends Model
{
    // Mengarahkan ke nama tabel yang benar di MySQL
    protected $table = 'wilayah_desa';

    // Kolom yang diizinkan untuk diisi massal
    protected $fillable = [
        'id', // FIX: wajib ada agar WilayahDesa::create() dengan id eksplisit (lihat
              // WilayahSeeder) tidak diabaikan mass assignment. Tanpa ini, id
              // auto-increment bisa bergeser dan merusak relasi wilayah_id di
              // jaraksekolahlokasi (lihat perbaikan yang sama di App\Models\Sekolah).
        'nama_wilayah',
        'geojson',
        'luas_wilayah',
        'gambar',
        'penduduk_usia_sekolah_l',
        'penduduk_usia_sekolah_p',
        'latitude_balai',
        'longitude_balai',
        'sumber_titik_balai',
    ];

    public function pendudukJenjang()
    {
        return $this->hasMany(PendudukJenjang::class, 'wilayah_id');
    }

    /**
     * Total penduduk usia sekolah (laki-laki + perempuan) di desa ini.
     * Gunakan: $desa->total_penduduk_usia_sekolah
     */
    public function getTotalPendudukUsiaSekolahAttribute(): int
    {
        return (int) ($this->penduduk_usia_sekolah_l ?? 0) + (int) ($this->penduduk_usia_sekolah_p ?? 0);
    }

    /**
     * Kepadatan penduduk usia sekolah = total penduduk usia sekolah / luas wilayah (km²).
     * Mengembalikan null jika luas wilayah tidak diisi atau nol (menghindari division by zero).
     * Gunakan: $desa->kepadatan_usia_sekolah
     */
    public function getKepadatanUsiaSekolahAttribute(): ?float
    {
        if (empty($this->luas_wilayah) || (float) $this->luas_wilayah <= 0) {
            return null;
        }

        return round($this->total_penduduk_usia_sekolah / (float) $this->luas_wilayah, 2);
    }

    /**
     * Hitung jumlah sekolah yang koordinatnya berada di dalam polygon (ROI) desa ini.
     * $sekolahs opsional: kirim koleksi yang sudah di-load sebelumnya agar tidak
     * query berulang saat dipanggil dalam loop (mis. di halaman kepadatan/index).
     */
    public function hitungJumlahSekolah($sekolahs = null): int
    {
        $sekolahs = $sekolahs ?? Sekolah::whereNotNull('latitude')->whereNotNull('longitude')->get();

        return GeoHelper::countPointsInGeoJson($this->geojson, $sekolahs);
    }

    /**
     * Ambil daftar sekolah (Collection) yang berada di dalam polygon (ROI) desa ini.
     */
    public function daftarSekolahDalamRoi($sekolahs = null)
    {
        $sekolahs = $sekolahs ?? Sekolah::with('jenjang')
            ->whereNotNull('latitude')->whereNotNull('longitude')->get();

        return GeoHelper::filterPointsInGeoJson($this->geojson, $sekolahs);
    }

    /**
     * Titik "balai desa" representatif: pakai latitude_balai/longitude_balai
     * kalau sudah diisi (manual override oleh admin), kalau belum jatuh
     * balik ke centroid otomatis dari polygon geojson desa.
     * Gunakan: $desa->titik_balai => ['latitude' => ..., 'longitude' => ..., 'sumber' => 'manual'|'centroid']
     */
    public function getTitikBalaiAttribute(): ?array
    {
        if ($this->latitude_balai !== null && $this->longitude_balai !== null) {
            return [
                'latitude'  => (float) $this->latitude_balai,
                'longitude' => (float) $this->longitude_balai,
                'sumber'    => $this->sumber_titik_balai ?? 'manual',
            ];
        }

        $centroid = GeoHelper::centroid($this->geojson);

        return $centroid ? [
            'latitude'  => $centroid['latitude'],
            'longitude' => $centroid['longitude'],
            'sumber'    => 'centroid',
        ] : null;
    }

    /**
     * Cari sekolah TERDEKAT dari titik balai desa untuk satu jenjang tertentu
     * (mis. semua sekolah dengan jenjang_id = SD).
     *
     * Prioritas jarak yang dipakai:
     *  1. Jika ada catatan rute riil di jaraksekolahlokasi untuk pasangan
     *     (desa, sekolah) tersebut -> pakai itu (struktur baru: moda +
     *     segmen + waktu_tempuh_mnt; leg multimoda digabung lewat
     *     JarakSekolahLokasi::susunOpsiRute) karena mencerminkan realita
     *     lapangan (Bajo pesisir). Moda utama: jalan kaki.
     *  2. Kalau belum ada -> fallback ke jarak garis lurus (haversine) dari
     *     titik balai desa, sebagai cross-check sekunder saja.
     *
     * Mengembalikan array berisi data sekolah + jarak, atau null jika tidak
     * ada sekolah jenjang tsb / titik balai desa tidak diketahui.
     */
    public function sekolahTerdekat(int $jenjangId, $sekolahsJenjang = null): ?array
    {
        $balai = $this->titik_balai;
        if (!$balai) {
            return null;
        }

        $sekolahsJenjang = $sekolahsJenjang ?? Sekolah::where('jenjang_id', $jenjangId)
            ->whereNotNull('latitude')->whereNotNull('longitude')->get();

        if ($sekolahsJenjang->isEmpty()) {
            return null;
        }

        // Ambil data jarak riil desa ini (struktur baru: 1 baris = 1 leg 1 moda)
        // lalu susun jadi opsi perjalanan per sekolah (leg multimoda digabung).
        $opsiRiil = JarakSekolahLokasi::susunOpsiRute(
            JarakSekolahLokasi::select(JarakSekolahLokasi::KOLOM_RINGAN)
                ->where('wilayah_id', $this->id)
                ->get()
        );

        $terdekat = null;

        foreach ($sekolahsJenjang as $sekolah) {
            $riil = JarakSekolahLokasi::pilihOpsi($opsiRiil->get($sekolah->id, []));

            if ($riil !== null) {
                $jarakKm = $riil['jarak_km'];
                $sumberJarak = JarakSekolahLokasi::labelOpsi($riil);
                $waktuTempuhMnt = $riil['waktu_mnt'];
            } else {
                $jarakKm = GeoHelper::haversineKm(
                    $balai['latitude'], $balai['longitude'],
                    (float) $sekolah->latitude, (float) $sekolah->longitude
                );
                $sumberJarak = 'garis lurus (estimasi)';
                $waktuTempuhMnt = JarakSekolahLokasi::hitungWalkMnt($jarakKm);
            }

            if ($terdekat === null || $jarakKm < $terdekat['jarak_km']) {
                $terdekat = [
                    'sekolah_id'       => $sekolah->id,
                    'nama_sekolah'     => $sekolah->nama_sekolah,
                    'jarak_km'         => round($jarakKm, 3),
                    'sumber_jarak'     => $sumberJarak,
                    'waktu_tempuh_mnt' => round($waktuTempuhMnt, 1),
                ];
            }
        }

        return $terdekat;
    }
}