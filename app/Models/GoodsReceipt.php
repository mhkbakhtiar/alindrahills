<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $primaryKey = 'receipt_id';

    protected $fillable = [
        'receipt_number',
        'purchase_id',
        'receipt_date',
        'received_by',
        'warehouse_id',
        'status',
        'is_corrected',
        'notes',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'is_corrected' => 'boolean',
    ];

    // Relationships
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'purchase_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by', 'user_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'warehouse_id');
    }

    public function details()
    {
        return $this->hasMany(GoodsReceiptDetail::class, 'receipt_id', 'receipt_id');
    }

    public function batches()
    {
        return $this->hasMany(StockBatch::class, 'receipt_id', 'receipt_id');
    }
}