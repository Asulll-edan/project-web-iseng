<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

/** @var \App\Models\User $user */
$user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan. Hubungi admin.');
        }

        if (!in_array($user->role, $roles)) {
            // Redirect ke panel yang sesuai bukan abort
            if ($user->isSuperadmin() || $user->isAdmin() || $user->isManager()) {
                return redirect()->route('admin.dashboard')->with('error', 'Akses tidak diizinkan.');
            }
            if ($user->isKasir()) {
                return redirect()->route('kasir.dashboard')->with('error', 'Akses tidak diizinkan.');
            }
            if ($user->isKitchen()) {
                return redirect()->route('kitchen.display')->with('error', 'Akses tidak diizinkan.');
            }
            return redirect()->route('home')->with('error', 'Akses tidak diizinkan.');
        }

        return $next($request);
    }
}