<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialUsageBatchDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'usage_batch_id';
    public $timestamps = true;

    protected $fillable = [
        'usage_detail_id',
        'batch_id',
        'qty_used',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'qty_used' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships
    public function usageDetail()
    {
        return $this->belongsTo(MaterialUsageDetail::class, 'usage_detail_id', 'detail_id');
    }

    public function batch()
    {
        return $this->belongsTo(StockBatch::class, 'batch_id');
    }
}