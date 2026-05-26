<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\LoginHistory;
use App\Models\User;
    

class ProfileController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $user->load(['profile', 'wallet', 'membership', 'loyaltyPoint']);
        $orderStats   = [
            'total'     => $user->orders()->count(),
            'completed' => $user->orders()->where('status', 'completed')->count(),
            'active'    => $user->orders()->whereIn('status', ['menunggu','cooking','selesai'])->count(),
        ];
        $loginHistory = $user->loginHistories()->latest()->take(5)->get();

        return view('customer.profile.index', compact('user', 'orderStats', 'loginHistory'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */

        $user = Auth::user();

        $request->validate([
            'name'         => 'required|string|max:100',
            'phone'        => 'nullable|string|max:20',
            'bio'          => 'nullable|string|max:300',
            'display_name' => 'nullable|string|max:100',
            'birthdate'    => 'nullable|date|before:today',
            'gender'       => 'nullable|in:male,female,other',
            'address'      => 'nullable|string|max:300',
            'city'         => 'nullable|string|max:100',
        ]);

        // --- Cek apakah ada perubahan ---
        $userChanged = (
            $request->name         !== $user->name ||
            $request->phone        !== $user->phone ||
            $request->bio          !== $user->bio
        );

        $profile = $user->profile;
        $profileChanged = (
            $request->display_name !== ($profile->display_name ?? '') ||
            $request->birthdate    !== (optional($profile->birthdate)->format('Y-m-d') ?? '') ||
            $request->gender       !== ($profile->gender ?? '') ||
            $request->address      !== ($profile->address ?? '') ||
            $request->city         !== ($profile->city ?? '')
        );

        if (!$userChanged && !$profileChanged) {
            return back()->with('info', 'Tidak ada perubahan yang perlu disimpan.');
        }

        if ($userChanged) {
            $user->update($request->only('name', 'phone', 'bio'));
        }

        if ($profileChanged) {
            $completion = 40;
            if ($request->filled('bio'))          $completion += 10;
            if ($request->filled('birthdate'))    $completion += 10;
            if ($request->filled('address'))      $completion += 10;
            if ($user->avatar)                    $completion += 15;
            if ($request->filled('display_name')) $completion += 15;
            $completion = min(100, $completion);

            $user->profile()->updateOrCreate(['user_id' => $user->id], array_merge(
                $request->only('display_name', 'birthdate', 'gender', 'address', 'city'),
                ['profile_completion' => $completion]
            ));
        }

        return back()->with('success', 'Profil berhasil diperbarui! 👤');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048']);

        /** @var \App\Models\User $user */

        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json(['success' => true, 'avatar_url' => $user->avatar_url]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed|different:current_password',
        ]);
/** @var \App\Models\User $user */

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diperbarui! 🔒');
    }

    public function toggleDarkMode(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update(['dark_mode' => !$user->dark_mode]);
        return response()->json(['dark_mode' => $user->dark_mode]);
    }

    public function updateSecurity(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'action'           => 'required|in:logout_all_devices',
        ]);

        /** @var \App\Models\User $user */

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password tidak sesuai.']);
        }

        if ($request->action === 'logout_all_devices') {
            // Regenerate remember token → invalidate all sessions
            $user->update(['remember_token' => \Illuminate\Support\Str::random(60)]);
            Auth::logout();
            return response()->json(['success' => true, 'message' => 'Semua sesi berhasil diakhiri. Silakan login kembali.', 'redirect' => route('login')]);
        }

        return response()->json(['success' => false, 'message' => 'Aksi tidak dikenali.']);
    }
}