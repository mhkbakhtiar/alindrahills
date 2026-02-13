<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $primaryKey = 'purchase_id';

    protected $fillable = [
        'purchase_number',
        'request_id',
        'purchase_date',
        'purchased_by',
        'supplier_id',
        'supplier_name',
        'supplier_contact',
        'warehouse_id',
        'total_amount',
        'notes',
        'jurnal_id'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'request_id', 'request_id');
    }

    public function purchaser()
    {
        return $this->belongsTo(User::class, 'purchased_by', 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'warehouse_id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id', 'purchase_id');
    }

    // UPDATED: Support multiple goods receipts (partial receipt)
    public function goodsReceipts()
    {
        return $this->hasMany(GoodsReceipt::class, 'purchase_id', 'purchase_id');
    }

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'jurnal_id');
    }

    // Keep for backward compatibility
    public function goodsReceipt()
    {
        return $this->hasOne(GoodsReceipt::class, 'purchase_id', 'purchase_id');
    }

    /**
     * Check if purchase is fully received
     */
    public function isFullyReceived()
    {
        if ($this->details->isEmpty()) {
            return false;
        }

        foreach ($this->details as $detail) {
            $totalReceived = $this->goodsReceipts()
                ->whereHas('details', function($q) use ($detail) {
                    $q->where('material_id', $detail->material_id);
                })
                ->get()
                ->flatMap->details
                ->where('material_id', $detail->material_id)
                ->sum('qty_received');

            if ($totalReceived < $detail->qty_ordered) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get remaining qty for a material
     */
    public function getRemainingQty($materialId)
    {
        $detail = $this->details()->where('material_id', $materialId)->first();
        if (!$detail) {
            return 0;
        }

        $totalReceived = $this->goodsReceipts()
            ->whereHas('details', function($q) use ($materialId) {
                $q->where('material_id', $materialId);
            })
            ->get()
            ->flatMap->details
            ->where('material_id', $materialId)
            ->sum('qty_received');

        return $detail->qty_ordered - $totalReceived;
    }
}