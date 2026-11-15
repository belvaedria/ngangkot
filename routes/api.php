<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TrayekController;
use App\Http\Controllers\API\NavigasiController;

Route::get('/trayek/{trayek}/angkot-aktif', [TrayekController::class, 'angkotAktif']);
Route::get('/trayeks', [TrayekController::class, 'index']);
Route::get('/trayeks/{kode}', [TrayekController::class, 'show']);
Route::post('/navigasi/cari', [NavigasiController::class, 'searchRoute']);


