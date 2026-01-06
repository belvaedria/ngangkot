<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Angkot;
use App\Models\DriverProfile;
use App\Models\Trayek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilArmadaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = DriverProfile::where('user_id', $user->id)->first();
        $angkot = Angkot::where('user_id', $user->id)->with('trayek')->first();
        
        // Simulasi saldo driver (bisa ditambahkan ke tabel nanti)
        $saldo = session('driver_saldo', 452000);
        
        return view('driver.profil-armada.index', compact('user', 'profile', 'angkot', 'saldo'));
    }
    
    public function edit()
    {
        $user = Auth::user();
        $profile = DriverProfile::where('user_id', $user->id)->first();
        $angkot = Angkot::where('user_id', $user->id)->first();
        $trayeks = Trayek::all();
        
        return view('driver.profil-armada.edit', compact('user', 'profile', 'angkot', 'trayeks'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Update angkot data
        $angkot = Angkot::where('user_id', $user->id)->first();
        
        if ($angkot) {
            $angkot->update([
                'plat_nomor' => $request->plat_nomor,
                'trayek_id' => $request->trayek_id,
            ]);
        } else {
            // Create new angkot if not exists
            $request->validate([
                'plat_nomor' => 'required|unique:angkots',
                'trayek_id' => 'required|exists:trayeks,id',
            ]);
            
            Angkot::create([
                'user_id' => $user->id,
                'plat_nomor' => $request->plat_nomor,
                'trayek_id' => $request->trayek_id,
                'is_active' => false,
            ]);
        }
        
        // Update driver profile
        $profile = DriverProfile::firstOrCreate(['user_id' => $user->id]);
        $profile->update([
            'nomor_sim' => $request->nomor_sim,
            'alamat_domisili' => $request->alamat_domisili,
        ]);
        
        return redirect()->route('driver.profil.index')->with('success', 'Profil berhasil diperbarui');
    }
}
