<?php
namespace App\Http\Controllers\Driver;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller {
    public function index() {
        $user = Auth::user();
        $statusAkun = $user->driverProfile ? $user->driverProfile->status : 'pending';
        return view('driver.dashboard', compact('statusAkun'));
    }
}