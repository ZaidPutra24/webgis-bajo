<?php

namespace App\Http\Controllers;

use App\Models\PendudukJenjang;
use App\Models\WilayahDesa;
use Illuminate\Http\Request;

/**
 * Form input DATA SUMBER (bukan hasil hitung) untuk:
 *  - Penduduk Usia Jenjang (penduduk_l / penduduk_p)
 *  - ATS / Putus Sekolah   (putus_sekolah_l / putus_sekolah_p)
 * per desa, untuk 4 jenjang inti (PAUD, SD, SMP, SMA) yang dipakai fitur
 * School-Age Density.
 *
 * SENGAJA TIDAK ADA form untuk Kepadatan (jiwa/km²) — kepadatan adalah hasil
 * turunan (penduduk ÷ luas_wilayah), selalu dihitung ulang otomatis
 * (lihat WilayahDesa::getKepadatanUsiaSekolahAttribute() &
 * KepadatanUsiaSekolahController). Memberi form manual untuk kepadatan akan
 * menciptakan 2 sumber kebenaran yang bisa saling kontradiksi dengan data
 * penduduk/luas aslinya.
 */
class PendudukJenjangController extends Controller
{
    /**
     * Jenjang inti yang dientri lewat form ini, selaras dengan
     * KepadatanUsiaSekolahController::JENJANG_INTI.
     */
    protected const JENJANG_INTI = [
        10 => 'PAUD',
        3  => 'SD',
        5  => 'SMP',
        7  => 'SMA',
    ];

    public function index()
    {
        $wilayahs = WilayahDesa::orderBy('nama_wilayah')->get();

        $pendudukJenjang = PendudukJenjang::whereIn('jenjang_id', array_keys(self::JENJANG_INTI))
            ->get()
            ->groupBy('wilayah_id');

        $wilayahs->each(function (WilayahDesa $desa) use ($pendudukJenjang) {
            $rincian = $pendudukJenjang->get($desa->id, collect());
            $desa->jumlah_jenjang_terisi = $rincian->count();
            $desa->ada_data_dummy = $rincian->contains(fn ($p) => str_starts_with((string) $p->sumber_data, 'Dummy'));
        });

        return view('admin.penduduk-jenjang.index', compact('wilayahs'));
    }

    public function edit($wilayah_id)
    {
        $desa = WilayahDesa::findOrFail($wilayah_id);

        $existing = PendudukJenjang::where('wilayah_id', $wilayah_id)
            ->whereIn('jenjang_id', array_keys(self::JENJANG_INTI))
            ->get()
            ->keyBy('jenjang_id');

        $jenjangInti = self::JENJANG_INTI;

        return view('admin.penduduk-jenjang.form', compact('desa', 'existing', 'jenjangInti'));
    }

    public function update(Request $request, $wilayah_id)
    {
        $desa = WilayahDesa::findOrFail($wilayah_id);

        $rules = ['sumber_data' => 'nullable|string|max:100'];
        foreach (array_keys(self::JENJANG_INTI) as $jenjangId) {
            $rules["penduduk_l.$jenjangId"]      = 'nullable|integer|min:0';
            $rules["penduduk_p.$jenjangId"]      = 'nullable|integer|min:0';
            $rules["putus_sekolah_l.$jenjangId"]  = 'nullable|integer|min:0';
            $rules["putus_sekolah_p.$jenjangId"]  = 'nullable|integer|min:0';
        }

        $validated = $request->validate($rules);

        // FIX konsistensi data sumber: ATS/putus sekolah adalah SUBSET dari
        // penduduk usia jenjang tsb (bukan tambahan di luar itu) — jadi tidak
        // masuk akal jika putus_sekolah > penduduk untuk jenjang & jenis
        // kelamin yang sama. Tolak dan minta admin cek ulang datanya.
        foreach (array_keys(self::JENJANG_INTI) as $jenjangId) {
            $pendudukL = (int) ($validated['penduduk_l'][$jenjangId] ?? 0);
            $pendudukP = (int) ($validated['penduduk_p'][$jenjangId] ?? 0);
            $putusL    = (int) ($validated['putus_sekolah_l'][$jenjangId] ?? 0);
            $putusP    = (int) ($validated['putus_sekolah_p'][$jenjangId] ?? 0);

            if ($putusL > $pendudukL || $putusP > $pendudukP) {
                $label = self::JENJANG_INTI[$jenjangId];
                return redirect()->back()->withInput()->withErrors([
                    "putus_sekolah_l.$jenjangId" => "Jenjang {$label}: ATS/Putus Sekolah tidak boleh lebih besar dari Penduduk Usia Jenjang (ATS adalah bagian dari penduduk usia jenjang tsb, bukan tambahan di luar itu).",
                ]);
            }
        }

        $sumberData = $request->input('sumber_data') ?: 'Input manual admin';

        foreach (array_keys(self::JENJANG_INTI) as $jenjangId) {
            PendudukJenjang::updateOrCreate(
                ['wilayah_id' => $wilayah_id, 'jenjang_id' => $jenjangId],
                [
                    'penduduk_l'      => $validated['penduduk_l'][$jenjangId] ?? 0,
                    'penduduk_p'      => $validated['penduduk_p'][$jenjangId] ?? 0,
                    'putus_sekolah_l' => $validated['putus_sekolah_l'][$jenjangId] ?? 0,
                    'putus_sekolah_p' => $validated['putus_sekolah_p'][$jenjangId] ?? 0,
                    'sumber_data'     => $sumberData,
                ]
            );
        }

        return redirect()->route('penduduk-jenjang.index')
            ->with('success', "School-age population & dropout/ATS data for village {$desa->nama_wilayah} successfully saved!");
    }
}
