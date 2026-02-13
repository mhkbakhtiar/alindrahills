<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseRequest;
use App\Models\Perkiraan;
use App\Models\Jurnal;
use App\Models\ItemJurnal;
use App\Models\TahunAnggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    
    public function index()
    {
        $purchases = Purchase::with('purchaseRequest', 'purchaser', 'goodsReceipt', 'jurnal')
            ->latest()
            ->paginate(20);

        return view('purchases.index', compact('purchases'));
    }

    public function create(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }
        
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

        // Get perkiraan untuk jurnal otomatis
        $perkiraanInventory = Perkiraan::active()
            ->details()
            ->first();

        $perkiraanHutang = Perkiraan::active()
            ->details()
            ->first();

        return view('purchases.create', compact(
            'purchaseRequest',
            'approvedRequests',
            'perkiraanInventory',
            'perkiraanHutang'
        ));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }
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
            // Perkiraan untuk jurnal otomatis
            'perkiraan_inventory' => 'required|exists:perkiraan,kode_perkiraan',
            'perkiraan_hutang' => 'required|exists:perkiraan,kode_perkiraan',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;

            // 1. Create Purchase
            $purchase = Purchase::create([
                'purchase_number' => $this->generatePurchaseNumber(),
                'request_id' => $validated['request_id'],
                'purchase_date' => $validated['purchase_date'],
                'purchased_by' => auth()->id(),
                'supplier_name' => $validated['supplier_name'],
                'supplier_contact' => $validated['supplier_contact'],
                'notes' => $validated['notes'],
            ]);
            
            // 2. Create Purchase Details
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

            // 3. Create Jurnal Otomatis
            $tahunAnggaran = TahunAnggaran::active()->first();
            
            $jurnal = Jurnal::create([
                'nomor_bukti' => $this->generateJurnalNumber(),
                'tanggal' => $validated['purchase_date'],
                'keterangan' => "Pembelian Material - {$validated['supplier_name']} - PO: {$purchase->purchase_number}",
                'jenis_jurnal' => 'umum',
                'departemen' => 'Procurement',
                'id_tahun_anggaran' => $tahunAnggaran?->id,
                'created_by' => auth()->id(),
                'status' => 'draft', // Draft dulu, bisa di-post manual
            ]);

            // 4. Create Item Jurnal - Debet: Persediaan/Inventory
            ItemJurnal::create([
                'id_jurnal' => $jurnal->id,
                'kode_perkiraan' => $validated['perkiraan_inventory'],
                'keterangan' => "Pembelian Material dari {$validated['supplier_name']}",
                'debet' => $totalAmount,
                'kredit' => 0,
                'urutan' => 1,
            ]);

            // 5. Create Item Jurnal - Kredit: Hutang Usaha
            ItemJurnal::create([
                'id_jurnal' => $jurnal->id,
                'kode_perkiraan' => $validated['perkiraan_hutang'],
                'keterangan' => "Hutang kepada {$validated['supplier_name']}",
                'debet' => 0,
                'kredit' => $totalAmount,
                'urutan' => 2,
            ]);

            // 6. Link Jurnal ke Purchase
            $purchase->update(['jurnal_id' => $jurnal->id]);

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

    private function generateJurnalNumber()
    {
        $date = date('Ymd');
        $count = Jurnal::whereDate('created_at', today())->count() + 1;
        return 'JU-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('purchaseRequest', 'purchaser', 'details.material', 'goodsReceipt', 'jurnal.items.perkiraan');
        return view('purchases.show', compact('purchase'));
    }

    public function postJurnal(Purchase $purchase)
    {
        if (!$purchase->jurnal) {
            return back()->withErrors(['error' => 'Jurnal tidak ditemukan']);
        }

        if ($purchase->jurnal->status === 'posted') {
            return back()->withErrors(['error' => 'Jurnal sudah di-posting']);
        }

        DB::beginTransaction();
        try {
            // Update status jurnal
            $purchase->jurnal->update([
                'status' => 'posted',
                'updated_by' => auth()->id(),
            ]);

            // Update saldo perkiraan
            foreach ($purchase->jurnal->items as $item) {
                $perkiraan = $item->perkiraan;
                if ($perkiraan) {
                    $perkiraan->saldo_debet += $item->debet;
                    $perkiraan->saldo_kredit += $item->kredit;
                    $perkiraan->save();
                }
            }

            DB::commit();
            return back()->with('success', 'Jurnal berhasil di-posting');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Gagal posting jurnal: ' . $e->getMessage()]);
        }
    }
}
