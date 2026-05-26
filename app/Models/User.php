<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role',
        'status', 'avatar', 'bio', 'dark_mode', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'dark_mode'         => 'boolean',
    ];

    // ── Roles ─────────────────────────────────────────────
    public function isSuperadmin(): bool { return $this->role === 'superadmin'; }
    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isManager(): bool    { return $this->role === 'manager'; }
    public function isKasir(): bool      { return $this->role === 'kasir'; }
    public function isKitchen(): bool    { return $this->role === 'kitchen'; }
    public function isCustomer(): bool   { return $this->role === 'customer'; }
    public function isActive(): bool     { return $this->status === 'active'; }
    public function isStaff(): bool      { return in_array($this->role, ['superadmin','admin','manager','kasir','kitchen']); }
    public function canAccessAdmin(): bool { return in_array($this->role, ['superadmin','admin','manager']); }
    public function hasPermission(string $permission): bool {
        return \App\Models\RolePermission::roleHas($this->role, $permission);
    }

    // ── Relationships ──────────────────────────────────────
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function topupRequests()
    {
        return $this->hasMany(TopupRequest::class);
    }

    public function membership()
    {
        return $this->hasOne(Membership::class);
    }

    public function loyaltyPoint()
    {
        return $this->hasOne(LoyaltyPoint::class);
    }

    public function loyaltyPointLogs()
    {
        return $this->hasMany(LoyaltyPointLog::class);
    }

    public function cashbackLogs()
    {
        return $this->hasMany(CashbackLog::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(MenuReview::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ── Helpers ────────────────────────────────────────────
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=5a7c65&color=fff&size=128";
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }
}