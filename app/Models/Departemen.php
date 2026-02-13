<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    protected $table = 'departemen';

    protected $fillable = [
        'kode_departemen',
        'nama_departemen',
        'deskripsi',
        'kepala_departemen',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function kepala()
    {
        return $this->belongsTo(User::class, 'kepala_departemen', 'user_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}