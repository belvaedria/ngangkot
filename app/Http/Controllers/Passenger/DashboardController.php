<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Trayek;
use App\Models\RiwayatPenumpang;
use App\Models\RuteFavorit;

class DashboardController extends Controller
{
    public function index()
    {
        // sama kayak data dasar yang dipakai NavigasiController@index
        $trayeks = Trayek::where('tampil_di_menu', true)->get();

        $riwayat = Auth::check()
            ? RiwayatPenumpang::where('user_id', Auth::id())->latest()->take(6)->get()
            : collect();

        $favorit = Auth::check()
            ? RuteFavorit::where('user_id', Auth::id())->latest()->take(6)->get()
            : collect();

        return view('passenger.dashboard', compact('trayeks', 'riwayat', 'favorit'));
    }
}
