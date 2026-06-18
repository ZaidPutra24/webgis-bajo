<?php

namespace App\Http\Controllers;

use App\Models\WilayahDesa;
use Illuminate\Http\Request;

class WilayahDesaController extends Controller
{
    public function index()
    {
        $wilayahs = WilayahDesa::latest()->get();
        return view('admin.wilayah.index', compact('wilayahs'));
    }

    public function create()
    {
        return view('admin.wilayah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_wilayah'             => 'required|string|max:255',
            'geojson'                  => 'required|json',
            'luas_wilayah'             => 'nullable|numeric',
            'gambar'                   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'penduduk_usia_sekolah_l'  => 'nullable|integer|min:0',
            'penduduk_usia_sekolah_p'  => 'nullable|integer|min:0',
        ]);

        $data = $request->only([
            'nama_wilayah', 'geojson', 'luas_wilayah',
            'penduduk_usia_sekolah_l', 'penduduk_usia_sekolah_p'
        ]);

        if ($request->hasFile('gambar')) {
            $filename = time() . '_' . $request->file('gambar')->getClientOriginalName();
            $request->file('gambar')->move(public_path('img/wilayah'), $filename);
            $data['gambar'] = $filename;
        }

        WilayahDesa::create($data);

        return redirect()->route('wilayah.index')->with('success', 'Data Wilayah berhasil ditambahkan!');
    }

    public function show($id)
    {
        return redirect()->route('wilayah.edit', $id);
    }

    public function edit($id)
    {
        $wilayah = WilayahDesa::findOrFail($id);
        return view('admin.wilayah.edit', compact('wilayah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_wilayah'             => 'required|string|max:255',
            'geojson'                  => 'required|json',
            'luas_wilayah'             => 'nullable|numeric',
            'gambar'                   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'penduduk_usia_sekolah_l'  => 'nullable|integer|min:0',
            'penduduk_usia_sekolah_p'  => 'nullable|integer|min:0',
        ]);

        $wilayah = WilayahDesa::findOrFail($id);

        $data = $request->only([
            'nama_wilayah', 'geojson', 'luas_wilayah',
            'penduduk_usia_sekolah_l', 'penduduk_usia_sekolah_p'
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus file lama jika ada
            if ($wilayah->gambar && file_exists(public_path('img/wilayah/' . $wilayah->gambar))) {
                unlink(public_path('img/wilayah/' . $wilayah->gambar));
            }
            $filename = time() . '_' . $request->file('gambar')->getClientOriginalName();
            $request->file('gambar')->move(public_path('img/wilayah'), $filename);
            $data['gambar'] = $filename;
        } elseif ($request->boolean('hapus_gambar')) {
            // Hapus foto tanpa ganti baru
            if ($wilayah->gambar && file_exists(public_path('img/wilayah/' . $wilayah->gambar))) {
                unlink(public_path('img/wilayah/' . $wilayah->gambar));
            }
            $data['gambar'] = null;
        }

        $wilayah->update($data);

        return redirect()->route('wilayah.index')->with('success', 'Data Wilayah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $wilayah = WilayahDesa::findOrFail($id);
        $wilayah->delete();

        return redirect()->route('wilayah.index')->with('success', 'Data Wilayah berhasil dihapus!');
    }
}
