<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id', 'display_name', 'birthdate', 'gender',
        'address', 'city', 'profile_completion', 'preferences',
    ];

    protected $casts = [
        'birthdate'    => 'date',
        'preferences'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}