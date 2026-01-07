<?php

namespace App\Http\Middleware;

use App\Models\DriverProfile;
use Closure;
use Illuminate\Http\Request;

class EnsureDriverVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // kalau bukan driver, biarin (harusnya gak kepakai juga)
        if (!$user || $user->role !== 'driver') {
            return $next($request);
        }

        $profile = DriverProfile::where('user_id', $user->id)->first();

        // belum diverifikasi -> tampil halaman tunggu
        if ($profile->status !== 'verified') {
            return redirect()->route('driver.verification.waiting');
        }

        if (!$profile || !$profile->nomor_sim || !$profile->alamat_domisili) {
            return redirect()->route('driver.profile.edit');
        }

        return $next($request);
    }
}
