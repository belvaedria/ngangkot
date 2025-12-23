<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Cek login
        if (! $request->user()) {
            return redirect('/login');
        }

        // 2. Cek Role
        if ($request->user()->role !== $role) {
            abort(403, 'Akses Ditolak! Halaman ini khusus ' . $role);
        }

        return $next($request);
    }
}
