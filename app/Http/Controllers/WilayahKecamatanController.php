<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\WilayahKecamatan;
use Illuminate\Http\Request;

class WilayahKecamatanController extends Controller
{
    public function index()
    {
        $kecamatans = WilayahKecamatan::latest()->get();

        // Ambil semua sekolah berkoordinat sekali saja, lalu hitung ROI per kecamatan
        // agar tidak query berulang di dalam loop (N+1).
        $sekolahs = Sekolah::whereNotNull('latitude')->whereNotNull('longitude')->get();

        $kecamatans->each(function ($kec) use ($sekolahs) {
            $kec->jumlah_sekolah = $kec->hitungJumlahSekolah($sekolahs);
        });

        return view('admin.kecamatan.index', compact('kecamatans'));
    }

    public function create()
    {
        return view('admin.kecamatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kecamatan' => 'required|string|max:255',
            'kabupaten'      => 'nullable|string|max:255',
            'provinsi'       => 'nullable|string|max:255',
            'geojson'        => 'required|json',
            'luas_wilayah'   => 'nullable|numeric',
            'warna'          => 'nullable|string|max:20',
        ]);

        WilayahKecamatan::create($request->only([
            'nama_kecamatan', 'kabupaten', 'provinsi', 'geojson', 'luas_wilayah', 'warna',
        ]));

        return redirect()->route('kecamatan.index')
            ->with('success', 'Data Wilayah Kecamatan berhasil ditambahkan!');
    }

    public function show($id)
    {
        return redirect()->route('kecamatan.edit', $id);
    }

    public function edit($id)
    {
        $kecamatan = WilayahKecamatan::findOrFail($id);

        // Preview jumlah & daftar sekolah yang masuk ROI kecamatan ini (analisis ROI)
        $sekolahs = Sekolah::with('jenjang')
            ->whereNotNull('latitude')->whereNotNull('longitude')->get();

        $sekolahDalamRoi = $kecamatan->daftarSekolahDalamRoi($sekolahs);

        return view('admin.kecamatan.edit', compact('kecamatan', 'sekolahDalamRoi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kecamatan' => 'required|string|max:255',
            'kabupaten'      => 'nullable|string|max:255',
            'provinsi'       => 'nullable|string|max:255',
            'geojson'        => 'required|json',
            'luas_wilayah'   => 'nullable|numeric',
            'warna'          => 'nullable|string|max:20',
        ]);

        $kecamatan = WilayahKecamatan::findOrFail($id);

        $kecamatan->update($request->only([
            'nama_kecamatan', 'kabupaten', 'provinsi', 'geojson', 'luas_wilayah', 'warna',
        ]));

        return redirect()->route('kecamatan.index')
            ->with('success', 'Data Wilayah Kecamatan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kecamatan = WilayahKecamatan::findOrFail($id);
        $kecamatan->delete();

        return redirect()->route('kecamatan.index')
            ->with('success', 'Data Wilayah Kecamatan berhasil dihapus!');
    }
}
