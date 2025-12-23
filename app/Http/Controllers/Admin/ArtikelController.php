<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtikelController extends Controller {
    public function index() {
        $artikels = Artikel::latest()->get();
        return view('admin.artikel.index', compact('artikels'));
    }

    public function create() {
        return view('admin.artikel.create');
    }

    public function store(Request $request) {
        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'kategori' => 'required|in:tips,info_traffic,umum',
            'gambar' => 'nullable|image|max:2048' // Max 2MB
        ]);

        $path = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('artikel', 'public');
        }

        Artikel::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'kategori' => $request->kategori,
            'gambar' => $path
        ]);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dibuat');
    }

    public function edit(Artikel $artikel) {
        return view('admin.artikel.edit', compact('artikel'));
    }

    public function update(Request $request, Artikel $artikel) {
        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'kategori' => 'required|in:tips,info_traffic,umum',
            'gambar' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($artikel->gambar) {
                Storage::disk('public')->delete($artikel->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('artikel', 'public');
        }

        $artikel->update($data);
        return redirect()->route('admin.artikel.index')->with('success', 'Artikel diperbarui');
    }

    public function destroy(Artikel $artikel) {
        if ($artikel->gambar) {
            Storage::disk('public')->delete($artikel->gambar);
        }
        $artikel->delete();
        return redirect()->route('admin.artikel.index')->with('success', 'Artikel dihapus');
    }
}