<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['membership','wallet'])->withTrashed();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ilike', '%'.$request->search.'%')
                  ->orWhere('email', 'ilike', '%'.$request->search.'%');
            });
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function show(int $id)
    {
        $user = User::with([
            'membership','wallet','loyaltyPoint',
            'orders' => fn($q) => $q->latest()->take(10),
            'loginHistories' => fn($q) => $q->latest()->take(5),
        ])->withTrashed()->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function suspend(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'suspended']);
        return response()->json(['success' => true, 'message' => "User {$user->name} disuspend."]);
    }

    public function activate(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->update(['status' => 'active']);
        $user->restore();
        return response()->json(['success' => true, 'message' => "User {$user->name} diaktifkan."]);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:superadmin,admin,manager,kasir,kitchen,customer',
            'password' => 'required|min:8|confirmed',
            'status'   => 'required|in:active,suspended',
        ]);

        $user = \App\Models\User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            'status'   => $request->status,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        \App\Models\UserProfile::create(['user_id' => $user->id, 'display_name' => $user->name, 'profile_completion' => 40]);
        \App\Models\Wallet::create(['user_id' => $user->id, 'balance' => 0, 'is_active' => true]);
        \App\Models\Membership::create(['user_id' => $user->id, 'tier' => 'none', 'completed_orders' => 0, 'cashback_rate' => 0, 'is_active' => true]);
        \App\Models\LoyaltyPoint::create(['user_id' => $user->id, 'total_points' => 0, 'used_points' => 0, 'available_points' => 0]);

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "User {$user->name} berhasil dibuat!");
    }
}