<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jurnal extends Model
{
    use SoftDeletes;

    protected $table = 'jurnal';

    protected $fillable = [
        'nomor_bukti',
        'tanggal',
        'keterangan',
        'jenis_jurnal',
        'departemen',
        'id_tahun_anggaran',
        'created_by',
        'updated_by',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(ItemJurnal::class, 'id_jurnal');
    }

    public function tahunAnggaran()
    {
        return $this->belongsTo(TahunAnggaran::class, 'id_tahun_anggaran');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    public function cicilanKavling()
    {
        return $this->hasMany(CicilanKavling::class, 'id_jurnal_pembayaran');
    }

    // Scopes
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('tanggal', [$start, $end]);
    }

    // Helpers
    public function getTotalDebetAttribute()
    {
        return $this->items->sum('debet');
    }

    public function getTotalKreditAttribute()
    {
        return $this->items->sum('kredit');
    }

    public function isBalanced()
    {
        return $this->total_debet == $this->total_kredit;
    }

    public function getBalanceAttribute()
    {
        return $this->total_debet - $this->total_kredit;
    }
}