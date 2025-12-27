<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureFriendlyAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        // If AJAX / expects JSON, return JSON hint
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Mohon maaf, fitur ini hanya tersedia untuk wargi yang sudah bergabung. Silakan masuk terlebih dahulu.',
                'login_url' => route('login')
            ], 401);
        }

        // Otherwise show a friendly view with options to Login or go back
        $loginUrl = route('login', ['redirect' => $request->fullUrl()]);
        return response()->view('auth.notice', [
            'message' => 'Mohon maaf, fitur ini hanya tersedia untuk wargi yang sudah bergabung. Silakan masuk terlebih dahulu.',
            'loginUrl' => $loginUrl,
            'backUrl' => url()->previous()
        ])->setStatusCode(401);
    }
}
