<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;

// Import Controller dengan Alias agar tidak bentrok nama DashboardController
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\TrayekController as AdminTrayek;
use App\Http\Controllers\Admin\AngkotController as AdminAngkot;
use App\Http\Controllers\Admin\VerifikasiController as AdminVerifikasi;
use App\Http\Controllers\Admin\LaporanController as AdminLaporan;
use App\Http\Controllers\Admin\ArtikelController as AdminArtikel;
use App\Http\Controllers\Admin\FaqController as AdminFaq;

use App\Http\Controllers\Driver\DashboardController as DriverDashboard;
use App\Http\Controllers\Driver\AngkotController as DriverAngkot;
use App\Http\Controllers\Driver\RiwayatController as DriverRiwayat;

use App\Http\Controllers\Passenger\DashboardController as PassengerDashboard;
use App\Http\Controllers\Passenger\NavigasiController as PassengerNavigasi;
use App\Http\Controllers\Passenger\LaporanController as PassengerLaporan;
use App\Http\Controllers\Passenger\EdukasiController as PassengerEdukasi;

// 1. HALAMAN PUBLIK
Route::get('/', [PublicController::class, 'index'])->name('home');

// 2. AUTHENTICATION (Breeze)
require __DIR__.'/auth.php';

// 3. LOGIC REDIRECTOR (Pemisah Pintu Masuk)
Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    if ($role === 'admin') return redirect()->route('admin.dashboard');
    if ($role === 'driver') return redirect()->route('driver.dashboard');
    return redirect()->route('passenger.dashboard');
})->middleware(['auth'])->name('dashboard');


// 4. GROUP ROUTE PER MODUL (HMVC)

Route::middleware(['auth'])->group(function () {

    // --- MODUL ADMIN ---
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/home', [AdminDashboard::class, 'index'])->name('dashboard');
        
        // Fitur CRUD
        Route::resource('trayek', AdminTrayek::class);
        Route::resource('angkot', AdminAngkot::class);
        Route::resource('artikel', AdminArtikel::class);
        Route::resource('faq', AdminFaq::class);
        
        // Verifikasi
        Route::get('/verifikasi-driver', [AdminVerifikasi::class, 'index'])->name('verifikasi.index');
        Route::post('/verifikasi-driver/{id}/approve', [AdminVerifikasi::class, 'approve'])->name('verifikasi.approve');
        Route::post('/verifikasi-driver/{id}/reject', [AdminVerifikasi::class, 'reject'])->name('verifikasi.reject');

        // Laporan
        Route::resource('laporan', AdminLaporan::class)->only(['index', 'update']);
    });

    // --- MODUL DRIVER ---
    Route::prefix('driver')->name('driver.')->middleware('role:driver')->group(function () {
        Route::get('/home', [DriverDashboard::class, 'index'])->name('dashboard');
        
        // Fitur Driver
        Route::get('/kelola-angkot', [DriverAngkot::class, 'index'])->name('angkot.index');
        Route::get('/riwayat-perjalanan', [DriverRiwayat::class, 'index'])->name('riwayat.index');
    });

    // --- MODUL PASSENGER ---
    Route::prefix('passenger')->name('passenger.')->middleware('role:passenger')->group(function () {
        Route::get('/home', [PassengerDashboard::class, 'index'])->name('dashboard');
        
        // Navigasi
        Route::get('/navigasi', [PassengerNavigasi::class, 'index'])->name('navigasi.index');
        Route::post('/navigasi/cari', [PassengerNavigasi::class, 'searchRoute'])->name('navigasi.search');
        
        // Fitur Lain
        Route::resource('laporan', PassengerLaporan::class);
        Route::get('/edukasi', [PassengerEdukasi::class, 'index'])->name('edukasi.index');
    });
});