<?php
namespace App\Http\Controllers\Driver;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Angkot;

class TrackingController extends Controller
{
    public function index() {
        $angkot = Angkot::where('user_id', Auth::id())->first();
        return view('driver.tracking.index', compact('angkot'));
    }

    public function updateStatus(Request $request) {
        $angkot = Angkot::where('user_id', Auth::id())->first();
        if($angkot) {
            $status = $request->status == 'aktif' ? true : false;
            $angkot->update(['is_active' => $status]);
            return back()->with('success', 'Status tracking: ' . $request->status);
        }
        return back()->with('error', 'Pilih angkot dulu di menu Kelola Angkot.');
    }
}