<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id', 'balance', 'total_topup', 'total_spent', 'is_active',
    ];

    protected $casts = [
        'balance'     => 'decimal:0',
        'total_topup' => 'decimal:0',
        'total_spent' => 'decimal:0',
        'is_active'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    public function topupRequests()
    {
        return $this->hasMany(TopupRequest::class)->latest();
    }

    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount && $this->is_active;
    }
}