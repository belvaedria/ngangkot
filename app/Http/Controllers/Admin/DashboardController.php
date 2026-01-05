<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trayek;
use App\Models\Angkot;
use App\Models\Laporan;
use App\Models\User;
use App\Models\Artikel;
use App\Models\DriverProfile;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTrayek = Trayek::count();
        $totalAngkot = Angkot::count();
        $laporanPending = Laporan::where('status', 'pending')->count();
        $totalPengguna = User::count();
        $totalArtikel = Artikel::count();
        $driverPending = DriverProfile::where('status', 'pending')->count();
        
        // Recent reports untuk dashboard
        $recentLaporans = Laporan::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalTrayek',
            'totalAngkot',
            'laporanPending',
            'totalPengguna',
            'totalArtikel',
            'driverPending',
            'recentLaporans'
        ));
    }
}
