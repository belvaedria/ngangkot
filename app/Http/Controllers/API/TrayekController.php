<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Trayek;
use Illuminate\Http\Request;

class TrayekController extends Controller
{
    // Mengambil semua data trayek (untuk dropdown/list)
    public function index()
    {
        $trayeks = Trayek::all();
        return response()->json($trayeks);
    }

    // Mengambil detail satu trayek (ketika dipilih user)
    public function show($kode)
    {
        // Cari berdasarkan kode_trayek, bukan ID
        $trayek = Trayek::where('kode_trayek', $kode)->first();

        if (!$trayek) {
            return response()->json(['message' => 'Trayek tidak ditemukan'], 404);
        }

        return response()->json($trayek);
    }
}