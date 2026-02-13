<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityWorker extends Model
{
    use HasFactory;

    protected $primaryKey = 'assignment_id';
    public $timestamps = false;

    protected $fillable = [
        'activity_id',
        'worker_id',
        'assigned_date',
        'work_description',
        'is_active',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}