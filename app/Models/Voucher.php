<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'description', 'type', 'value', 'min_order',
        'max_discount', 'max_usage', 'used_count', 'is_active', 'start_at', 'end_at',
    ];

    protected $casts = [
        'value'        => 'decimal:0',
        'min_order'    => 'decimal:0',
        'max_discount' => 'decimal:0',
        'is_active'    => 'boolean',
        'start_at'     => 'datetime',
        'end_at'       => 'datetime',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->start_at && $this->start_at->isFuture()) return false;
        if ($this->end_at && $this->end_at->isPast()) return false;
        if ($this->max_usage && $this->used_count >= $this->max_usage) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->min_order) return 0;

        if ($this->type === 'percent') {
            $discount = $subtotal * ($this->value / 100);
            if ($this->max_discount) $discount = min($discount, $this->max_discount);
            return $discount;
        }
        return min($this->value, $subtotal);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('start_at')->orWhere('start_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('end_at')->orWhere('end_at', '>=', now()))
            ->where(fn($q) => $q->whereNull('max_usage')->orWhereRaw('used_count < max_usage'));
    }
}