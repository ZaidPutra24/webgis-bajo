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
        $sekolahs = Sekolah::with(['semuaJarakLokasi', 'jenjang', 'statistik', 'utilitas'])->get();

        $totalWilayah = $wilayahs->count();
        $totalSekolah = $sekolahs->count();

        $totalBajo = $sekolahs->filter(function ($item) {
            return str_contains(strtolower($item->nama_sekolah ?? ''), 'bajo');
        })->count();

        $sdCount  = $sekolahs->whereIn('jenjang_id', [1, 2])->count();  // SD + MI
        $smpCount = $sekolahs->whereIn('jenjang_id', [3, 4])->count();  // SMP + MTS
        $smaCount = $sekolahs->whereIn('jenjang_id', [5, 6, 7])->count(); // SMA + MA + SMK

        $persenSd  = $totalSekolah > 0 ? round(($sdCount  / $totalSekolah) * 100) : 0;
        $persenSmp = $totalSekolah > 0 ? round(($smpCount / $totalSekolah) * 100) : 0;
        $persenSma = $totalSekolah > 0 ? round(($smaCount / $totalSekolah) * 100) : 0;

        return view('welcome', compact(
            'wilayahs',
            'sekolahs',
            'jenjangs',
            'totalWilayah',
            'totalSekolah',
            'totalBajo',
            'persenSd',
            'persenSmp',
            'persenSma'
        ));
    }
}
