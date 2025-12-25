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

    public function pilihAngkot(Request $request) {
        $request->validate(['angkot_id' => 'required|exists:angkots,id']);

        // Lepas angkot lama jika ada
        Angkot::where('user_id', Auth::id())->update(['user_id' => null, 'is_active' => false]);
        
        // Ambil angkot baru
        $angkot = Angkot::findOrFail($request->angkot_id);
        
        // Double check availability
        if($angkot->user_id && $angkot->user_id !== Auth::id()) {
            return back()->with('error', 'Angkot ini sedang dipakai supir lain.');
        }

        $angkot->update(['user_id' => Auth::id()]);
        return back()->with('success', 'Angkot dipilih. Siap narik!');
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

    public function updateLokasi(Request $request) {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric'
        ]);

        $angkot = Angkot::where('user_id', Auth::id())
                        ->where('is_active', true)
                        ->first();

        if ($angkot) {
            $angkot->update([
                'lat_sekarang' => $request->lat,
                'lng_sekarang' => $request->lng
            ]);
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Angkot tidak aktif'], 400);
    }
}