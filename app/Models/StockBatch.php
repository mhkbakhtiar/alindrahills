<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    use HasFactory;

    protected $primaryKey = 'batch_id';

    protected $fillable = [
        'warehouse_id',
        'material_id',
        'receipt_id',
        'batch_number',
        'purchase_date',
        'unit_price',
        'qty_in',
        'qty_remaining',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'unit_price' => 'decimal:2',
        'qty_in' => 'decimal:2',
        'qty_remaining' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
    
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
    
    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class, 'receipt_id');
    }
}
