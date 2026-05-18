<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckWallet
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (!$user->wallet) {
                \App\Models\Wallet::create([
                    'user_id'   => $user->id,
                    'balance'   => 0,
                    'is_active' => true,
                ]);
            }
        }

        return $next($request);
    }
}