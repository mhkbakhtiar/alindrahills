<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $primaryKey = 'warehouse_id';
    public $timestamps = false;

    protected $fillable = [
        'warehouse_code',
        'warehouse_name',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stocks()
    {
        return $this->hasMany(WarehouseStock::class, 'warehouse_id');
    }
}
