<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user         = User::with(['profile', 'wallet', 'membership', 'loyaltyPoint'])->find(Auth::id());
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
        $user = User::find(Auth::id());

        $request->validate([
            'name'         => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'bio'          => 'nullable|string|max:300',
            'display_name' => 'nullable|string|max:100',
            'birthdate'    => 'nullable|date|before:today',
            'gender'       => 'nullable|in:male,female,other',
            'address'      => 'nullable|string|max:300',
            'city'         => 'nullable|string|max:100',
        ]);

        User::where('id', Auth::id())->update($request->only('name', 'phone', 'bio'));

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

        return back()->with('success', 'Profil berhasil diperbarui! 👤');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048']);

        $user = User::find(Auth::id());

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        User::where('id', Auth::id())->update(['avatar' => $path]);

        // Refresh user untuk ambil avatar terbaru
        $user = User::find(Auth::id());

        return response()->json(['success' => true, 'avatar_url' => $user->avatar_url]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        User::where('id', Auth::id())->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diperbarui! 🔒');
    }

    public function toggleDarkMode(Request $request)
    {
        $user = User::find(Auth::id());
        User::where('id', Auth::id())->update(['dark_mode' => !$user->dark_mode]);

        // Refresh untuk ambil nilai terbaru
        $user = User::find(Auth::id());

        return response()->json(['dark_mode' => $user->dark_mode]);
    }
}