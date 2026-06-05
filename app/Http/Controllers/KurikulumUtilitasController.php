<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\KurikulumUtilitas;
use Illuminate\Http\Request;

class KurikulumUtilitasController extends Controller
{
    public function index()
    {
        $sekolah = Sekolah::with('utilitas')->orderBy('nama_sekolah', 'asc')->get();
        return view('admin.utilitas.index', compact('sekolah'));
    }

    public function edit($id)
    {
        $sekolah = Sekolah::with('utilitas')->findOrFail($id);
        return view('admin.utilitas.form', compact('sekolah'));
    }

    /*
     * BUG FIX #5 — Parameter route utilitas.update di web.php didefinisikan sebagai
     * Route::put('/utilitas/{sekolah}', ...) sehingga Laravel meng-inject nilai
     * segmen URL ke parameter pertama method ini.
     *
     * Versi lama memakai nama parameter `$sekolah_id` yang TIDAK cocok dengan nama
     * segmen route `{sekolah}`. Pada Laravel versi terbaru (implicit binding),
     * ketidakcocokan nama ini menyebabkan $sekolah_id bernilai null / tidak terdefinisi
     * sehingga `Sekolah::findOrFail($sekolah_id)` gagal dengan error 404 atau TypeError.
     *
     * Solusi: samakan nama parameter dengan nama segmen route → `$sekolah` (int/id).
     * Di dalam method kita terima sebagai ID numerik, bukan model binding, agar
     * konsisten dengan cara Statistik controller bekerja.
     */
    public function update(Request $request, $sekolah)
    {
        $sekolah_id = $sekolah; // Alias untuk kejelasan — $sekolah berisi ID dari segmen URL.

        $validated = $request->validate([
            'kurikulum'      => 'nullable|string|max:100',
            'penyelenggara'  => 'nullable|string|max:150',
            'akses_internet' => 'nullable|string|max:150',
            'sumber_listrik' => 'nullable|string|max:150',
            'daya_listrik'   => 'nullable|integer|min:0',
            'luas_tanah'     => 'nullable|numeric|min:0',
        ]);

        $sekolahModel = Sekolah::findOrFail($sekolah_id);

        // Field string: kosong jika null atau string kosong setelah trim
        $stringFields  = ['kurikulum', 'penyelenggara', 'akses_internet', 'sumber_listrik'];
        // Field numerik: kosong jika null atau 0
        $numericFields = ['daya_listrik', 'luas_tanah'];

        $semuaKosong = true;

        foreach ($stringFields as $field) {
            $val = $validated[$field] ?? null;
            if (!is_null($val) && trim($val) !== '') {
                $semuaKosong = false;
                break;
            }
        }

        if ($semuaKosong) {
            foreach ($numericFields as $field) {
                $val = $validated[$field] ?? null;
                if (!is_null($val) && (float) $val !== 0.0) {
                    $semuaKosong = false;
                    break;
                }
            }
        }

        if ($semuaKosong) {
            KurikulumUtilitas::where('sekolah_id', $sekolahModel->id)->delete();
            return redirect()->route('utilitas.index')
                ->with('success', 'Semua field kosong — data kurikulum & utilitas ' . $sekolahModel->nama_sekolah . ' berhasil dihapus.');
        }

        KurikulumUtilitas::updateOrCreate(
            ['sekolah_id' => $sekolahModel->id],
            $validated
        );

        return redirect()->route('utilitas.index')
            ->with('success', 'Data kurikulum & utilitas ' . $sekolahModel->nama_sekolah . ' berhasil diperbarui!');
    }
}
