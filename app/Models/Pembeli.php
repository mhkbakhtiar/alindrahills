<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembeli extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembeli';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'alamat',
        'no_identitas',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi ke kavling (many-to-many)
    public function kavlings()
    {
        return $this->belongsToMany(ProjectLocation::class, 'kavling_pembeli', 'user_id', 'location_id', 'user_id', 'location_id')
            ->withPivot(['tanggal_booking', 'tanggal_akad', 'harga_jual', 'status', 'keterangan'])
            ->withTimestamps();
    }

    // Relasi ke item jurnal
    public function itemJurnal()
    {
        return $this->hasMany(ItemJurnal::class, 'id_user', 'user_id');
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper
    public function getKavlingAktifAttribute()
    {
        return $this->kavlings()->wherePivot('status', '!=', 'batal')->get();
    }
}