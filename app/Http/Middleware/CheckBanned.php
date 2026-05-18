<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->status === 'suspended') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah disuspend. Hubungi admin untuk informasi lebih lanjut.');
        }

        return $next($request);
    }
}