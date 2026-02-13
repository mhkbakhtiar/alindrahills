<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $primaryKey = 'worker_id';

    protected $fillable = [
        'worker_code',
        'full_name',
        'phone',
        'address',
        'worker_type',
        'daily_rate',
        'is_active',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function payrollDetails()
    {
        return $this->hasMany(PayrollRequestDetail::class, 'worker_id', 'worker_id');
    }
}
