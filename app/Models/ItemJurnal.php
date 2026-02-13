<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemJurnal extends Model
{
    protected $table = 'item_jurnal';

    protected $fillable = [
        'id_jurnal',
        'kode_perkiraan',
        'kode_kavling',
        'id_user',
        'keterangan',
        'debet',
        'kredit',
        'urutan',
    ];

    protected $casts = [
        'debet' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];

    // Relationships
    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal');
    }

    public function perkiraan()
    {
        return $this->belongsTo(Perkiraan::class, 'kode_perkiraan', 'kode_perkiraan');
    }

    public function kavling()
    {
        return $this->belongsTo(ProjectLocation::class, 'kode_kavling', 'kavling');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_user', 'user_id');
    }

    // Scopes
    public function scopeByPerkiraan($query, $kodePerkiraan)
    {
        return $query->where('kode_perkiraan', $kodePerkiraan);
    }

    public function scopeByKavling($query, $kodeKavling)
    {
        return $query->where('kode_kavling', $kodeKavling);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('id_user', $userId);
    }

    public function scopeDebet($query)
    {
        return $query->where('debet', '>', 0);
    }

    public function scopeKredit($query)
    {
        return $query->where('kredit', '>', 0);
    }

    // Helpers
    public function getMutasiAttribute()
    {
        return $this->debet - $this->kredit;
    }
}