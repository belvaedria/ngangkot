<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;

// Alias Controller biar gak bentrok nama
use App\Http\Controllers\Passenger\NavigasiController as PasNavigasi;
use App\Http\Controllers\Passenger\RiwayatController as PasRiwayat;
use App\Http\Controllers\Passenger\DashboardController as PasDashboard;
use App\Http\Controllers\Passenger\LaporanController as PasLaporan;
use App\Http\Controllers\Passenger\EdukasiController as PasEdukasi;

use App\Http\Controllers\Driver\TrackingController as DrvTracking;
use App\Http\Controllers\Driver\DashboardController as DrvDashboard;
use App\Http\Controllers\Driver\AngkotController as DrvAngkot;
use App\Http\Controllers\Driver\RiwayatController as DrvRiwayat;

use App\Http\Controllers\Admin\TrayekController as AdmTrayek;
use App\Http\Controllers\Admin\DashboardController as AdmDashboard;
use App\Http\Controllers\Admin\VerifikasiController as AdmVerifikasi;
use App\Http\Controllers\Admin\LaporanController as AdmLaporan;
use App\Http\Controllers\Admin\ArtikelController as AdmArtikel;
use App\Http\Controllers\Admin\FaqController as AdmFaq;


// --- PUBLIC (Tanpa Login) ---
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/trayek/{kode}', [PublicController::class, 'show'])->name('trayek.show');

// FITUR KAMU: Navigasi (Bisa Public)
Route::get('/navigasi', [PasNavigasi::class, 'index'])->name('navigasi.index');
Route::post('/navigasi/cari', [PasNavigasi::class, 'searchRoute'])->name('navigasi.search');

require __DIR__.'/auth.php';

// --- DASHBOARD REDIRECTOR ---
Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    if ($role === 'admin') return redirect()->route('admin.dashboard');
    if ($role === 'driver') return redirect()->route('driver.dashboard');
    return redirect()->route('passenger.dashboard');
})->middleware(['auth'])->name('dashboard');

// --- AREA LOGIN ---
Route::middleware(['auth'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('passenger')->name('passenger.')->middleware('role:passenger')->group(function () {
        Route::get('/home', [PasDashboard::class, 'index'])->name('dashboard');
        Route::get('/riwayat', [PasRiwayat::class, 'index'])->name('riwayat.index');
        Route::post('/favorit', [PasRiwayat::class, 'storeFavorit'])->name('favorit.store');
        Route::resource('laporan', PasLaporan::class);
        Route::resource('edukasi', PasEdukasi::class);
    });

    Route::prefix('driver')->name('driver.')->middleware('role:driver')->group(function () {
        Route::get('/home', [DrvDashboard::class, 'index'])->name('dashboard');
        Route::get('/tracking', [DrvTracking::class, 'index'])->name('tracking.index');
        Route::post('/tracking', [DrvTracking::class, 'updateStatus'])->name('tracking.update');
        Route::resource('angkot', DrvAngkot::class);
        Route::post('/angkot/pilih', [DrvAngkot::class, 'pilihAngkot'])->name('angkot.pilih');
        Route::get('/riwayat', [DrvRiwayat::class, 'index'])->name('riwayat.index');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/home', [AdmDashboard::class, 'index'])->name('dashboard');
        Route::resource('trayek', AdmTrayek::class);
        Route::get('/verifikasi', [AdmVerifikasi::class, 'index'])->name('verifikasi.index');
        Route::post('/verifikasi/{id}/approve', [AdmVerifikasi::class, 'approve'])->name('verifikasi.approve');
        Route::post('/verifikasi/{id}/reject', [AdmVerifikasi::class, 'reject'])->name('verifikasi.reject');
        Route::resource('laporan', AdmLaporan::class)->only(['index', 'update']);
        Route::resource('artikel', AdmArtikel::class);
        Route::resource('faq', AdmFaq::class);
    });

});
