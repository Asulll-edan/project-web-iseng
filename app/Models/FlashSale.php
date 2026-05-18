<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    protected $fillable = [
        'title', 'menu_id', 'flash_price', 'quota', 'sold_count', 'is_active', 'start_at', 'end_at',
    ];

    protected $casts = [
        'flash_price' => 'decimal:0',
        'is_active'   => 'boolean',
        'start_at'    => 'datetime',
        'end_at'      => 'datetime',
    ];

    public function menu() { return $this->belongsTo(Menu::class); }

    public function isActive(): bool
    {
        return $this->is_active
            && $this->start_at->isPast()
            && $this->end_at->isFuture()
            && $this->sold_count < $this->quota;
    }

    public function getRemainingQuotaAttribute(): int
    {
        return max(0, $this->quota - $this->sold_count);
    }
}