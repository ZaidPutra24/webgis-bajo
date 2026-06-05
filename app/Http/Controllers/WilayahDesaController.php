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
            'nama_wilayah' => 'required|string|max:255',
            'geojson'      => 'required|json', // Validasi memastikan format teks adalah JSON valid
            'luas_wilayah' => 'nullable|numeric',
        ]);

        WilayahDesa::create($request->only(['nama_wilayah', 'geojson', 'luas_wilayah']));

        return redirect()->route('wilayah.index')->with('success', 'Data Wilayah berhasil ditambahkan!');
    }

    public function show($id)
    {
        // Tidak ada view show tersendiri — redirect ke halaman edit sebagai fallback
        // agar akses GET /wilayah/{id} tidak menghasilkan 404.
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
            'nama_wilayah' => 'required|string|max:255',
            'geojson'      => 'required|json',
            'luas_wilayah' => 'nullable|numeric',
        ]);

        $wilayah = WilayahDesa::findOrFail($id);
        $wilayah->update($request->only(['nama_wilayah', 'geojson', 'luas_wilayah']));

        return redirect()->route('wilayah.index')->with('success', 'Data Wilayah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $wilayah = WilayahDesa::findOrFail($id);
        $wilayah->delete();

        return redirect()->route('wilayah.index')->with('success', 'Data Wilayah berhasil dihapus!');
    }
}