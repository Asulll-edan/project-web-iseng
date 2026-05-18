<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    protected $fillable = ['user_id', 'total_points', 'used_points', 'available_points'];

    public function user() { return $this->belongsTo(User::class); }
    public function logs() { return $this->hasMany(LoyaltyPointLog::class, 'user_id', 'user_id'); }
}