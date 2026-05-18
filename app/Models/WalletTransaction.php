<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id', 'user_id', 'transaction_code', 'type',
        'amount', 'balance_before', 'balance_after', 'description', 'reference_id',
    ];

    protected $casts = [
        'amount'         => 'decimal:0',
        'balance_before' => 'decimal:0',
        'balance_after'  => 'decimal:0',
    ];

    public function wallet() { return $this->belongsTo(Wallet::class); }
    public function user()   { return $this->belongsTo(User::class); }

    public static function generateCode(): string
    {
        return 'TXN-' . strtoupper(substr(md5(uniqid()), 0, 10));
    }
}