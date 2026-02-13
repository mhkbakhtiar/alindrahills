<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $primaryKey = 'activity_id';

    protected $fillable = [
        'activity_code',
        'activity_name',
        'location_id',
        'activity_type',
        'start_date',
        'end_date',
        'status',
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function location()
    {
        return $this->belongsTo(ProjectLocation::class, 'location_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function activityWorkers()
    {
        return $this->hasMany(ActivityWorker::class, 'activity_id');
    }

    public function materialUsages()
    {
        return $this->hasMany(MaterialUsage::class, 'activity_id');
    }

    public function contractors()
    {
        return $this->belongsToMany(
            Contractor::class,
            'activity_contractors',
            'activity_id',
            'contractor_id'
        );
    }

    // Helper Methods
    public function getTotalMaterialCostAttribute()
    {
        return $this->materialUsages()->sum('total_value');
    }

    public function getTotalWorkersAttribute()
    {
        return $this->activityWorkers()->where('is_active', true)->count();
    }
}