<?php

namespace App\Services;

use App\Models\StockBatch;

class BatchService
{
    /**
     * Get active batches by material
     */
    public function getActiveBatches($warehouseId, $materialId)
    {
        return StockBatch::where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->where('status', 'active')
            ->where('qty_remaining', '>', 0)
            ->orderBy('purchase_date', 'asc')
            ->get();
    }

    /**
     * Calculate total stock value
     */
    public function calculateStockValue($warehouseId, $materialId = null)
    {
        $query = StockBatch::where('warehouse_id', $warehouseId)
            ->where('status', 'active');

        if ($materialId) {
            $query->where('material_id', $materialId);
        }

        return $query->sum(DB::raw('qty_remaining * unit_price'));
    }

    /**
     * Get batch aging report
     */
    public function getBatchAgingReport($warehouseId)
    {
        return StockBatch::where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->selectRaw('*, DATEDIFF(NOW(), purchase_date) as age_days')
            ->orderBy('purchase_date', 'asc')
            ->get();
    }
}
