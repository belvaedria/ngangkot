<?php

namespace App\Http\Controllers;

use App\Models\Trayek;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        // Kita kirim data trayek dasar ke view untuk isi Sidebar
        // FILTER: Hanya ambil yang tampil_di_menu = true
        $trayeks = Trayek::where('tampil_di_menu', true)
                        ->select('id', 'kode_trayek', 'nama_trayek', 'warna_angkot')
                        ->get();
        return view('welcome', compact('trayeks'));
    }

    // Halaman publik untuk melihat daftar trayek
    public function trayekIndex()
    {
        // Hanya tampil trayek yang "tampil_di_menu" = true
        $trayeks = Trayek::where('tampil_di_menu', true)->orderBy('kode_trayek')->get();
        return view('public.trayek.index', compact('trayeks'));
    }

    // Tampilkan detail trayek berdasarkan kode (public)
    public function show($kode)
    {
        $trayek = Trayek::where('kode_trayek', $kode)->firstOrFail();
        return view('public.trayek.show', compact('trayek'));
    }
}
