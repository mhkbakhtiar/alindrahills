<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectLocation extends Model
{
    use HasFactory;

    protected $primaryKey = 'location_id';

    protected $fillable = [
        'kavling',
        'blok',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function activities()
    {
        return $this->hasMany(Activity::class, 'location_id');
    }

    // Relasi ke pembeli (many-to-many)
    public function pembeli()
    {
        return $this->belongsToMany(Pembeli::class, 'kavling_pembeli', 'location_id', 'user_id', 'location_id', 'user_id')
            ->withPivot(['tanggal_booking', 'tanggal_akad', 'harga_jual', 'status', 'keterangan'])
            ->withTimestamps();
    }

    // Relasi ke pemilik aktif (single owner)
    public function owner()
    {
        return $this->belongsToMany(Pembeli::class, 'kavling_pembeli', 'location_id', 'user_id', 'location_id', 'user_id')
            ->wherePivot('status', '!=', 'batal')
            ->withPivot(['tanggal_booking', 'tanggal_akad', 'harga_jual', 'status', 'keterangan'])
            ->withTimestamps()
            ->latest('kavling_pembeli.created_at')
            ->limit(1);
    }

    public function itemJurnal()
    {
        return $this->hasMany(ItemJurnal::class, 'kode_kavling', 'kavling');
    }

    public function cicilan()
    {
        return $this->hasMany(CicilanKavling::class, 'kode_kavling', 'kavling');
    }

    // Helper
    public function isAvailable()
    {
        return !$this->pembeli()->wherePivot('status', '!=', 'batal')->exists();
    }
}