<?php
namespace App\Http\Controllers\Driver;
use App\Http\Controllers\Controller;
use App\Models\Angkot;
use App\Models\Trayek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AngkotController extends Controller
{
    public function index() {
        // Driver cuma bisa liat angkot miliknya sendiri
        $angkots = Angkot::where('user_id', Auth::id())->get();
        return view('driver.angkot.index', compact('angkots'));
    }

    public function create() {
        $trayeks = Trayek::all();
        return view('driver.angkot.create', compact('trayeks'));
    }

    public function store(Request $request) {
        $request->validate([
            'plat_nomor' => 'required|unique:angkots',
            'trayek_id'  => 'required|exists:trayeks,id',
        ]);

        $trayek = Trayek::findOrFail($request->trayek_id);

        Angkot::create([
            'plat_nomor'   => $request->plat_nomor,
            'trayek_id'    => $request->trayek_id,
            'kode_trayek'  => $trayek->kode_trayek, // atau nama kolom sebenarnya di trayeks
            'user_id'      => Auth::id(),
            'is_active'    => false,
        ]);

        return redirect()->route('driver.angkot.index')->with('success', 'Mobil berhasil didaftarkan');
    }


    public function edit(Angkot $angkot) {
        if($angkot->user_id !== Auth::id()) abort(403);
        $trayeks = Trayek::all();
        return view('driver.angkot.edit', compact('angkot', 'trayeks'));
    }

    public function update(Request $request, Angkot $angkot) {
        if($angkot->user_id !== Auth::id()) abort(403);
        $angkot->update($request->only('plat_nomor', 'trayek_id'));
        return redirect()->route('driver.angkot.index');
    }

    public function destroy(Angkot $angkot) {
        if($angkot->user_id !== Auth::id()) abort(403);
        $angkot->delete();
        return back();
    }
}
