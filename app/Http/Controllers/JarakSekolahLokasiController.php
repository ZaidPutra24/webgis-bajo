<?php

namespace App\Http\Controllers;

use App\Models\JarakSekolahLokasi;
use App\Models\Sekolah;
use App\Models\WilayahDesa;
use Illuminate\Http\Request;

class JarakSekolahLokasiController extends Controller
{
    public function index()
    {
        $matriksJarak = JarakSekolahLokasi::with(['sekolah', 'wilayahDesa'])->latest()->get();
        return view('admin.jarak.index', compact('matriksJarak'));
    }

    public function create()
    {
        $sekolahs = Sekolah::orderBy('nama_sekolah')->get();
        $wilayahs = WilayahDesa::orderBy('nama_wilayah')->get();
        return view('admin.jarak.create', compact('sekolahs', 'wilayahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sekolah_id' => 'required|exists:sekolah,id',
            'wilayah_id' => 'required|exists:wilayah_desa,id',
            'jarak'      => 'required|numeric|min:0',
        ]);

        $exists = JarakSekolahLokasi::where('sekolah_id', $request->sekolah_id)
                                    ->where('wilayah_id', $request->wilayah_id)
                                    ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Data jarak untuk sekolah ke desa ini sudah ada!');
        }

        JarakSekolahLokasi::create($request->only(['sekolah_id', 'wilayah_id', 'jarak']));
        return redirect()->route('jarak.index')->with('success', 'Data Matriks Jarak Berhasil Ditambahkan!');
    }

    public function show($id)
    {
        // Redirect ke edit agar data $sekolahs dan $wilayahs tersedia untuk form select.
        return redirect()->route('jarak.edit', $id);
    }

    public function edit($id)
    {
        $jarak    = JarakSekolahLokasi::findOrFail($id);
        $sekolahs = Sekolah::orderBy('nama_sekolah')->get();
        $wilayahs = WilayahDesa::orderBy('nama_wilayah')->get();
        return view('admin.jarak.edit', compact('jarak', 'sekolahs', 'wilayahs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'sekolah_id' => 'required|exists:sekolah,id',
            'wilayah_id' => 'required|exists:wilayah_desa,id',
            'jarak'      => 'required|numeric|min:0',
        ]);

        $jarak = JarakSekolahLokasi::findOrFail($id);

        /*
         * BUG FIX #2 — Cek duplikat pasangan sekolah+wilayah saat UPDATE.
         * Sebelumnya tidak ada pengecekan duplikat di update(), hanya di store().
         * Akibatnya user bisa mengganti sekolah/wilayah ke kombinasi yang sudah
         * ada di record lain — melanggar unique constraint dan menyebabkan data
         * ganda / error DB.
         *
         * Solusi: cari apakah kombinasi baru sudah ada di record LAIN (exclude id saat ini).
         */
        $duplicate = JarakSekolahLokasi::where('sekolah_id', $request->sekolah_id)
                                        ->where('wilayah_id', $request->wilayah_id)
                                        ->where('id', '!=', $id)
                                        ->exists();

        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kombinasi Sekolah dan Wilayah tersebut sudah ada pada data lain!');
        }

        $jarak->update($request->only(['sekolah_id', 'wilayah_id', 'jarak']));

        return redirect()->route('jarak.index')->with('success', 'Data Jarak berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jarak = JarakSekolahLokasi::findOrFail($id);
        $jarak->delete();

        return redirect()->route('jarak.index')->with('success', 'Data Jarak berhasil dihapus!');
    }
}