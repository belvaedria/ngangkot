<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Trayek;
use Illuminate\Http\Request;

class TrayekController extends Controller {
    public function index() {
        $trayeks = Trayek::all();
        return view('admin.trayek.index', compact('trayeks'));
    }
    
    public function create() {
        return view('admin.trayek.create');
    }

    public function store(Request $request) {
        // Validasi input
        $validated = $request->validate([
            'kode_trayek' => 'required|unique:trayeks',
            'nama_trayek' => 'required',
            'lat_asal' => 'required|numeric',
            'lng_asal' => 'required|numeric',
            'lat_tujuan' => 'required|numeric',
            'lng_tujuan' => 'required|numeric',
            'rute_json' => 'nullable', // JSON string dari geojson.io
            'daftar_jalan' => 'nullable|array', // Input array dari form
            'warna_angkot' => 'required',
            'harga' => 'required|numeric'
        ]);

        Trayek::create($validated);
        return redirect()->route('admin.trayek.index')->with('success', 'Trayek berhasil ditambahkan');
    }

    public function edit($id) {
        $trayek = Trayek::findOrFail($id);
        return view('admin.trayek.edit', compact('trayek'));
    }

    public function update(Request $request, $id) {
        $trayek = Trayek::findOrFail($id);
        
        $validated = $request->validate([
            'kode_trayek' => 'required|unique:trayeks,kode_trayek,'.$id,
            'nama_trayek' => 'required',
            'lat_asal' => 'required|numeric',
            'lng_asal' => 'required|numeric',
            'lat_tujuan' => 'required|numeric',
            'lng_tujuan' => 'required|numeric',
            'rute_json' => 'nullable',
            'daftar_jalan' => 'nullable|array',
            'warna_angkot' => 'required',
            'harga' => 'required|numeric'
        ]);

        $trayek->update($validated);
        return redirect()->route('admin.trayek.index')->with('success', 'Data trayek diperbarui');
    }

    public function destroy($id) {
        $trayek = Trayek::findOrFail($id);
        $trayek->delete();
        return redirect()->route('admin.trayek.index')->with('success', 'Trayek dihapus');
    }
}
