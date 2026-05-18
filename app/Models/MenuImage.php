<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuImage extends Model
{
    protected $fillable = ['menu_id', 'image_path', 'is_primary', 'sort_order'];
    protected $casts    = ['is_primary' => 'boolean'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}