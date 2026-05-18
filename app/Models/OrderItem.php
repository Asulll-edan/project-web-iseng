<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'menu_id', 'menu_name', 'price', 'quantity', 'subtotal', 'note',
    ];

    protected $casts = [
        'price'    => 'decimal:0',
        'subtotal' => 'decimal:0',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function menu()  { return $this->belongsTo(Menu::class); }
}