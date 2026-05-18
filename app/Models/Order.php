<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    const STATUS_MENUNGGU  = 'menunggu';
    const STATUS_COOKING   = 'cooking';
    const STATUS_SELESAI   = 'selesai';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DIBATALKAN= 'dibatalkan';

    const STATUS_LABELS = [
        'menunggu'   => ['label' => 'Menunggu', 'color' => 'amber',  'icon' => 'ti-clock'],
        'cooking'    => ['label' => 'Memasak',  'color' => 'coral',  'icon' => 'ti-chef-hat'],
        'selesai'    => ['label' => 'Selesai',  'color' => 'teal',   'icon' => 'ti-check'],
        'completed'  => ['label' => 'Completed','color' => 'green',  'icon' => 'ti-circle-check'],
        'dibatalkan' => ['label' => 'Dibatalkan','color' => 'red',   'icon' => 'ti-x'],
    ];

    protected $fillable = [
        'user_id', 'order_number', 'status', 'order_type',
        'subtotal', 'discount_amount', 'tax_amount', 'total_amount',
        'payment_method', 'payment_status', 'voucher_code', 'notes',
        'table_number', 'estimated_time', 'completed_at',
        'loyalty_points_earned', 'cashback_earned',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:0',
        'discount_amount' => 'decimal:0',
        'tax_amount'      => 'decimal:0',
        'total_amount'    => 'decimal:0',
        'cashback_earned' => 'decimal:0',
        'completed_at'    => 'datetime',
    ];

    public function user()       { return $this->belongsTo(User::class); }
    public function items()      { return $this->hasMany(OrderItem::class); }
    public function statusLogs() { return $this->hasMany(OrderStatusLog::class)->latest(); }
    public function payment()    { return $this->hasOne(Payment::class); }
    public function review()     { return $this->hasOne(MenuReview::class); }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_LABELS[$this->status]['color'] ?? 'gray';
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_MENUNGGU, self::STATUS_COOKING, self::STATUS_SELESAI]);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'RAS-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }
}