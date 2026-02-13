<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $primaryKey = 'stock_id';

    protected $fillable = [
        'warehouse_id',
        'material_id',
        'current_stock',
        'average_price',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'average_price' => 'decimal:2',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
