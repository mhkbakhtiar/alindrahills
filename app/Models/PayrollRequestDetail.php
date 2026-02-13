<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollRequestDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'payroll_request_detail_id';

    protected $fillable = [
        'payroll_request_id',
        'worker_id',
        'days_worked',
        'daily_rate',
        'total_wage',
        'bonus',
        'deduction',
        'net_payment',
        'notes',
    ];

    protected $casts = [
        'days_worked' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'total_wage' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'net_payment' => 'decimal:2',
    ];

    // Relationships
    public function payrollRequest()
    {
        return $this->belongsTo(PayrollRequest::class, 'payroll_request_id', 'payroll_request_id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id', 'worker_id');
    }

    // Accessors & Mutators
    public function getTotalWageFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_wage, 0, ',', '.');
    }

    public function getNetPaymentFormattedAttribute()
    {
        return 'Rp ' . number_format($this->net_payment, 0, ',', '.');
    }

    public function getBonusFormattedAttribute()
    {
        return 'Rp ' . number_format($this->bonus, 0, ',', '.');
    }

    public function getDeductionFormattedAttribute()
    {
        return 'Rp ' . number_format($this->deduction, 0, ',', '.');
    }

    // Helper Methods
    public function hasBonus()
    {
        return $this->bonus > 0;
    }

    public function hasDeduction()
    {
        return $this->deduction > 0;
    }

    public function getAdjustmentAmount()
    {
        return $this->bonus - $this->deduction;
    }
}