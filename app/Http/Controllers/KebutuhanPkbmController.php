<?php

namespace App\Http\Controllers;

use App\Models\KebutuhanPkbm;
use App\Models\WilayahDesa;
use Illuminate\Http\Request;

/**
 * Form input Kebutuhan PKBM (BPB usia 19-24 & 25+) per desa.
 *
 * SENGAJA TERPISAH dari PendudukJenjangController — lihat komentar di
 * migration create_kebutuhan_pkbm_table & model KebutuhanPkbm untuk alasan
 * lengkapnya (PKBM tidak punya kohort usia sekolah formal seperti
 * PAUD/SD/SMP/SMA, jadi tidak masuk ke tabel penduduk_jenjang).
 */
class KebutuhanPkbmController extends Controller
{
    public function index()
    {
        $wilayahs = WilayahDesa::orderBy('nama_wilayah')->get();

        $kebutuhan = KebutuhanPkbm::all()->keyBy('wilayah_id');

        $wilayahs->each(function (WilayahDesa $desa) use ($kebutuhan) {
            $row = $kebutuhan->get($desa->id);
            $desa->kebutuhan_row = $row;
        });

        return view('admin.kebutuhan-pkbm.index', compact('wilayahs'));
    }

    public function edit($wilayah_id)
    {
        $desa = WilayahDesa::findOrFail($wilayah_id);
        $row  = KebutuhanPkbm::where('wilayah_id', $wilayah_id)->first();

        return view('admin.kebutuhan-pkbm.form', compact('desa', 'row'));
    }

    public function update(Request $request, $wilayah_id)
    {
        $desa = WilayahDesa::findOrFail($wilayah_id);

        $validated = $request->validate([
            'bpb_19_24'   => 'nullable|integer|min:0',
            'bpb_25_plus' => 'nullable|integer|min:0',
            'sumber_data' => 'nullable|string|max:150',
        ]);

        KebutuhanPkbm::updateOrCreate(
            ['wilayah_id' => $wilayah_id],
            [
                'bpb_19_24'   => $validated['bpb_19_24'] ?? 0,
                'bpb_25_plus' => $validated['bpb_25_plus'] ?? 0,
                'sumber_data' => $validated['sumber_data'] ?: 'Input manual admin',
            ]
        );

        return redirect()->route('kebutuhan-pkbm.index')
            ->with('success', "PKBM needs data for village {$desa->nama_wilayah} successfully saved!");
    }
}
