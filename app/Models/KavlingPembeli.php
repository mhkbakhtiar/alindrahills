<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KavlingPembeli extends Model
{
    use SoftDeletes;

    protected $table = 'kavling_pembeli';

    protected $fillable = [
        'location_id',
        'user_id',
        'tanggal_booking',
        'tanggal_akad',
        'harga_jual',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_booking' => 'date',
        'tanggal_akad' => 'date',
        'harga_jual' => 'decimal:2',
    ];

    // Relasi
    public function kavling()
    {
        return $this->belongsTo(ProjectLocation::class, 'location_id', 'location_id');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'user_id', 'user_id');
    }

    // Scope
    public function scopeAktif($query)
    {
        return $query->where('status', '!=', 'batal');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}