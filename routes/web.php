<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;

// Passenger
use App\Http\Controllers\Passenger\NavigasiController as PasNavigasi;
use App\Http\Controllers\Passenger\RiwayatController as PasRiwayat;
use App\Http\Controllers\Passenger\LaporanController as PasLaporan;
use App\Http\Controllers\Passenger\EdukasiController as PasEdukasi;
use App\Http\Controllers\Passenger\DashboardController as PasDashboard;

// Driver
use App\Http\Controllers\Driver\TrackingController as DrvTracking;
use App\Http\Controllers\Driver\DashboardController as DrvDashboard;
use App\Http\Controllers\Driver\AngkotController as DrvAngkot;
use App\Http\Controllers\Driver\RiwayatController as DrvRiwayat;
use App\Http\Controllers\Driver\EdukasiController as DrvEdukasi;
use App\Http\Controllers\Driver\DriverProfileController;

// Admin
use App\Http\Controllers\Admin\TrayekController as AdmTrayek;
use App\Http\Controllers\Admin\DashboardController as AdmDashboard;
use App\Http\Controllers\Admin\VerifikasiController as AdmVerifikasi;
use App\Http\Controllers\Admin\LaporanController as AdmLaporan;
use App\Http\Controllers\Admin\ArtikelController as AdmArtikel;
use App\Http\Controllers\Admin\FaqController as AdmFaq;

use App\Http\Controllers\API\TrayekController;


/*
|--------------------------------------------------------------------------
| PUBLIC (Tanpa Login)
|--------------------------------------------------------------------------
*/

// Welcome / landing
Route::get('/', [PublicController::class, 'index'])->name('home');

Route::get('/api/trayeks', [TrayekController::class, 'index']);
Route::get('/api/trayeks/{kode}', [TrayekController::class, 'show']);

Route::get('/trayek/{kode}', [PublicController::class, 'trayekIndex'])->name('passenger.trayek');
Route::get('/passenger/trayek', [PublicController::class, 'trayekIndex'])->name('passenger.trayek');


// Navigasi = dashboard → jadi /navigasi cuma redirect (biar gak ada 2 konsep)
Route::get('/navigasi', fn () => redirect()->route('passenger.dashboard'))
    ->name('navigasi.index');

// Search route tetap public (guest boleh cari rute)
Route::post('/navigasi/cari', [PasNavigasi::class, 'searchRoute'])->name('navigasi.search');
Route::get('/navigasi/cari', fn () => redirect()->route('passenger.dashboard'));


/*
|--------------------------------------------------------------------------
| PASSENGER PUBLIC AREA (Guest boleh masuk)
|--------------------------------------------------------------------------
| Dashboard passenger = navigasi + map
| Trayek & edukasi public (untuk welcome page)
*/
Route::prefix('passenger')->name('passenger.')->group(function () {
    // HOME/DASHBOARD (public)
    Route::get('/home', [PasDashboard::class, 'index'])->name('dashboard');

    // Public trayek & edukasi versi passenger UI
    // Sesuaikan controller/method kamu: bisa PublicController atau controller passenger.
    Route::get('/trayek', [PublicController::class, 'trayekIndex'])->name('trayek.index');
    Route::get('/edukasi', [PasEdukasi::class, 'index'])->name('edukasi.index');
    
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Breeze/Jetstream)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| DASHBOARD REDIRECTOR (untuk yang sudah login)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    if ($role === 'passenger') return redirect()->route('passenger.dashboard');
    if ($role === 'admin') return redirect()->route('admin.dashboard');
    if ($role === 'driver') return redirect()->route('driver.dashboard');
    return redirect()->route('passenger.dashboard');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED AREA (Role-based)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // PASSENGER locked
    Route::prefix('passenger')->name('passenger.')->middleware('role:passenger')->group(function () {
        Route::get('/riwayat', [PasRiwayat::class, 'index'])->name('riwayat.index');
        Route::post('/favorit', [PasRiwayat::class, 'storeFavorit'])->name('favorit.store');
        Route::delete('/favorit/{id}', [PasRiwayat::class, 'destroyFavorit'])->name('favorit.destroy');
        Route::resource('laporan', PasLaporan::class);
    });


    // DRIVER area (driver saja, belum tentu verified)
    Route::prefix('driver')->name('driver.')->middleware('role:driver')->group(function () {

        // index profil (lihat status, dll)
        Route::get('/profile', [DriverProfileController::class, 'index'])->name('profile.index');

        // edit profil harus beda URL biar route name kebentuk
        Route::get('/profile/edit', [DriverProfileController::class, 'edit'])->name('profile.edit');

        // update profil
        Route::put('/profile', [DriverProfileController::class, 'update'])->name('profile.update');

        // waiting page
        Route::get('/waiting', [DriverProfileController::class, 'waiting'])->name('verification.waiting');
    });


    // DRIVER area (wajib verified)
    Route::prefix('driver')->name('driver.')->middleware(['role:driver','driver.verified'])->group(function () {
        Route::get('/home', [DrvDashboard::class, 'index'])->name('dashboard');
        Route::post('/tracking/status', [DrvTracking::class, 'updateStatus'])->name('tracking.status');
        Route::post('/tracking/lokasi', [DrvTracking::class, 'updateLokasi'])->name('tracking.lokasi');
        Route::resource('tracking', DrvTracking::class)->only(['index', 'store']);
        Route::resource('angkot', DrvAngkot::class);
        Route::resource('riwayat', DrvRiwayat::class)->only(['index']);
        Route::get('/edukasi', [DrvEdukasi::class, 'index'])->name('edukasi.index');
    });

    // ADMIN locked
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/home', [AdmDashboard::class, 'index'])->name('dashboard');
        Route::resource('trayek', AdmTrayek::class);
        Route::get('/verifikasi', action: [AdmVerifikasi::class, 'index'])
            ->name('verifikasi.index');
        Route::get('/verifikasi/{id}', [AdmVerifikasi::class, 'show'])
            ->name('verifikasi.show');
        Route::post('/verifikasi/{id}/approve', [AdmVerifikasi::class, 'approve'])
            ->name('verifikasi.approve');
        Route::post('/verifikasi/{id}/reject', [AdmVerifikasi::class, 'reject'])
            ->name('verifikasi.reject');
        Route::resource('laporan', AdmLaporan::class)->only(['index', 'update']);
        Route::resource('artikel', AdmArtikel::class);
        Route::resource('faq', AdmFaq::class);
    });

});
