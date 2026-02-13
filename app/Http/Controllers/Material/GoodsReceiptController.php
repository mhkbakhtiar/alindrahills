<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptDetail;
use App\Models\Purchase;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class GoodsReceiptController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        $receipts = GoodsReceipt::with('purchase', 'receiver')
            ->latest()
            ->paginate(20);

        return view('goods-receipts.index', compact('receipts'));
    }

    public function create(Request $request)
    {
        $purchaseId = $request->query('purchase_id');

        // Get pending purchases (yang belum fully received)
        $pendingPurchases = Purchase::with('details.material', 'goodsReceipts.details')
            ->get()
            ->filter(function($purchase) {
                // Filter hanya yang belum fully received
                return !$purchase->isFullyReceived();
            });

        $purchase = $purchaseId
            ? Purchase::with('details.material', 'goodsReceipts.details')
                ->findOrFail($purchaseId)
            : null;

        return view('goods-receipts.create', compact('purchase', 'pendingPurchases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => 'required|exists:purchases,purchase_id',
            'receipt_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,warehouse_id',
            'notes' => 'nullable',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,material_id',
            'materials.*.qty_ordered' => 'required|numeric|min:0',
            'materials.*.qty_received' => 'required|numeric|min:0',
            'materials.*.unit_price' => 'required|numeric|min:0',
            'materials.*.condition_status' => 'required|in:good,damaged,incomplete',
            'materials.*.notes' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $purchase = Purchase::with('details', 'goodsReceipts.details')->findOrFail($validated['purchase_id']);

            // Validate qty received tidak melebihi sisa
            foreach ($validated['materials'] as $material) {
                $remaining = $purchase->getRemainingQty($material['material_id']);
                if ($material['qty_received'] > $remaining) {
                    throw new \Exception("Qty received untuk material ID {$material['material_id']} melebihi sisa yang belum diterima");
                }
            }

            // Check if there's any difference (partial or damaged)
            $hasDifference = false;
            foreach ($validated['materials'] as $material) {
                if ($material['qty_received'] != $material['qty_ordered'] || $material['condition_status'] !== 'good') {
                    $hasDifference = true;
                    break;
                }
            }

            $goodsReceipt = GoodsReceipt::create([
                'receipt_number' => $this->generateReceiptNumber(),
                'purchase_id' => $validated['purchase_id'],
                'receipt_date' => $validated['receipt_date'],
                'received_by' => auth()->id(),
                'warehouse_id' => $validated['warehouse_id'],
                'status' => $hasDifference ? 'corrected' : 'received',
                'is_corrected' => $hasDifference,
                'notes' => $validated['notes'],
            ]);

            // Save details (only for materials with qty_received > 0)
            foreach ($validated['materials'] as $material) {
                if ($material['qty_received'] > 0) {
                    GoodsReceiptDetail::create([
                        'receipt_id' => $goodsReceipt->receipt_id,
                        'material_id' => $material['material_id'],
                        'qty_ordered' => $material['qty_ordered'],
                        'qty_received' => $material['qty_received'],
                        'unit_price' => $material['unit_price'],
                        'condition_status' => $material['condition_status'],
                        'notes' => $material['notes'],
                    ]);
                }
            }

            // Process stock (only for good condition items)
            $goodMaterials = array_filter($validated['materials'], function($m) {
                return $m['condition_status'] === 'good' && $m['qty_received'] > 0;
            });

            if (!empty($goodMaterials)) {
                $this->stockService->processGoodsReceipt(
                    $goodsReceipt->receipt_id,
                    $validated['warehouse_id'],
                    $goodMaterials
                );
            }

            DB::commit();

            // Check if fully received
            $purchase->refresh();
            $fullyReceived = $purchase->isFullyReceived();
            
            $message = $fullyReceived 
                ? 'Penerimaan barang berhasil dicatat. Semua barang sudah lengkap diterima!' 
                : 'Penerimaan barang berhasil dicatat. Masih ada barang yang belum diterima.';

            return redirect()->route('goods-receipts.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors([
                'error' => 'Gagal mencatat penerimaan: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        $receipt = $goodsReceipt->load([
            'purchase.details.material',
            'purchase.goodsReceipts' => function($query) {
                $query->orderBy('receipt_date', 'asc'); // ← Sort by date
            },
            'purchase.goodsReceipts.details.material', // ← Load details per receipt
            'receiver',
            'warehouse',
            'details.material'
        ]);
        
        return view('goods-receipts.show', compact('receipt'));
    }

    private function generateReceiptNumber()
    {
        $date = date('Ymd');
        $count = GoodsReceipt::whereDate('created_at', today())->count() + 1;
        return 'GR-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Print goods receipt invoice
     */
    public function printInvoice(GoodsReceipt $goodsReceipt)
    {
        $receipt = $goodsReceipt->load([
            'purchase.details.material',
            'purchase.goodsReceipts' => function($query) {
                $query->orderBy('receipt_date', 'asc');
            },
            'purchase.goodsReceipts.details.material',
            'receiver',
            'warehouse',
            'details.material'
        ]);
        
        $pdf = Pdf::loadView('goods-receipts.invoice', compact('receipt'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);
        
        return $pdf->stream($receipt->receipt_number . '.pdf');
    }
}