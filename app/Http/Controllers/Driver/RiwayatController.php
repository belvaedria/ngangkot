<?php
namespace App\Http\Controllers\Driver;
use App\Http\Controllers\Controller;
use App\Models\RiwayatPengemudi;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller {
    public function index() {
        $riwayats = RiwayatPengemudi::where('user_id', Auth::id())->latest()->get();
        return view('driver.riwayat.index', compact('riwayats'));
    }
}
