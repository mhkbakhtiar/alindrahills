<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'request_number',
        'request_date',
        'requested_by',
        'letter_number',
        'letter_date',
        'purpose',
        'status',
        'approved_by',
        'approved_date',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'date',
        'letter_date' => 'date',
        'approved_date' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseRequestDetail::class, 'request_id');
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class, 'request_id');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function canBeApproved()
    {
        return $this->isPending() && auth()->user()->isAdmin() || auth()->user()->isSuperadmin();
    }
}
