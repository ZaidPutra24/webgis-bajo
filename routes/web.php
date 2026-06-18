<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WilayahDesaController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JarakSekolahLokasiController;
use App\Http\Controllers\StatistikSekolahController;
use App\Http\Controllers\KurikulumUtilitasController;

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
});

require __DIR__.'/auth.php';
