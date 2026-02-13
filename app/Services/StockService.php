<?php

namespace App\Services;

use App\Models\StockBatch;
use App\Models\WarehouseStock;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Process material receipt and create batches (FIFO)
     */
    public function processGoodsReceipt($receiptId, $warehouseId, $materials)
    {
        DB::beginTransaction();
        try {
            foreach ($materials as $material) {
                // Create batch
                $batch = StockBatch::create([
                    'warehouse_id' => $warehouseId,
                    'material_id' => $material['material_id'],
                    'receipt_id' => $receiptId,
                    'batch_number' => $this->generateBatchNumber(),
                    'purchase_date' => now(),
                    'unit_price' => $material['unit_price'],
                    'qty_in' => $material['qty_received'],
                    'qty_remaining' => $material['qty_received'],
                    'status' => 'active',
                ]);

                // Update warehouse stock
                $this->updateWarehouseStock(
                    $warehouseId, 
                    $material['material_id'], 
                    $material['qty_received'],
                    'in'
                );

                // Record mutation
                $this->recordMutation(
                    $warehouseId,
                    $material['material_id'],
                    'in',
                    'goods_receipt',
                    $receiptId,
                    $material['qty_received'],
                    $material['unit_price']
                );
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Process material usage with FIFO method
     */
    public function processMaterialUsage($usageId, $warehouseId, $materialId, $qtyNeeded)
    {
        DB::beginTransaction();
        try {
            $batches = StockBatch::where('warehouse_id', $warehouseId)
                ->where('material_id', $materialId)
                ->where('status', 'active')
                ->where('qty_remaining', '>', 0)
                ->orderBy('purchase_date', 'asc')
                ->get();

            $remainingQty = $qtyNeeded;
            $totalValue = 0;

            foreach ($batches as $batch) {
                if ($remainingQty <= 0) break;

                $qtyToTake = min($batch->qty_remaining, $remainingQty);

                // Update batch
                $batch->qty_remaining -= $qtyToTake;
                if ($batch->qty_remaining <= 0) {
                    $batch->status = 'depleted';
                }
                $batch->save();

                $totalValue += ($qtyToTake * $batch->unit_price);
                $remainingQty -= $qtyToTake;
            }

            if ($remainingQty > 0) {
                throw new \Exception('Insufficient stock for material ID: ' . $materialId);
            }

            // Update warehouse stock
            $this->updateWarehouseStock($warehouseId, $materialId, $qtyNeeded, 'out');

            // Record mutation
            $this->recordMutation(
                $warehouseId,
                $materialId,
                'out',
                'material_usage',
                $usageId,
                $qtyNeeded,
                $totalValue / $qtyNeeded
            );

            DB::commit();
            return $totalValue;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update warehouse stock
     */
    private function updateWarehouseStock($warehouseId, $materialId, $qty, $type)
    {
        $stock = WarehouseStock::firstOrCreate(
            ['warehouse_id' => $warehouseId, 'material_id' => $materialId],
            ['current_stock' => 0, 'average_price' => 0]
        );

        if ($type === 'in') {
            $stock->current_stock += $qty;
        } else {
            $stock->current_stock -= $qty;
        }

        $stock->save();
    }

    /**
     * Record stock mutation
     */
    private function recordMutation($warehouseId, $materialId, $type, $refType, $refId, $qty, $unitPrice)
    {
        $stock = WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->first();

        $stockBefore = $stock ? $stock->current_stock : 0;
        $stockAfter = $type === 'in' ? $stockBefore + $qty : $stockBefore - $qty;

        StockMutation::create([
            'warehouse_id' => $warehouseId,
            'material_id' => $materialId,
            'mutation_type' => $type,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'total_value' => $qty * $unitPrice,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Generate unique batch number
     */
    private function generateBatchNumber()
    {
        return 'BATCH-' . date('Ymd') . '-' . str_pad(StockBatch::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get stock value by warehouse
     */
    public function getStockValueByWarehouse($warehouseId)
    {
        return StockBatch::where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->selectRaw('material_id, SUM(qty_remaining * unit_price) as total_value')
            ->groupBy('material_id')
            ->get();
    }
}
