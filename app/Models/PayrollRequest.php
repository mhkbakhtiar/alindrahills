<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'payroll_request_id';

    protected $fillable = [
        'request_number',
        'request_date',
        'period_start',
        'period_end',
        'activity_id',
        'requested_by',
        'approved_by',
        'approved_date',
        'letter_number',
        'letter_date',
        'total_amount',
        'notes',
        'status',
    ];

    protected $casts = [
        'request_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'approved_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }

    public function details()
    {
        return $this->hasMany(PayrollRequestDetail::class, 'payroll_request_id', 'payroll_request_id');
    }
}