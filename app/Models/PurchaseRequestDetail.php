<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_id';
    public $timestamps = false;

    protected $fillable = [
        'request_id',
        'material_id',
        'qty_requested',
        'notes',
    ];

    protected $casts = [
        'qty_requested' => 'decimal:2',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'request_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
