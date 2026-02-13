<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_id';
    public $timestamps = false;

    protected $fillable = [
        'purchase_id',
        'material_id',
        'qty_ordered',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'qty_ordered' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
