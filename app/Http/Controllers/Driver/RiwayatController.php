<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPenumpang;
use App\Models\RuteFavorit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Untuk keperluan UI: cek cepat apakah sebuah riwayat sudah difavoritkan
        // Key disusun dari asal_coords|tujuan_coords
        $favoritMap = $favorit
            ->keyBy(fn ($f) => $f->asal_coords . '|' . $f->tujuan_coords)
            ->map(fn ($f) => $f->id);
        
        return view('passenger.riwayat.index', compact('riwayat', 'favorit', 'favoritMap'));
    }

    /**
     * Menyimpan rute ke daftar favorit.
     */
    public function storeFavorit(Request $request)
    {
        // Validasi input
        // label dibuat opsional supaya dari card riwayat bisa langsung "bintang" tanpa modal form tambahan.
        $request->validate([
            'label' => 'nullable|string|max:50', // Misal: "Rumah", "Kampus" (opsional)
            'asal_nama' => 'required|string',
            'tujuan_nama' => 'required|string',
            'asal_coords' => 'required|string', // Format: "lat,long"
            'tujuan_coords' => 'required|string',
        ]);

        $labelDefault = trim(($request->asal_nama ?? '') . ' → ' . ($request->tujuan_nama ?? ''));
        $label = $request->label ?: mb_strimwidth($labelDefault, 0, 50, '');

        // Hindari duplikat favorit untuk rute yang sama
        $favorit = RuteFavorit::where('user_id', Auth::id())
            ->where('asal_coords', $request->asal_coords)
            ->where('tujuan_coords', $request->tujuan_coords)
            ->first();

        if ($favorit) {
            return back()->with('success', 'Rute ini sudah ada di favorit.');
        }

        RuteFavorit::create([
            'user_id' => Auth::id(),
            'nama_label' => $label,
            'asal_nama' => $request->asal_nama,
            'tujuan_nama' => $request->tujuan_nama,
            'asal_coords' => $request->asal_coords,
            'tujuan_coords' => $request->tujuan_coords,
        ]);

        return back()->with('success', 'Rute ditambahkan ke favorit!');
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