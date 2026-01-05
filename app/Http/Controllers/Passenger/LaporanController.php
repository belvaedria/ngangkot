<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar laporan milik user yang sedang login.
     */
    public function index()
    {
        $laporans = Laporan::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('passenger.laporan.index', compact('laporans'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat laporan baru.
     */
    public function create()
    {
        return view('passenger.laporan.create');
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan laporan baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string|min:20',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'judul.required' => 'Judul laporan wajib diisi.',
            'judul.max' => 'Judul laporan maksimal 255 karakter.',
            'isi.required' => 'Isi laporan wajib diisi.',
            'isi.min' => 'Isi laporan minimal 20 karakter.',
            'bukti_foto.image' => 'File harus berupa gambar.',
            'bukti_foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'bukti_foto.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $path = null;
        if ($request->hasFile('bukti_foto')) {
            $path = $request->file('bukti_foto')->store('laporan', 'public');
        }

        Laporan::create([
            'user_id' => Auth::id(),
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'bukti_foto' => $path,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('passenger.laporan.index')
            ->with('success', 'Laporan berhasil dikirim! Terima kasih atas kontribusi Anda.');
    }

    /**
     * Display the specified resource.
     * Menampilkan detail laporan.
     */
    public function show(Laporan $laporan)
    {
        // Pastikan user hanya bisa melihat laporan miliknya
        if ($laporan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        return view('passenger.laporan.show', compact('laporan'));
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form edit laporan (hanya jika status masih pending).
     */
    public function edit(Laporan $laporan)
    {
        // Pastikan user hanya bisa edit laporan miliknya
        if ($laporan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        // Hanya bisa edit jika status masih pending
        if ($laporan->status !== 'pending') {
            return redirect()
                ->route('passenger.laporan.index')
                ->with('error', 'Laporan yang sedang diproses atau selesai tidak dapat diedit.');
        }

        return view('passenger.laporan.edit', compact('laporan'));
    }

    /**
     * Update the specified resource in storage.
     * Memperbarui laporan yang sudah ada.
     */
    public function update(Request $request, Laporan $laporan)
    {
        // Pastikan user hanya bisa update laporan miliknya
        if ($laporan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        // Hanya bisa update jika status masih pending
        if ($laporan->status !== 'pending') {
            return redirect()
                ->route('passenger.laporan.index')
                ->with('error', 'Laporan yang sedang diproses atau selesai tidak dapat diedit.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string|min:20',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'judul.required' => 'Judul laporan wajib diisi.',
            'judul.max' => 'Judul laporan maksimal 255 karakter.',
            'isi.required' => 'Isi laporan wajib diisi.',
            'isi.min' => 'Isi laporan minimal 20 karakter.',
            'bukti_foto.image' => 'File harus berupa gambar.',
            'bukti_foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'bukti_foto.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $path = $laporan->bukti_foto;
        if ($request->hasFile('bukti_foto')) {
            // Hapus foto lama jika ada
            if ($laporan->bukti_foto) {
                Storage::disk('public')->delete($laporan->bukti_foto);
            }
            $path = $request->file('bukti_foto')->store('laporan', 'public');
        }

        $laporan->update([
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'bukti_foto' => $path,
        ]);

        return redirect()
            ->route('passenger.laporan.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus laporan (hanya jika status masih pending).
     */
    public function destroy(Laporan $laporan)
    {
        // Pastikan user hanya bisa hapus laporan miliknya
        if ($laporan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        // Hanya bisa hapus jika status masih pending
        if ($laporan->status !== 'pending') {
            return redirect()
                ->route('passenger.laporan.index')
                ->with('error', 'Laporan yang sedang diproses atau selesai tidak dapat dihapus.');
        }

        // Hapus foto jika ada
        if ($laporan->bukti_foto) {
            Storage::disk('public')->delete($laporan->bukti_foto);
        }

        $laporan->delete();

        return redirect()
            ->route('passenger.laporan.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}