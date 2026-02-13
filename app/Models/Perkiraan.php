<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perkiraan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'perkiraan';

    protected $fillable = [
        'kode_perkiraan',
        'nama_perkiraan',
        'jenis_akun',
        'kategori',
        'departemen',
        'parent_id',
        'saldo_debet',
        'saldo_kredit',
        'anggaran',
        'keterangan',
        'is_header',
        'is_cash_bank',
        'is_active',
    ];

    protected $casts = [
        'saldo_debet' => 'decimal:2',
        'saldo_kredit' => 'decimal:2',
        'anggaran' => 'decimal:2',
        'is_header' => 'boolean',
        'is_cash_bank' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = ['saldo'];

    /**
     * Get the parent perkiraan
     */
    public function parent()
    {
        return $this->belongsTo(Perkiraan::class, 'parent_id');
    }

    /**
     * Get the child perkiraan
     */
    public function children()
    {
        return $this->hasMany(Perkiraan::class, 'parent_id');
    }

    /**
     * Get all jurnal items for this perkiraan
     */
    public function itemJurnal()
    {
        return $this->hasMany(ItemJurnal::class, 'kode_perkiraan', 'kode_perkiraan');
    }

    /**
     * Calculate saldo based on account type
     */
    public function getSaldoAttribute()
    {
        // Aset, Biaya: Debet - Kredit
        if (in_array($this->jenis_akun, ['Aset', 'Biaya'])) {
            return $this->saldo_debet - $this->saldo_kredit;
        }
        
        // Kewajiban, Modal, Pendapatan: Kredit - Debet
        return $this->saldo_kredit - $this->saldo_debet;
    }

    /**
     * Get saldo normal (positive value)
     */
    public function getSaldoNormalAttribute()
    {
        return abs($this->saldo);
    }

    /**
     * Check if account is debit normal
     */
    public function isDebitNormal()
    {
        return in_array($this->jenis_akun, ['Aset', 'Biaya']);
    }

    /**
     * Check if account is credit normal
     */
    public function isCreditNormal()
    {
        return in_array($this->jenis_akun, ['Kewajiban', 'Modal', 'Pendapatan']);
    }

    /**
     * Scope for active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for header accounts
     */
    public function scopeHeaders($query)
    {
        return $query->where('is_header', true);
    }

    /**
     * Scope for detail accounts (non-header)
     */
    public function scopeDetails($query)
    {
        return $query->where('is_header', false);
    }

    /**
     * Scope for cash/bank accounts
     */
    public function scopeCashBank($query)
    {
        return $query->where('is_cash_bank', true);
    }

    /**
     * Scope by account type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('jenis_akun', $type);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('kategori', $category);
    }

    /**
     * Update saldo from jurnal items
     */
    public function updateSaldo()
    {
        $totalDebet = $this->itemJurnal()
            ->whereHas('jurnal', function($query) {
                $query->where('status', 'posted');
            })
            ->sum('debet');

        $totalKredit = $this->itemJurnal()
            ->whereHas('jurnal', function($query) {
                $query->where('status', 'posted');
            })
            ->sum('kredit');

        $this->update([
            'saldo_debet' => $totalDebet,
            'saldo_kredit' => $totalKredit,
        ]);
    }

    /**
     * Get formatted saldo
     */
    public function getFormattedSaldoAttribute()
    {
        return 'Rp ' . number_format($this->saldo, 0, ',', '.');
    }

    /**
     * Get full account name with code
     */
    public function getFullNameAttribute()
    {
        return $this->kode_perkiraan . ' - ' . $this->nama_perkiraan;
    }

    /**
     * Check if account has transactions
     */
    public function hasTransactions()
    {
        return $this->itemJurnal()->exists();
    }

    /**
     * Get account hierarchy path
     */
    public function getHierarchyPath()
    {
        $path = [$this->nama_perkiraan];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->nama_perkiraan);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Check budget utilization percentage
     */
    public function getBudgetUtilizationAttribute()
    {
        if (!$this->anggaran || $this->anggaran == 0) {
            return 0;
        }

        $utilized = $this->jenis_akun == 'Biaya' ? $this->saldo_debet : $this->saldo_kredit;
        return ($utilized / $this->anggaran) * 100;
    }

    /**
     * Get budget status
     */
    public function getBudgetStatusAttribute()
    {
        $utilization = $this->budget_utilization;

        if ($utilization == 0) return 'no-budget';
        if ($utilization < 50) return 'safe';
        if ($utilization < 80) return 'warning';
        if ($utilization < 100) return 'near-limit';
        return 'over-budget';
    }
}