<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laporan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nomor_laporan','user_id','judul','tipe','periode_start','periode_end',
        'file_path','file_type','catatan','status','approved_by','approved_at','admin_note',
    ];

    protected $casts = ['approved_at' => 'datetime'];

    public function user()     { return $this->belongsTo(User::class); }
    public function approver() { return $this->belongsTo(User::class,'approved_by'); }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/'.$this->file_path) : null;
    }

    public static function generateNomor(): string
    {
        $prefix = 'LAP-'.date('Ym').'-';
        $last   = static::where('nomor_laporan','like',$prefix.'%')->count();
        return $prefix.str_pad($last+1, 4, '0', STR_PAD_LEFT);
    }
}