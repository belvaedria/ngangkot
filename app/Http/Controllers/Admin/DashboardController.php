<?php
namespace App\Http\Controllers\Passenger;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Trayek;

class DashboardController extends Controller
{
    public function index()
    {
        $trayeks = Trayek::all(); 
        
        return view('passenger.dashboard.index', compact('trayeks'));
    }
}