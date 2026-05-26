<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles       = ['admin','manager','kasir','kitchen','customer'];
        $allPerms    = RolePermission::allPermissions();
        $permissions = [];

        foreach ($roles as $role) {
            $dbPerms = RolePermission::where('role', $role)->pluck('is_allowed','permission')->toArray();
            $permissions[$role] = [];
            foreach ($allPerms as $module => $perms) {
                foreach ($perms as $perm) {
                    $default  = in_array($perm, RolePermission::DEFAULTS[$role] ?? []);
                    $permissions[$role][$perm] = $dbPerms[$perm] ?? $default;
                }
            }
        }

        return view('admin.role-permissions.index', compact('roles','allPerms','permissions'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'role'        => 'required|in:admin,manager,kasir,kitchen,customer',
            'permissions' => 'array',
        ]);

        $role    = $request->role;
        $allPerms= collect(RolePermission::allPermissions())->flatten()->toArray();

        foreach ($allPerms as $perm) {
            $allowed = in_array($perm, $request->permissions ?? []);
            RolePermission::setPermission($role, $perm, $allowed);
        }

        return response()->json(['success' => true, 'message' => "Permission role {$role} berhasil disimpan."]);
    }
}