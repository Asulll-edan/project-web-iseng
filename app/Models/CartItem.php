<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'menu_id', 'quantity', 'note', 'price'];
    protected $casts    = ['price' => 'decimal:0'];

    public function cart() { return $this->belongsTo(Cart::class); }
    public function menu() { return $this->belongsTo(Menu::class); }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}