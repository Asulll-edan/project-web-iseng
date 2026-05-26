<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RolePermission extends Model
{
    protected $fillable = ['role', 'permission', 'module', 'is_allowed'];
    protected $casts    = ['is_allowed' => 'boolean'];

    // Default permissions per role
    const DEFAULTS = [
        'superadmin' => ['*'], // semua
        'admin' => [
            'view_dashboard','view_orders','manage_orders','view_menus','manage_menus',
            'view_users','manage_users','view_wallet','view_membership',
            'view_banners','manage_banners','view_vouchers','manage_vouchers',
            'view_reservations','manage_reservations','view_analytics',
            'view_reports','create_reports','export_reports','approve_reports',
            'view_payment_history',
        ],
        'manager' => [
            'view_dashboard','view_orders','view_analytics',
            'view_reports','create_reports','export_reports',
            'view_payment_history','view_wallet','view_membership',
        ],
        'kasir' => [
            'view_orders','manage_order_status',
        ],
        'kitchen' => [
            'view_kitchen_orders','manage_kitchen_status',
        ],
        'customer' => [
            'order','view_own_orders','wallet','membership','reservation','profile',
        ],
    ];

    public static function roleHas(string $role, string $permission): bool
    {
        if ($role === 'superadmin') return true;

        return Cache::remember("perm_{$role}_{$permission}", 300, function () use ($role, $permission) {
            // Check DB override first
            $override = static::where('role', $role)->where('permission', $permission)->first();
            if ($override) return $override->is_allowed;

            // Fall back to defaults
            $defaults = static::DEFAULTS[$role] ?? [];
            return in_array($permission, $defaults) || in_array('*', $defaults);
        });
    }

    public static function getForRole(string $role): array
    {
        return static::where('role', $role)->get()->toArray();
    }

    public static function setPermission(string $role, string $permission, bool $allowed, string $module = null): void
    {
        static::updateOrCreate(
            ['role' => $role, 'permission' => $permission],
            ['is_allowed' => $allowed, 'module' => $module]
        );
        Cache::forget("perm_{$role}_{$permission}");
    }

    public static function allPermissions(): array
    {
        return [
            'Dashboard'   => ['view_dashboard'],
            'Orders'      => ['view_orders','manage_orders','manage_order_status'],
            'Menu'        => ['view_menus','manage_menus'],
            'Users'       => ['view_users','manage_users'],
            'Wallet'      => ['view_wallet','approve_topup'],
            'Membership'  => ['view_membership','approve_platinum'],
            'Banners'     => ['view_banners','manage_banners'],
            'Vouchers'    => ['view_vouchers','manage_vouchers'],
            'Reservations'=> ['view_reservations','manage_reservations'],
            'Analytics'   => ['view_analytics'],
            'Reports'     => ['view_reports','create_reports','export_reports','approve_reports'],
            'Payments'    => ['view_payment_history'],
            'Kitchen'     => ['view_kitchen_orders','manage_kitchen_status'],
            'Settings'    => ['manage_settings'],
        ];
    }
}