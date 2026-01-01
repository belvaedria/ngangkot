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

    public function trayekIndex()
    {
        return view('passenger.trayek.index');
    }

}
