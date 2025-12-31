<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trayek;
use App\Models\RiwayatPenumpang;
use App\Models\RuteFavorit;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $trayeks = Trayek::where('tampil_di_menu', true)->get();
        $riwayat = Auth::check()
            ? RiwayatPenumpang::where('user_id', Auth::id())->latest()->take(5)->get()
            : collect();
        $favorit = Auth::check()
            ? RuteFavorit::where('user_id', Auth::id())->latest()->take(5)->get()
            : collect();

        return view('passenger.dashboard', compact('trayeks', 'riwayat', 'favorit'));
    }
}
