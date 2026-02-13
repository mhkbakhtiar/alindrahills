<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialUsage extends Model
{
    use HasFactory;

    protected $primaryKey = 'usage_id';

    protected $fillable = [
        'usage_number',
        'activity_id',
        'warehouse_id',
        'usage_date',
        'total_value',
        'issued_by',
        'notes',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'total_value' => 'decimal:2',
    ];

    // Relationships
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by', 'user_id');
    }

    public function details()
    {
        return $this->hasMany(MaterialUsageDetail::class, 'usage_id');
    }
}