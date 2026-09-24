<?php

namespace App\Http\Controllers;

use App\Support\JarakKondisiAnalyzer;
use Illuminate\Http\Request;

/**
 * Analisis Hubungan Jarak/Keterpencilan dengan Kondisi Sekolah (poin 4).
 * Logika inti ada di App\Support\JarakKondisiAnalyzer — lihat docblock di
 * sana untuk dasar rujukan tiap ambang yang dipakai.
 */
class JarakKondisiController extends Controller
{
    public function index(Request $request, JarakKondisiAnalyzer $analyzer)
    {
        $ambangJauh = (float) $request->query('ambang', JarakKondisiAnalyzer::AMBANG_JAUH_MENIT);

        $hasil = $analyzer->analisis($ambangJauh);

        // Urutkan tabel: beban ganda dulu, lalu makin terpencil, lalu makin
        // banyak masalah terkonfirmasi — supaya kasus paling perlu perhatian
        // langsung terlihat di atas.
        $prioritasKelompok = ['kepulauan' => 3, 'jauh' => 2, 'mudah' => 1, 'tidak_ada_data' => 0];
        $sekolah = $hasil['sekolah']->sort(function ($a, $b) use ($prioritasKelompok) {
            $skorA = ($a['beban_ganda'] ? 100 : 0) + $prioritasKelompok[$a['kelompok_keterpencilan']] * 10 + $a['jumlah_masalah'];
            $skorB = ($b['beban_ganda'] ? 100 : 0) + $prioritasKelompok[$b['kelompok_keterpencilan']] * 10 + $b['jumlah_masalah'];

            return $skorB <=> $skorA;
        })->values();

        return view('admin.jarak-kondisi.index', [
            'sekolah'           => $sekolah,
            'ringkasanKelompok' => $hasil['ringkasanKelompok'],
            'totalSekolah'      => $hasil['totalSekolah'],
            'totalBebanGanda'   => $hasil['totalBebanGanda'],
            'ambangJauh'        => $hasil['ambangJauh'],
        ]);
    }
}
