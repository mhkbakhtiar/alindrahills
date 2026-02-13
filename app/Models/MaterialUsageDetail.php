<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialUsageDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_id';
    public $timestamps = false;

    protected $fillable = [
        'usage_id',
        'material_id',
        'qty_used',
        'average_unit_price',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'qty_used' => 'decimal:2',
        'average_unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships
    public function materialUsage()
    {
        return $this->belongsTo(MaterialUsage::class, 'usage_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function batchDetails()
    {
        return $this->hasMany(MaterialUsageBatchDetail::class, 'usage_detail_id', 'detail_id');
    }
}