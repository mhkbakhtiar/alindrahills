<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $primaryKey = 'material_id';

    protected $fillable = [
        'material_code',
        'material_name',
        'category',
        'unit',
        'min_stock',
        'description',
        'costing_method',
        'is_active',
    ];

    protected $casts = [
        'min_stock' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class, 'material_id');
    }

    public function batches()
    {
        return $this->hasMany(StockBatch::class, 'material_id');
    }
}
