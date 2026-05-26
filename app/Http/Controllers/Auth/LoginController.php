<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user->status === 'suspended') {
                Auth::logout();
                return back()->with('error', 'Akun Anda telah disuspend.');
            }

            // Log login history
            LoginHistory::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device'     => $this->detectDevice($request->userAgent()),
                'success'    => true,
                'logged_at'  => now(),
            ]);

            $user->update(['last_login_at' => now()]);
            $request->session()->regenerate();

            return $this->redirectByRole($user);
        }

        // Log failed attempt
        $user = User::where('email', $request->email)->first();
        if ($user) {
            LoginHistory::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'success'    => false,
                'logged_at'  => now(),
            ]);
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput($request->only('email'));
    }

    private function redirectByRole(User $user)
    {
        switch ($user->role) {
            case 'superadmin':
            case 'admin':
            case 'manager':
                return redirect()->route('admin.dashboard');
            case 'kasir':
                return redirect()->route('kasir.dashboard');
            case 'kitchen':
                return redirect()->route('kitchen.display');
            default:
                return redirect()->route('home');
        }
    }

    private function detectDevice(string $ua): string
    {
        if (str_contains($ua, 'Mobile')) return 'Mobile';
        if (str_contains($ua, 'Tablet')) return 'Tablet';
        return 'Desktop';
    }
}