<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\WilayahDesa;
use App\Models\WilayahKecamatan;
use App\Models\Jenjang;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $wilayahs = WilayahDesa::all();
        $jenjangs = Jenjang::all();
        $kecamatans = WilayahKecamatan::all();

        // Semua sekolah untuk statistik panel (termasuk yang tanpa koordinat)
        $sekolahsAll = Sekolah::with(['jenjang', 'statistik'])->get();

        // Untuk peta: hanya sekolah yang punya koordinat
        $sekolahs = Sekolah::with(['semuaJarakLokasi', 'jenjang', 'statistik', 'utilitas'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // ── ROI KECAMATAN: hitung jumlah sekolah + breakdown jenis (jenjang) sekolah
        // dalam jangkauan tiap kecamatan ── Menggunakan point-in-polygon (GeoHelper)
        // terhadap koordinat sekolah yang sudah dimuat di atas (dengan relasi jenjang
        // sudah di-eager-load), sehingga tidak perlu query ulang per kecamatan.
        $kecamatans->each(function ($kec) use ($sekolahs) {
            $sekolahDalamRoi = $kec->daftarSekolahDalamRoi($sekolahs);

            $kec->jumlah_sekolah = $sekolahDalamRoi->count();

            // Jumlah sekolah per jenis jenjang (mis. SD: 5, SMP: 3, SMA: 2)
            $kec->sekolah_by_jenjang = $sekolahDalamRoi
                ->groupBy(fn($s) => $s->jenjang->nama_jenjang ?? 'Lainnya')
                ->map(fn($group) => $group->count())
                ->sortDesc();

            // Jumlah jenis/tingkat sekolah yang ada di kecamatan ini (mis. 3 jenis: SD, SMP, SMA)
            $kec->jumlah_jenis_sekolah = $kec->sekolah_by_jenjang->count();
        });

        $totalWilayah = $wilayahs->count();
        $totalKecamatan = $kecamatans->count();
        $totalSekolah = $sekolahsAll->count();

        // Total siswa dari SEMUA sekolah (bukan hanya yang berkoordinat)
        $totalSiswa = $sekolahsAll->reduce(
            fn($carry, $s) => $carry + ($s->statistik->jumlah_siswa ?? 0), 0
        );

        return view('welcome', compact(
            'wilayahs',
            'kecamatans',
            'sekolahs',
            'sekolahsAll',
            'jenjangs',
            'totalWilayah',
            'totalKecamatan',
            'totalSekolah',
            'totalSiswa'
        ));
    }
}