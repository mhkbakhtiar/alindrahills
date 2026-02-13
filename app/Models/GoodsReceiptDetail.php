<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_id';
    public $timestamps = false;

    protected $fillable = [
        'receipt_id',
        'material_id',
        'qty_ordered',
        'qty_received',
        'unit_price',
        'condition_status',
        'notes',
    ];

    protected $casts = [
        'qty_ordered' => 'decimal:2',
        'qty_received' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
