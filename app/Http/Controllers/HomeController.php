<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\WilayahDesa;
use App\Models\Jenjang;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $wilayahs = WilayahDesa::all();
        $jenjangs = Jenjang::all();

        // Semua sekolah untuk statistik panel (termasuk yang tanpa koordinat)
        $sekolahsAll = Sekolah::with(['jenjang', 'statistik'])->get();

        // Untuk peta: hanya sekolah yang punya koordinat
        $sekolahs = Sekolah::with(['semuaJarakLokasi', 'jenjang', 'statistik', 'utilitas'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $totalWilayah = $wilayahs->count();
        $totalSekolah = $sekolahsAll->count();

        // Total siswa dari SEMUA sekolah (bukan hanya yang berkoordinat)
        $totalSiswa = $sekolahsAll->reduce(
            fn($carry, $s) => $carry + ($s->statistik->jumlah_siswa ?? 0), 0
        );

        return view('welcome', compact(
            'wilayahs',
            'sekolahs',
            'sekolahsAll',
            'jenjangs',
            'totalWilayah',
            'totalSekolah',
            'totalSiswa'
        ));
    }
}
