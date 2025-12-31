<?php
namespace App\Http\Controllers\Driver;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Angkot;
use App\Models\RiwayatPengemudi;

class DashboardController extends Controller {
    public function index() {
        $user = Auth::user();
        $statusAkun = $user->driverProfile ? $user->driverProfile->status : 'pending';
        $angkot = Angkot::where('user_id', $user->id)->first();
        $riwayatTerbaru = RiwayatPengemudi::where('user_id', $user->id)->latest()->take(5)->get();

        return view('driver.dashboard', compact('statusAkun', 'angkot', 'riwayatTerbaru'));
    }
}
