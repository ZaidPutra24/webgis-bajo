<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Jenjang;
use App\Models\WilayahDesa;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    public function index()
    {
        $sekolahs = Sekolah::with('jenjang')->latest()->get();
        return view('admin.sekolah.index', compact('sekolahs'));
    }

    public function create()
    {
        $jenjangs  = Jenjang::all();
        return view('admin.sekolah.create', compact('jenjangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'jenjang_id'   => 'required|exists:jenjang,id',
            'status'       => 'required|in:Negeri,Swasta',
            'npsn'         => 'nullable|string|max:20',
            'akreditasi'   => 'nullable|string|max:5',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'alamat'       => 'nullable|string',
        ]);

        Sekolah::create($request->only([
            'nama_sekolah', 'jenjang_id', 'status',
            'npsn', 'akreditasi', 'latitude', 'longitude', 'alamat'
        ]));

        return redirect()->route('sekolah.index')->with('success', 'Data Sekolah berhasil ditambahkan!');
    }

    /*
     * BUG FIX #4 — Method show() memanggil view 'admin.sekolah.show' yang tidak ada.
     * Alih-alih menampilkan error 404/view not found saat user mengakses detail sekolah,
     * kita redirect ke halaman index dengan pesan yang lebih ramah, atau bisa juga
     * dibuat view-nya. Solusi terbaik: redirect ke edit agar admin tetap bisa bekerja,
     * dan tandai dengan komentar bahwa view show perlu dibuat jika dibutuhkan.
     *
     * Jika view admin.sekolah.show tersedia di masa depan, ganti body method ini kembali ke:
     *   $sekolah = Sekolah::with(['jenjang', 'statistik', 'utilitas', 'wilayahDesa'])->findOrFail($id);
     *   return view('admin.sekolah.show', compact('sekolah'));
     */
    public function show($id)
    {
        // View 'admin.sekolah.show' belum tersedia — redirect ke halaman edit sebagai fallback.
        return redirect()->route('sekolah.edit', $id);
    }

    public function edit($id)
    {
        $sekolah  = Sekolah::findOrFail($id);
        $jenjangs = Jenjang::all();
        return view('admin.sekolah.edit', compact('sekolah', 'jenjangs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'jenjang_id'   => 'required|exists:jenjang,id',
            'status'       => 'required|in:Negeri,Swasta',
            'npsn'         => 'nullable|string|max:20',
            'akreditasi'   => 'nullable|string|max:5',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'alamat'       => 'nullable|string',
        ]);

        $sekolah = Sekolah::findOrFail($id);
        $sekolah->update($request->only([
            'nama_sekolah', 'jenjang_id', 'status',
            'npsn', 'akreditasi', 'latitude', 'longitude', 'alamat'
        ]));

        return redirect()->route('sekolah.index')->with('success', 'Data Sekolah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        $sekolah->delete();

        return redirect()->route('sekolah.index')->with('success', 'Data Sekolah berhasil dihapus!');
    }
}
