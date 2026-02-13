<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAnggaran extends Model
{
    protected $table = 'tahun_anggaran';

    protected $fillable = [
        'tahun',
        'periode_awal',
        'periode_akhir',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'periode_awal' => 'date',
        'periode_akhir' => 'date',
    ];

    // Relationships
    public function jurnal()
    {
        return $this->hasMany(Jurnal::class, 'id_tahun_anggaran');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'tutup_buku');
    }

    // Helpers
    public function isClosed()
    {
        return $this->status === 'tutup_buku';
    }
}