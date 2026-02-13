<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contractor extends Model
{
    use HasFactory;

    protected $primaryKey = 'contractor_id';

    protected $fillable = [
        'contractor_code',
        'contractor_name',
        'phone',
        'address',
        'pic_name',
        'status'
    ];

    public function activities()
    {
        return $this->belongsToMany(
            Activity::class,
            'activity_contractors',
            'contractor_id',
            'activity_id'
        );
    }
}
