<?php
namespace App\Http\Controllers\Driver;
use App\Http\Controllers\Controller;
use App\Models\Angkot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AngkotController extends Controller {
    public function index() {
        $angkots = Angkot::where('is_active', false)->get(); 
        $myAngkot = Angkot::where('user_id', Auth::id())->first();
        return view('driver.operasional.index', compact('angkots', 'myAngkot'));
    }

    public function mulaiNarik(Request $request) {
        $angkot = Angkot::findOrFail($request->angkot_id);
        $angkot->update(['is_active' => true, 'user_id' => Auth::id()]);
        return back()->with('success', 'Mulai beroperasi.');
    }

    public function berhentiNarik() {
        Angkot::where('user_id', Auth::id())->update(['is_active' => false, 'user_id' => null]);
        return back()->with('success', 'Selesai beroperasi.');
    }
}
