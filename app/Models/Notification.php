<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'title', 'message', 'type', 'icon',
        'action_url', 'is_read', 'is_broadcast',
    ];

    protected $casts = [
        'is_read'      => 'boolean',
        'is_broadcast' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function scopeUnread($query) { return $query->where('is_read', false); }
}