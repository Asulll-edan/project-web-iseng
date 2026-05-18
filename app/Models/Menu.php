<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'discount_price',
        'image', 'stock', 'is_available', 'is_best_seller', 'is_featured',
        'rating', 'review_count', 'order_count', 'calories', 'preparation_time', 'tags',
    ];

    protected $casts = [
        'price'          => 'decimal:0',
        'discount_price' => 'decimal:0',
        'is_available'   => 'boolean',
        'is_best_seller' => 'boolean',
        'is_featured'    => 'boolean',
        'tags'           => 'array',
        'rating'         => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(MenuImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(MenuImage::class)->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(MenuReview::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function flashSales()
    {
        return $this->hasMany(FlashSale::class)->where('is_active', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    // ── Helpers ────────────────────────────────────────────
    public function getEffectivePriceAttribute()
    {
        $flash = $this->flashSales()->first();
        if ($flash) return $flash->flash_price;
        return $this->discount_price ?? $this->price;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) return asset('storage/' . $this->image);
        return asset('images/menu-placeholder.jpg');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'ilike', "%{$term}%")
              ->orWhere('description', 'ilike', "%{$term}%");
        });
    }
}