<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TrayekController;

// Endpoint Public (Bisa diakses Web & Mobile tanpa login)
Route::get('/trayeks', [TrayekController::class, 'index']);
Route::get('/trayeks/{kode}', [TrayekController::class, 'show']);
