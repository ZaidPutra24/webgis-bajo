<?php

namespace App\Http\Controllers;

use App\Models\StatistikSekolah;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class StatistikSekolahController extends Controller
{
    public function index()
    {
        $sekolahs = Sekolah::with(['statistik', 'jenjang'])->orderBy('nama_sekolah')->get();
        return view('admin.statistik.index', compact('sekolahs'));
    }

    public function edit($sekolah_id)
    {
        $sekolah   = Sekolah::findOrFail($sekolah_id);
        $statistik = StatistikSekolah::where('sekolah_id', $sekolah_id)->first() ?? new StatistikSekolah();
        return view('admin.statistik.form', compact('sekolah', 'statistik'));
    }

    public function update(Request $request, $sekolah_id)
    {
        // daya_tampung kembali divalidasi karena kolomnya sudah ditambahkan lagi ke DB.
        // Tetap nullable: beberapa satuan non-formal (LKP/PKBM/SKB) memang belum punya datanya.
        $request->validate([
            'siswa_l'       => 'nullable|integer|min:0',
            'siswa_p'       => 'nullable|integer|min:0',
            'jumlah_siswa'  => 'nullable|integer|min:0',
            'daya_tampung'  => 'nullable|integer|min:0',
            'jumlah_guru'   => 'nullable|integer|min:0',
            'jumlah_rombel' => 'nullable|integer|min:0',
            'ruang_kelas'   => 'nullable|integer|min:0',
            'laboratorium'  => 'nullable|integer|min:0',
            'perpustakaan'  => 'nullable|integer|min:0',
        ]);

        // FIX: Validasi konsistensi jumlah_siswa harus sama dengan siswa_l + siswa_p
        // jika ketiganya diisi dan bukan nol.
        $siswaL      = (int) ($request->input('siswa_l', 0) ?? 0);
        $siswaP      = (int) ($request->input('siswa_p', 0) ?? 0);
        $jumlahSiswa = (int) ($request->input('jumlah_siswa', 0) ?? 0);

        if (($siswaL + $siswaP) > 0 && $jumlahSiswa > 0 && ($siswaL + $siswaP) !== $jumlahSiswa) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['jumlah_siswa' => 'Total Siswa harus sama dengan Siswa L + Siswa P (' . ($siswaL + $siswaP) . ').']);
        }

        // Jika jumlah_siswa kosong/0 tapi siswa_l dan siswa_p terisi, isi otomatis
        if ($jumlahSiswa === 0 && ($siswaL + $siswaP) > 0) {
            $request->merge(['jumlah_siswa' => $siswaL + $siswaP]);
        }

        // UPDATE: 'daya_tampung' dikembalikan ke $fields karena kolomnya sudah ada
        // lagi di DB (lihat migration add_daya_tampung_to_statistik_sekolah_table),
        // sehingga nilainya kini ikut disimpan oleh updateOrCreate. 'ruang_guru'
        // tetap tidak disertakan karena kolom tsb memang belum ada di tabel.
        $fields = [
            'siswa_l', 'siswa_p', 'jumlah_siswa', 'daya_tampung', 'jumlah_guru',
            'jumlah_rombel', 'ruang_kelas', 'laboratorium', 'perpustakaan',
        ];

        // Loop $semuaNol mengecek seluruh field yang benar-benar dikirim oleh form
        // (9 field, termasuk daya_tampung) sehingga tidak salah trigger delete.
        $semuaNol = true;
        foreach ($fields as $field) {
            $nilai = $request->input($field);
            if (!is_null($nilai) && (int) $nilai !== 0) {
                $semuaNol = false;
                break;
            }
        }

        if ($semuaNol) {
            StatistikSekolah::where('sekolah_id', $sekolah_id)->delete();
            return redirect()->route('statistik.index')
                ->with('success', 'Semua nilai 0 — data statistik sekolah berhasil dihapus.');
        }

        StatistikSekolah::updateOrCreate(
            ['sekolah_id' => $sekolah_id],
            $request->only($fields)
        );

        return redirect()->route('statistik.index')
            ->with('success', 'Data statistik fasilitas sekolah berhasil disimpan!');
    }
}
