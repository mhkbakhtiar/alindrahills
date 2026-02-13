<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    use HasFactory;

    protected $primaryKey = 'mutation_id';

    protected $fillable = [
        'warehouse_id',
        'material_id',
        'mutation_type',
        'reference_type',
        'reference_id',
        'qty',
        'unit_price',
        'total_value',
        'stock_before',
        'stock_after',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_value' => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
    ];

    // Relationships
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}