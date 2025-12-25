<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\DriverProfile;

class VerifikasiController extends Controller
{
    public function index() {
        $drivers = DriverProfile::with('user')->where('status', 'pending')->get();
        return view('admin.verifikasi.index', compact('drivers'));
    }

    public function approve($id) {
        DriverProfile::where('id', $id)->update(['status' => 'verified']);
        return back()->with('success', 'Driver diverifikasi');
    }

    public function reject($id) {
        DriverProfile::where('id', $id)->update(['status' => 'rejected']);
        return back()->with('success', 'Driver ditolak');
    }
}
