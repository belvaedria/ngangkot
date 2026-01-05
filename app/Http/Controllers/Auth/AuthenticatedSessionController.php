<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Mapping dari input form ke role database
        $loginAs = $request->input('login_as', 'wargi');
        $roleMapping = [
            'wargi' => 'passenger',
            'driver' => 'driver',
            'admin' => 'admin',
        ];
        $mappedRole = $roleMapping[$loginAs] ?? 'passenger';
        $userRole = Auth::user()->role;

        // Cek apakah role yang dipilih sesuai dengan role user
        if ($mappedRole !== $userRole) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Role yang dipilih tidak sesuai dengan akun Anda. Silakan pilih role yang benar.',
            ])->withInput($request->only('email'));
        }

        // Redirect berdasarkan role
        return match($userRole) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'driver' => redirect()->intended(route('driver.dashboard')),
            default => redirect()->intended(route('passenger.dashboard')),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
