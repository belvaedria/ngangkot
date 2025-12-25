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
        // Tampilkan angkot milik user login, dan angkot nganggur yang bisa dipilih
        $myAngkot = Angkot::where('user_id', Auth::id())->get(); // List angkot dia
        $availableAngkots = Angkot::whereNull('user_id')->get(); // Angkot kosong
        return view('driver.angkot.index', compact('myAngkot', 'availableAngkots'));
    }

    public function create() {
        $trayeks = Trayek::all();
        return view('driver.angkot.create', compact('trayeks'));
    }

    public function store(Request $request) {
        // Driver menambahkan angkot baru (CRUD)
        $request->validate(['plat_nomor' => 'required|unique:angkots', 'trayek_id' => 'required']);
        
        Angkot::create([
            'plat_nomor' => $request->plat_nomor,
            'trayek_id' => $request->trayek_id,
            'user_id' => Auth::id(), // Langsung jadi milik dia
            'is_active' => false
        ]);
        return redirect()->route('driver.angkot.index')->with('success', 'Angkot berhasil didaftarkan');
    }

    public function edit(Angkot $angkot) {
        if($angkot->user_id !== Auth::id()) abort(403); // Validasi kepemilikan
        $trayeks = Trayek::all();
        return view('driver.angkot.edit', compact('angkot', 'trayeks'));
    }

    public function update(Request $request, Angkot $angkot) {
        if($angkot->user_id !== Auth::id()) abort(403);
        $angkot->update($request->only('plat_nomor', 'trayek_id'));
        return redirect()->route('driver.angkot.index')->with('success', 'Data angkot diupdate');
    }

    public function destroy(Angkot $angkot) {
        if($angkot->user_id !== Auth::id()) abort(403);
        $angkot->delete();
        return back()->with('success', 'Angkot dihapus');
    }

    // Fitur Pilih Angkot Nganggur (Jika driver mau ganti mobil)
    public function pilihAngkot(Request $request) {
        // Lepas angkot lama
        Angkot::where('user_id', Auth::id())->update(['user_id' => null, 'is_active' => false]);
        
        // Ambil angkot baru
        $angkot = Angkot::findOrFail($request->angkot_id);
        $angkot->update(['user_id' => Auth::id()]);
        return back()->with('success', 'Berhasil berganti angkot');
    }
}
