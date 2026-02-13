<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengeluaranBulanan extends Model
{
    protected $table = 'pengeluaran_bulanan';

    protected $fillable = [
        'bulan',
        'kategori',
        'keterangan',
        'jumlah',
        'created_by',
    ];

    protected $casts = [
        'bulan' => 'date',
        'jumlah' => 'decimal:2',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    // Scopes
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereYear('bulan', $year)
            ->whereMonth('bulan', $month);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}