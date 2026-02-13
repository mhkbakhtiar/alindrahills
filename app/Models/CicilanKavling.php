<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CicilanKavling extends Model
{
    protected $table = 'cicilan_kavling';

    protected $fillable = [
        'kode_kavling',
        'id_user',
        'nomor_cicilan',
        'tanggal_jatuh_tempo',
        'jumlah',
        'status',
        'tanggal_bayar',
        'id_jurnal_pembayaran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
        'jumlah' => 'decimal:2',
    ];

    // Relationships
    public function kavling()
    {
        return $this->belongsTo(ProjectLocation::class, 'kode_kavling', 'kavling');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'user_id');
    }

    public function jurnalPembayaran()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal_pembayaran');
    }

    // Scopes
    public function scopeBelumBayar($query)
    {
        return $query->where('status', 'belum_bayar');
    }

    public function scopeSudahBayar($query)
    {
        return $query->where('status', 'sudah_bayar');
    }

    public function scopeTelat($query)
    {
        return $query->where('status', 'telat');
    }

    public function scopeJatuhTempo($query)
    {
        return $query->where('tanggal_jatuh_tempo', '<=', now())
            ->where('status', 'belum_bayar');
    }

    // Helpers
    public function isTelat()
    {
        return $this->tanggal_jatuh_tempo < now() && $this->status === 'belum_bayar';
    }
}