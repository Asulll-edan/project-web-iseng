<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'table_id', 'reservation_code', 'reservation_date',
        'reservation_time', 'guest_count', 'status', 'special_request',
        'event_type', 'admin_note',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'datetime',
    ];

    public function user()  { return $this->belongsTo(User::class); }
    public function table() { return $this->belongsTo(RestaurantTable::class, 'table_id'); }

    public static function generateCode(): string
    {
        return 'RSV-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }
}