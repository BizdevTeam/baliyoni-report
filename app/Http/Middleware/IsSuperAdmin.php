<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
                // Periksa apakah pengguna sudah login dan perannya adalah 'superadmin'
        if (Auth::check() && Auth::user()->role == 'superadmin') {
            // Jika ya, lanjutkan request
            return $next($request);
        }

        // Jika tidak, kembalikan halaman error 403 (Forbidden)
        abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
    }
}
