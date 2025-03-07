<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if the user is authenticated and has the required role
        if (Auth::check() && Auth::user()->role == $role) {
            return $next($request);
        }
        return response()->json([
            'error' => 'You do not have permission to access this page.'
        ], Response::HTTP_FORBIDDEN);}
}