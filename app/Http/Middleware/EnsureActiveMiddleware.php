<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->status !== 'active') {
            // Log out the user if their status is not active
            auth()->logout();

            return redirect()->route('login')->with('error', 'Status akun Anda saat ini tidak aktif.');
        }

        return $next($request);
    }
}
