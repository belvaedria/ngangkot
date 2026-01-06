<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Trayek;
use Illuminate\Http\Request;

class CekJalurController extends Controller
{
    public function index(Request $request)
    {
        $query = Trayek::where('tampil_di_menu', true);
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_trayek', 'like', "%{$search}%")
                  ->orWhere('nama_trayek', 'like', "%{$search}%");
            });
        }
        
        $trayeks = $query->get();
        
        return view('driver.cek-jalur.index', compact('trayeks'));
    }
    
    public function show($kode)
    {
        $trayek = Trayek::where('kode_trayek', $kode)->firstOrFail();
        return view('driver.cek-jalur.show', compact('trayek'));
    }
}
