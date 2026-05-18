<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'payment_code', 'amount',
        'method', 'status', 'metadata', 'paid_at',
    ];

    protected $casts = [
        'amount'   => 'decimal:0',
        'metadata' => 'array',
        'paid_at'  => 'datetime',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function user()  { return $this->belongsTo(User::class); }

    public static function generatePaymentCode(): string
    {
        return 'PAY-' . strtoupper(substr(md5(uniqid()), 0, 10));
    }
}