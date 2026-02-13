#!/bin/bash

echo "🎮 Creating ALL Material Controllers with Complete CRUD..."

# =========================================================
# PurchaseRequestController
# =========================================================
cat > app/Http/Controllers/Material/PurchaseRequestController.php << 'EOF'
<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with('requester', 'approver');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (auth()->user()->isTeknik()) {
            $query->where('requested_by', auth()->id());
        }

        $requests = $query->latest()->paginate(20);
        return view('purchase-requests.index', compact('requests'));
    }

    public function create()
    {
        $materials = Material::where('is_active', true)->get();
        return view('purchase-requests.create', compact('materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_date' => 'required|date',
            'letter_number' => 'required|max:50',
            'letter_date' => 'required|date',
            'purpose' => 'required',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,material_id',
            'materials.*.qty_requested' => 'required|numeric|min:0.01',
            'materials.*.notes' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $purchaseRequest = PurchaseRequest::create([
                'request_number' => $this->generateRequestNumber(),
                'request_date' => $validated['request_date'],
                'requested_by' => auth()->id(),
                'letter_number' => $validated['letter_number'],
                'letter_date' => $validated['letter_date'],
                'purpose' => $validated['purpose'],
                'status' => 'pending',
            ]);

            foreach ($validated['materials'] as $material) {
                PurchaseRequestDetail::create([
                    'request_id' => $purchaseRequest->request_id,
                    'material_id' => $material['material_id'],
                    'qty_requested' => $material['qty_requested'],
                    'notes' => $material['notes'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('purchase-requests.index')
                ->with('success', 'Pengajuan pembelian berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors([
                'error' => 'Gagal membuat pengajuan: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load('requester', 'approver', 'details.material', 'purchase');
        return view('purchase-requests.show', compact('purchaseRequest'));
    }

    public function approve($id)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($id);

        if (!$purchaseRequest->canBeApproved()) {
            return back()->withErrors(['error' => 'Pengajuan tidak dapat diapprove']);
        }

        $purchaseRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_date' => now(),
        ]);

        return back()->with('success', 'Pengajuan berhasil diapprove');
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        if (!$purchaseRequest->isPending()) {
            return back()->withErrors(['error' => 'Hanya pengajuan pending yang bisa dihapus']);
        }

        $purchaseRequest->delete();
        return redirect()->route('purchase-requests.index')
            ->with('success', 'Pengajuan berhasil dihapus');
    }

    private function generateRequestNumber()
    {
        $date = date('Ymd');
        $count = PurchaseRequest::whereDate('created_at', today())->count() + 1;
        return 'REQ-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
EOF

# =========================================================
# PurchaseController
# =========================================================
cat > app/Http/Controllers/Material/PurchaseController.php << 'EOF'
<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('purchaseRequest', 'purchaser', 'goodsReceipt')
            ->latest()
            ->paginate(20);

        return view('purchases.index', compact('purchases'));
    }

    public function create(Request $request)
    {
        $requestId = $request->query('request_id');

        $purchaseRequest = $requestId
            ? PurchaseRequest::with('details.material')
                ->where('status', 'approved')
                ->findOrFail($requestId)
            : null;

        $approvedRequests = PurchaseRequest::where('status', 'approved')
            ->whereDoesntHave('purchase')
            ->with('details.material')
            ->get();

        return view('purchases.create', compact('purchaseRequest', 'approvedRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:purchase_requests,request_id',
            'purchase_date' => 'required|date',
            'supplier_name' => 'required|max:100',
            'supplier_contact' => 'nullable|max:100',
            'notes' => 'nullable',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,material_id',
            'materials.*.qty_ordered' => 'required|numeric|min:0.01',
            'materials.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;

            $purchase = Purchase::create([
                'purchase_number' => $this->generatePurchaseNumber(),
                'request_id' => $validated['request_id'],
                'purchase_date' => $validated['purchase_date'],
                'purchased_by' => auth()->id(),
                'supplier_name' => $validated['supplier_name'],
                'supplier_contact' => $validated['supplier_contact'],
                'notes' => $validated['notes'],
            ]);

            foreach ($validated['materials'] as $material) {
                $subtotal = $material['qty_ordered'] * $material['unit_price'];
                $totalAmount += $subtotal;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->purchase_id,
                    'material_id' => $material['material_id'],
                    'qty_ordered' => $material['qty_ordered'],
                    'unit_price' => $material['unit_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $purchase->update(['total_amount' => $totalAmount]);
            PurchaseRequest::find($validated['request_id'])->update(['status' => 'purchased']);

            DB::commit();
            return redirect()->route('purchases.index')
                ->with('success', 'Pembelian berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors([
                'error' => 'Gagal membuat pembelian: ' . $e->getMessage()
            ])->withInput();
        }
    }

    private function generatePurchaseNumber()
    {
        $date = date('Ymd');
        $count = Purchase::whereDate('created_at', today())->count() + 1;
        return 'PO-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
EOF

# =========================================================
# GoodsReceiptController
# =========================================================
cat > app/Http/Controllers/Material/GoodsReceiptController.php << 'EOF'
<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptDetail;
use App\Models\Purchase;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $purchase = $purchaseId
            ? Purchase::with('details.material')
                ->whereDoesntHave('goodsReceipt')
                ->findOrFail($purchaseId)
            : null;

        $pendingPurchases = Purchase::whereDoesntHave('goodsReceipt')
            ->with('details.material')
            ->get();

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
            $hasDifference = collect($validated['materials'])
                ->some(fn($m) => $m['qty_received'] != $m['qty_ordered']);

            $goodsReceipt = GoodsReceipt::create([
                'receipt_number' => $this->generateReceiptNumber(),
                'purchase_id' => $validated['purchase_id'],
                'receipt_date' => $validated['receipt_date'],
                'received_by' => auth()->id(),
                'status' => $hasDifference ? 'corrected' : 'received',
                'is_corrected' => $hasDifference,
                'notes' => $validated['notes'],
            ]);

            foreach ($validated['materials'] as $material) {
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

            $this->stockService->processGoodsReceipt(
                $goodsReceipt->receipt_id,
                $validated['warehouse_id'],
                $validated['materials']
            );

            DB::commit();
            return redirect()->route('goods-receipts.index')
                ->with('success', 'Penerimaan barang berhasil dicatat');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors([
                'error' => 'Gagal mencatat penerimaan: ' . $e->getMessage()
            ])->withInput();
        }
    }

    private function generateReceiptNumber()
    {
        $date = date('Ymd');
        $count = GoodsReceipt::whereDate('created_at', today())->count() + 1;
        return 'GR-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
EOF

echo "✅ Material Controllers created successfully!"
