<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverProfileController extends Controller
{
    public function edit(Request $request)
    {
        $profile = DriverProfile::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['status' => 'pending']
        );

        return view('driver.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nomor_sim' => ['required', 'string', 'max:50'],
            'alamat_domisili' => ['required', 'string', 'max:255'],
            'foto_ktp' => ['nullable', 'image', 'max:2048'],
            'foto_sim' => ['nullable', 'image', 'max:2048'],
        ]);

        $profile = DriverProfile::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['status' => 'pending']
        );

        $data = $request->only('nomor_sim', 'alamat_domisili');

        if ($request->hasFile('foto_ktp')) {
            $data['foto_ktp'] = $request->file('foto_ktp')->store('driver/ktp', 'public');
        }
        if ($request->hasFile('foto_sim')) {
            $data['foto_sim'] = $request->file('foto_sim')->store('driver/sim', 'public');
        }

        // setiap update profil => balik ke pending (biar aman kalau driver edit ulang)
        $data['status'] = 'pending';

        $profile->update($data);

        return redirect()->route('driver.verification.waiting')
            ->with('success', 'Profil terkirim. Mohon tunggu verifikasi admin ya 😊');
    }

    public function waiting(Request $request)
    {
        $profile = DriverProfile::where('user_id', $request->user()->id)->first();
        return view('driver.verification.waiting', compact('profile'));
    }
}
