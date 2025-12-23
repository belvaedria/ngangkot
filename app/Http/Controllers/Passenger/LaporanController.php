<?php
namespace App\Http\Controllers\Passenger;
use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index() {
        // Lihat riwayat laporan saya
        $laporans = Laporan::where('user_id', Auth::id())->latest()->get();
        return view('passenger.laporan.index', compact('laporans'));
    }

    public function create() {
        return view('passenger.laporan.create');
    }

    public function store(Request $request) {
        $request->validate(['judul' => 'required', 'isi' => 'required', 'bukti_foto' => 'nullable|image']);
        
        $path = $request->file('bukti_foto') ? $request->file('bukti_foto')->store('laporan', 'public') : null;

        Laporan::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'isi' => $request->isi,
            'bukti_foto' => $path,
            'status' => 'pending'
        ]);
        return redirect()->route('passenger.laporan.index')->with('success', 'Laporan terkirim.');
    }
}