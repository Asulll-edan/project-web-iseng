<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyPoint;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'required|string|max:20',
            'password'              => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        UserProfile::create([
            'user_id'            => $user->id,
            'display_name'       => $user->name,
            'profile_completion' => 40,
        ]);

        Wallet::create([
            'user_id'   => $user->id,
            'balance'   => 0,
            'is_active' => true,
        ]);

        Membership::create([
            'user_id'          => $user->id,
            'tier'             => 'none',
            'completed_orders' => 0,
            'cashback_rate'    => 0,
            'is_active'        => true,
        ]);

        LoyaltyPoint::create([
            'user_id'          => $user->id,
            'total_points'     => 0,
            'used_points'      => 0,
            'available_points' => 0,
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Selamat datang di Rumahnya Anak Sekolah! 🎉');
    }
}