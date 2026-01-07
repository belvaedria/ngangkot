<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;

class VerifikasiController extends Controller
{
    // LIST DRIVER PENDING
    public function index()
    {
        $drivers = DriverProfile::with('user')
            ->where('status', 'pending')
            ->get();

        return view('admin.verifikasi.index', compact('drivers'));
    }

    // LIHAT DETAIL DRIVER
    public function show($id)
    {
        $profile = DriverProfile::with('user')->findOrFail($id);

        // pakai view yang sama dengan driver
        return view('driver.profile.index', compact('profile'));
    }

    // TERIMA DRIVER
    public function approve($id)
    {
        $profile = DriverProfile::findOrFail($id);
        $profile->update(['status' => 'verified']);

        return redirect()->route('admin.verifikasi.index')
            ->with('success', 'Driver berhasil diverifikasi');
    }

    // TOLAK DRIVER
    public function reject($id)
    {
        $profile = DriverProfile::findOrFail($id);
        $profile->update(['status' => 'rejected']);

        return redirect()->route('admin.verifikasi.index')
            ->with('success', 'Driver ditolak');
    }
}
