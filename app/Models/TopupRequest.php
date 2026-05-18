<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupRequest extends Model
{
    protected $fillable = [
        'user_id', 'wallet_id', 'transaction_code', 'amount', 'payment_method',
        'proof_image', 'status', 'admin_note', 'approved_by', 'approved_at', 'expires_at',
    ];

    protected $casts = [
        'amount'      => 'decimal:0',
        'approved_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function wallet()   { return $this->belongsTo(Wallet::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }

    public function getProofUrlAttribute(): ?string
    {
        return $this->proof_image ? asset('storage/' . $this->proof_image) : null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public static function generateCode(): string
    {
        return 'TOP-' . strtoupper(substr(md5(uniqid()), 0, 10));
    }
}