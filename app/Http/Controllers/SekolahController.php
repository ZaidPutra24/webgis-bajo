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
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'alamat'       => 'nullable|string',
            'img'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only([
            'nama_sekolah', 'jenjang_id', 'status',
            'npsn', 'akreditasi', 'latitude', 'longitude', 'alamat'
        ]);

        if ($request->hasFile('img')) {
            $filename = time() . '_' . $request->file('img')->getClientOriginalName();
            $request->file('img')->move(public_path('img/sekolah'), $filename);
            $data['img'] = $filename;
        }

        Sekolah::create($data);

        return redirect()->route('sekolah.index')->with('success', 'Data Sekolah berhasil ditambahkan!');
    }

    public function show($id)
    {
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
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'alamat'       => 'nullable|string',
            'img'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $sekolah = Sekolah::findOrFail($id);

        $data = $request->only([
            'nama_sekolah', 'jenjang_id', 'status',
            'npsn', 'akreditasi', 'latitude', 'longitude', 'alamat'
        ]);

        if ($request->hasFile('img')) {
            // Hapus file lama jika ada
            if ($sekolah->img && file_exists(public_path('img/sekolah/' . $sekolah->img))) {
                unlink(public_path('img/sekolah/' . $sekolah->img));
            }
            $filename = time() . '_' . $request->file('img')->getClientOriginalName();
            $request->file('img')->move(public_path('img/sekolah'), $filename);
            $data['img'] = $filename;
        } elseif ($request->boolean('hapus_img')) {
            // Hapus foto tanpa ganti baru
            if ($sekolah->img && file_exists(public_path('img/sekolah/' . $sekolah->img))) {
                unlink(public_path('img/sekolah/' . $sekolah->img));
            }
            $data['img'] = null;
        }

        $sekolah->update($data);

        return redirect()->route('sekolah.index')->with('success', 'Data Sekolah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        $sekolah->delete();

        return redirect()->route('sekolah.index')->with('success', 'Data Sekolah berhasil dihapus!');
    }
}
