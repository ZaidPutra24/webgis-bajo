<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WilayahDesaController;
use App\Http\Controllers\WilayahKecamatanController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JarakSekolahLokasiController;
use App\Http\Controllers\StatistikSekolahController;
use App\Http\Controllers\KurikulumUtilitasController;
use App\Http\Controllers\KepadatanUsiaSekolahController;
use App\Http\Controllers\PendudukJenjangController;
use App\Http\Controllers\KebutuhanPkbmController;
use App\Http\Controllers\KMeansClusteringController;
use App\Http\Controllers\AksesibilitasController;
use App\Http\Controllers\JarakKondisiController;

// Halaman utama WebGIS (publik)
Route::get('/', [HomeController::class, 'index'])->name('home');

// BUG FIX #5 & #6: Pindahkan logika dashboard ke DashboardController agar
// tidak duplikasi kode, dan gunakan whereIn dengan ID jenjang yang benar.
// ID jenjang sesuai JenjangSeeder: SD=1, MI=2, SMP=3, MTS=4, SMA=5, MA=6, SMK=7
Route::get('/dashboard', function () {
    // Dashboard menghitung statistik detail secara mandiri via @php di view-nya.
    // Route hanya perlu inject totalWilayah dan totalSekolah untuk stat cards.
    $totalWilayah = \App\Models\WilayahDesa::count();
    $totalSekolah = \App\Models\Sekolah::count();

    return view('dashboard', compact('totalWilayah', 'totalSekolah'));
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Wilayah Kecamatan (ROI kecamatan + hitung jumlah sekolah dalam jangkauan)
    Route::resource('kecamatan', WilayahKecamatanController::class);

    // CRUD Wilayah Desa
    Route::resource('wilayah', WilayahDesaController::class);

    // CRUD Sekolah
    Route::resource('sekolah', SekolahController::class);

    // CRUD Matriks Jarak
    Route::resource('jarak', JarakSekolahLokasiController::class);

    // Statistik Sekolah (custom routes - One-to-One)
    Route::get('/statistik', [StatistikSekolahController::class, 'index'])->name('statistik.index');
    Route::get('/statistik/{sekolah_id}/edit', [StatistikSekolahController::class, 'edit'])->name('statistik.edit');
    Route::put('/statistik/{sekolah_id}', [StatistikSekolahController::class, 'update'])->name('statistik.update');

    // Kurikulum & Utilitas
    Route::get('/utilitas', [KurikulumUtilitasController::class, 'index'])->name('utilitas.index');
    Route::get('/utilitas/{sekolah}/edit', [KurikulumUtilitasController::class, 'edit'])->name('utilitas.edit');
    Route::put('/utilitas/{sekolah}', [KurikulumUtilitasController::class, 'update'])->name('utilitas.update');

    // Analisis Kepadatan Usia Sekolah per Desa (poin 1)
    Route::get('/kepadatan', [KepadatanUsiaSekolahController::class, 'index'])->name('kepadatan.index');

    // Pengelompokan Wilayah dengan K-Means Clustering (poin 2) — dasar
    // usulan lokasi sekolah baru berdasarkan kedekatan geografis desa-sekolah
    // dan beban jumlah siswa.
    Route::get('/clustering', [KMeansClusteringController::class, 'index'])->name('clustering.index');
    Route::get('/aksesibilitas', [AksesibilitasController::class, 'index'])->name('aksesibilitas.index');

    // Analisis Hubungan Jarak/Keterpencilan dengan Kondisi Sekolah (poin 4).
    Route::get('/jarak-kondisi', [JarakKondisiController::class, 'index'])->name('jarak-kondisi.index');

    // Data SUMBER (bukan hasil hitung): Penduduk Usia Jenjang & ATS/Putus
    // Sekolah per desa. Kepadatan SENGAJA tidak punya form — selalu dihitung
    // otomatis dari data ini + luas_wilayah (lihat KepadatanUsiaSekolahController).
    Route::get('/penduduk-jenjang', [PendudukJenjangController::class, 'index'])->name('penduduk-jenjang.index');
    Route::get('/penduduk-jenjang/{wilayah_id}/edit', [PendudukJenjangController::class, 'edit'])->name('penduduk-jenjang.edit');
    Route::put('/penduduk-jenjang/{wilayah_id}', [PendudukJenjangController::class, 'update'])->name('penduduk-jenjang.update');

    // Kebutuhan PKBM (BPB usia 19-24 & 25+) per desa — SENGAJA terpisah dari
    // penduduk-jenjang, lihat KebutuhanPkbmController.
    Route::get('/kebutuhan-pkbm', [KebutuhanPkbmController::class, 'index'])->name('kebutuhan-pkbm.index');
    Route::get('/kebutuhan-pkbm/{wilayah_id}/edit', [KebutuhanPkbmController::class, 'edit'])->name('kebutuhan-pkbm.edit');
    Route::put('/kebutuhan-pkbm/{wilayah_id}', [KebutuhanPkbmController::class, 'update'])->name('kebutuhan-pkbm.update');
});

require __DIR__.'/auth.php';
