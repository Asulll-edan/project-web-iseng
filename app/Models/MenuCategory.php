<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'image', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'category_id');
    }

    public function activeMenus()
    {
        return $this->hasMany(Menu::class, 'category_id')
            ->where('is_available', true)
            ->whereNull('deleted_at');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}