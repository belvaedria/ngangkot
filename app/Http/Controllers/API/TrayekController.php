<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Trayek;
use Illuminate\Http\Request;

class TrayekController extends Controller
{
    public function index(Request $request)
    {
        $query = Trayek::query();

        // LOGIKA PENCARIAN SAKTI (Nama Angkot ATAU Nama Jalan)
        if ($request->has('q')) {
            $keyword = strtolower($request->q);
            $query->where(function($q) use ($keyword) {
                $q->where('nama_trayek', 'like', "%{$keyword}%")
                  ->orWhere('kode_trayek', 'like', "%{$keyword}%")
                  // Mencari text di dalam JSON array daftar_jalan
                  ->orWhere('daftar_jalan', 'like', "%{$keyword}%");
            });
        }

        // Secara default, sembunyikan rute balik (kecuali diminta)
        if (!$request->has('show_all')) {
            $query->where('tampil_di_menu', true);
        }

        return response()->json($query->get());
    }

    public function show($kode)
    {
        $trayek = Trayek::where('kode_trayek', $kode)->first();
        return $trayek ? response()->json($trayek) : response()->json(['message'=>'Not found'], 404);
    }
}