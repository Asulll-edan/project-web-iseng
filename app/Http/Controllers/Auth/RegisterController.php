<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Models\LoyaltyPoint;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
            'status'   => 'pending',
        ]);

        $otp = rand(100000, 999999);

        EmailVerification::where('user_id', $user->id)->delete();

EmailVerification::create([
    'user_id'    => $user->id,
    'otp'        => $otp,
    'expired_at' => now()->addMinutes(10),
]);

// try {

//     Mail::send(
//         'emails.otp',
//         [
//             'otp' => $otp,
//             'name' => $user->name
//         ],
//         function ($message) use ($user) {
//             $message->to($user->email)
//                 ->subject('Kode Verifikasi Akun');
//         }
//     );

// } catch (\Exception $e) {

//     dd($e->getMessage());

// }

return redirect()->route('verify.otp.form', $user->id);

        return redirect()->route('verify.otp.form', $user->id);
    }

    public function showOtpForm(User $user)
    {
        return view('auth.verify-otp', compact('user'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp'     => 'required|digits:6',
        ]);

        $verification = EmailVerification::where(
            'user_id',
            $request->user_id
        )->latest()->first();

        if (!$verification) {
            return back()->withErrors([
                'otp' => 'OTP tidak ditemukan.'
            ]);
        }

        if (now()->gt($verification->expired_at)) {
            return back()->withErrors([
                'otp' => 'OTP sudah kadaluarsa.'
            ]);
        }

        if ($verification->otp != $request->otp) {
            return back()->withErrors([
                'otp' => 'OTP yang Anda masukkan salah.'
            ]);
        }

        $user = User::findOrFail($request->user_id);

        $user->update([
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        $verification->delete();

        UserProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'display_name'       => $user->name,
                'profile_completion' => 40,
            ]
        );

        Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance'   => 0,
                'is_active' => true,
            ]
        );

        Membership::firstOrCreate(
            ['user_id' => $user->id],
            [
                'tier'             => 'none',
                'completed_orders' => 0,
                'cashback_rate'    => 0,
                'is_active'        => true,
            ]
        );

        LoyaltyPoint::firstOrCreate(
            ['user_id' => $user->id],
            [
                'total_points'     => 0,
                'used_points'      => 0,
                'available_points' => 0,
            ]
        );

        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Email berhasil diverifikasi 🎉');
    }
}