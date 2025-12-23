<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;

class LaporanController extends Controller {
    public function index() {
        $laporans = Laporan::with('user')->latest()->get();
        return view('admin.laporan.index', compact('laporans'));
    }
    public function update(Request $request, $id) {
        $laporan = Laporan::findOrFail($id);
        $laporan->update([
            'status' => $request->status, // 'diproses' / 'selesai'
            'tanggapan_admin' => $request->tanggapan
        ]);
        return back()->with('success', 'Tanggapan dikirim');
    }
}
