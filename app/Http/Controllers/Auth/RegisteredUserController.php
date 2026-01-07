<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\DriverProfile;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:wargi,driver'], // Terima 'wargi' dari form
        ]);

        // Mapping dari input form ke role database
        $roleMapping = [
            'wargi' => 'passenger',
            'driver' => 'driver',
        ];
        $dbRole = $roleMapping[$request->role] ?? 'passenger';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $dbRole, // Simpan sebagai 'passenger' di database
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($user->role === 'driver') {
            DriverProfile::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => 'pending']
            );
            return redirect()->route('driver.profile.edit');
        }

        return redirect()->route('passenger.dashboard');

        
    }
}
