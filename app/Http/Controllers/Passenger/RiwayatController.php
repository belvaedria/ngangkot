<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPenumpang;
use App\Models\RuteFavorit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RiwayatController extends Controller
{
    /**
     * Menampilkan daftar riwayat pencarian dan rute favorit user.
     */
    public function index()
    {
        // 1. Ambil Riwayat Pencarian (Terbaru di atas)
        $riwayat = RiwayatPenumpang::where('user_id', Auth::id())
                    ->latest()
                    ->take(20) // Batasi 20 terakhir biar gak berat
                    ->get();

        // 2. Ambil Daftar Favorit
        $favorit = RuteFavorit::where('user_id', Auth::id())
                    ->latest()
                    ->get();
        
        return view('passenger.riwayat.index', compact('riwayat', 'favorit'));
    }

    private function firstPhrase(string $text): string
    {
        $first = explode(',', $text)[0] ?? $text;
        return trim($first);
    }

    /**
     * Menyimpan rute ke daftar favorit.
     */
    public function storeFavorit(Request $request)
    {

        // Validasi input
        $request->validate([
            'label' => 'required|string', // Misal: "Rumah", "Kampus"
            'asal_nama' => 'required|string',
            'tujuan_nama' => 'required|string',
            'asal_coords' => 'required|string', // Format: "lat,long"
            'tujuan_coords' => 'required|string',
        ]);

        $asalSingkat = $this->firstPhrase($request->asal_nama);
        $tujuanSingkat = $this->firstPhrase($request->tujuan_nama);

        $label = $asalSingkat . ' → ' . $tujuanSingkat;
        $label = substr($label, 0, 50);


        // Simpan ke database
        RuteFavorit::create([
            'user_id' => Auth::id(),
            'nama_label' => $label,
            'asal_nama' => $request->asal_nama,
            'tujuan_nama' => $request->tujuan_nama,
            'asal_coords' => $request->asal_coords,
            'tujuan_coords' => $request->tujuan_coords
        ]);

        return back()->with('success', 'Rute berhasil ditambahkan ke favorit!');
    }

    /**
     * Menghapus rute dari daftar favorit.
     */
    public function destroyFavorit($id)
    {
        // Pastikan hanya bisa hapus punya sendiri
        $favorit = RuteFavorit::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();
                    
        $favorit->delete();

        return back()->with('success', 'Rute dihapus dari favorit.');
    }
}