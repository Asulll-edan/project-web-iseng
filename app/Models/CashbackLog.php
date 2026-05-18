<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbackLog extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'amount', 'cashback_rate', 'membership_tier', 'description',
    ];

    protected $casts = ['amount' => 'decimal:0', 'cashback_rate' => 'float'];

    public function user()  { return $this->belongsTo(User::class); }
    public function order() { return $this->belongsTo(Order::class); }
}