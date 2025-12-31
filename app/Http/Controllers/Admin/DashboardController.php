<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trayek;
use App\Models\Angkot;
use App\Models\Laporan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTrayek = Trayek::count();
        $totalAngkot = Angkot::count();
        $laporanPending = Laporan::where('status', 'pending')->count();
        $totalPengguna = User::count();

        return view('admin.dashboard', compact(
            'totalTrayek',
            'totalAngkot',
            'laporanPending',
            'totalPengguna'
        ));
    }
}
