<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuReview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'menu_id', 'user_id', 'order_id', 'rating',
        'comment', 'images', 'is_verified',
    ];

    protected $casts = [
        'images'      => 'array',
        'is_verified' => 'boolean',
        'rating'      => 'integer',
    ];

    public function menu()    { return $this->belongsTo(Menu::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function order()   { return $this->belongsTo(Order::class); }
}