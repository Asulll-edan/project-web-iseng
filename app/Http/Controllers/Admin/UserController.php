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
}
