<?php
namespace App\Http\Controllers\Driver;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Angkot;
use App\Models\RiwayatPengemudi;
use Carbon\Carbon;

class DashboardController extends Controller {
    public function index() {
        $user = Auth::user();
        $statusAkun = $user->driverProfile ? $user->driverProfile->status : 'pending';
        $angkot = Angkot::where('user_id', $user->id)->first();
        
        // Hitung trip hari ini
        $tripHariIni = RiwayatPengemudi::where('user_id', $user->id)
            ->whereDate('waktu_mulai', Carbon::today())
            ->count();
        
        // Status online (cek apakah ada tracking aktif)
        $isOnline = RiwayatPengemudi::where('user_id', $user->id)
            ->whereNull('waktu_selesai')
            ->exists();
        
        $riwayatTerbaru = RiwayatPengemudi::where('user_id', $user->id)->latest()->take(5)->get();

        return view('driver.dashboard', compact('statusAkun', 'angkot', 'tripHariIni', 'isOnline', 'riwayatTerbaru'));
    }
}
