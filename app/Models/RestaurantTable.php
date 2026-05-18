<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $fillable = ['table_number', 'capacity', 'status', 'location', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function reservations() { return $this->hasMany(Reservation::class, 'table_id'); }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }
}