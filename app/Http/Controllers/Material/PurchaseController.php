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
use App\Models\MasterPrefixNomor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('purchaseRequest', 'purchaser', 'goodsReceipt', 'jurnal')->latest();

        if ($request->search) {
            $query->where('purchase_number', 'like', '%' . $request->search . '%')
                  ->orWhere('supplier_name', 'like', '%' . $request->search . '%');
        }

        if ($request->payment_type) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        $purchases = $query->paginate(20)->withQueryString();

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

        // Semua perkiraan aktif detail untuk dropdown
        $perkiraanList = Perkiraan::active()->details()->orderBy('kode_perkiraan')->get();

        // Default suggestions
        $perkiraanInventory = $perkiraanList->first();
        $perkiraanHutang    = $perkiraanList->first();
        $perkiraanKas       = $perkiraanList->first();

        return view('purchases.create', compact(
            'purchaseRequest',
            'approvedRequests',
            'perkiraanList',
            'perkiraanInventory',
            'perkiraanHutang',
            'perkiraanKas',
        ));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'request_id'                     => 'required|exists:purchase_requests,request_id',
            'purchase_date'                  => 'required|date',
            'supplier_name'                  => 'required|max:100',
            'supplier_contact'               => 'nullable|max:100',
            'notes'                          => 'nullable',
            'payment_type'                   => 'required|in:cash,tempo',
            'tempo_date'                     => 'required_if:payment_type,tempo|nullable|date|after:purchase_date',
            'materials'                      => 'required|array|min:1',
            'materials.*.material_id'        => 'required|exists:materials,material_id',
            'materials.*.qty_ordered'        => 'required|numeric|min:0.01',
            'materials.*.unit_price'         => 'required|numeric|min:0',
            'perkiraan_inventory'            => 'required|exists:perkiraan,kode_perkiraan',
            'perkiraan_bayar'                => 'required|exists:perkiraan,kode_perkiraan',
        ], [
            'tempo_date.required_if'         => 'Tanggal jatuh tempo wajib diisi untuk pembayaran tempo.',
            'tempo_date.after'               => 'Tanggal jatuh tempo harus setelah tanggal purchase.',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;

            // 1. Create Purchase
            $purchase = Purchase::create([
                'purchase_number'  => MasterPrefixNomor::generateFor('PO'),
                'request_id'       => $validated['request_id'],
                'purchase_date'    => $validated['purchase_date'],
                'purchased_by'     => auth()->id(),
                'supplier_name'    => $validated['supplier_name'],
                'supplier_contact' => $validated['supplier_contact'],
                'notes'            => $validated['notes'],
                'payment_type'     => $validated['payment_type'],
                'tempo_date'       => $validated['payment_type'] === 'tempo' ? $validated['tempo_date'] : null,
                'payment_status'   => $validated['payment_type'] === 'cash' ? 'lunas' : 'belum_bayar',
            ]);

            // 2. Create Purchase Details
            foreach ($validated['materials'] as $material) {
                $subtotal     = $material['qty_ordered'] * $material['unit_price'];
                $totalAmount += $subtotal;

                PurchaseDetail::create([
                    'purchase_id'  => $purchase->purchase_id,
                    'material_id'  => $material['material_id'],
                    'qty_ordered'  => $material['qty_ordered'],
                    'unit_price'   => $material['unit_price'],
                    'subtotal'     => $subtotal,
                ]);
            }

            $purchase->update(['total_amount' => $totalAmount]);

            // 3. Cek Tahun Anggaran aktif
            $tahunAnggaran = TahunAnggaran::active()->first();
            if (!$tahunAnggaran) {
                DB::rollBack();
                return back()->withInput()
                    ->with('error', 'Tidak ada Tahun Anggaran yang aktif. Silakan aktifkan terlebih dahulu!');
            }

            // 4. Buat Jurnal otomatis
            $paymentLabel = $validated['payment_type'] === 'cash'
                ? 'Cash/Tunai'
                : 'Tempo - Jatuh Tempo: ' . \Carbon\Carbon::parse($validated['tempo_date'])->format('d/m/Y');

            $jurnal = Jurnal::create([
                'nomor_bukti'       => $this->generateJurnalNumber(),
                'tanggal'           => $validated['purchase_date'],
                'keterangan'        => "Pembelian Material ({$paymentLabel}) - {$validated['supplier_name']} - PO: {$purchase->purchase_number}",
                'jenis_jurnal'      => 'umum',
                'departemen'        => 'Procurement',
                'id_tahun_anggaran' => $tahunAnggaran->id,
                'created_by'        => auth()->id(),
                'status'            => 'draft',
            ]);

            // 5. Item Jurnal — Debet: Persediaan/Inventory (selalu sama)
            ItemJurnal::create([
                'id_jurnal'      => $jurnal->id,
                'kode_perkiraan' => $validated['perkiraan_inventory'],
                'keterangan'     => "Pembelian Material dari {$validated['supplier_name']}",
                'debet'          => $totalAmount,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            // 6. Item Jurnal — Kredit: Kas/Bank (cash) ATAU Hutang Usaha (tempo)
            $keteranganKredit = $validated['payment_type'] === 'cash'
                ? "Pembayaran tunai ke {$validated['supplier_name']}"
                : "Hutang ke {$validated['supplier_name']} - jatuh tempo: " . \Carbon\Carbon::parse($validated['tempo_date'])->format('d/m/Y');

            ItemJurnal::create([
                'id_jurnal'      => $jurnal->id,
                'kode_perkiraan' => $validated['perkiraan_bayar'],
                'keterangan'     => $keteranganKredit,
                'debet'          => 0,
                'kredit'         => $totalAmount,
                'urutan'         => 2,
            ]);

            // 7. Link jurnal ke purchase
            $purchase->update(['jurnal_id' => $jurnal->id]);

            // 8. Update status purchase request
            PurchaseRequest::find($validated['request_id'])->update(['status' => 'purchased']);

            DB::commit();
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase Order berhasil dibuat. Silakan review dan posting jurnal.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal membuat pembelian: ' . $e->getMessage());
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(
            'purchaseRequest',
            'purchaser',
            'details.material',
            'goodsReceipt',
            'jurnal.items.perkiraan'
        );

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchase->goodsReceipt) {
            return redirect()->route('purchases.show', $purchase)
                ->with('error', 'Pembelian tidak dapat diedit karena barang sudah diterima.');
        }

        $purchase->load('details.material', 'purchaseRequest');
        $perkiraanList = Perkiraan::active()->details()->orderBy('kode_perkiraan')->get();

        return view('purchases.edit', compact('purchase', 'perkiraanList'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchase->goodsReceipt) {
            return redirect()->route('purchases.show', $purchase)
                ->with('error', 'Pembelian tidak dapat diedit karena barang sudah diterima.');
        }

        $validated = $request->validate([
            'purchase_date'           => 'required|date',
            'supplier_name'           => 'required|max:100',
            'supplier_contact'        => 'nullable|max:100',
            'notes'                   => 'nullable',
            'payment_type'            => 'required|in:cash,tempo',
            'tempo_date'              => 'required_if:payment_type,tempo|nullable|date',
            'materials'               => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,material_id',
            'materials.*.qty_ordered' => 'required|numeric|min:0.01',
            'materials.*.unit_price'  => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;

            $purchase->update([
                'purchase_date'    => $validated['purchase_date'],
                'supplier_name'    => $validated['supplier_name'],
                'supplier_contact' => $validated['supplier_contact'],
                'notes'            => $validated['notes'],
                'payment_type'     => $validated['payment_type'],
                'tempo_date'       => $validated['payment_type'] === 'tempo' ? $validated['tempo_date'] : null,
                'payment_status'   => $validated['payment_type'] === 'cash' ? 'lunas' : 'belum_bayar',
            ]);

            // Update detail material
            $purchase->details()->delete();
            foreach ($validated['materials'] as $material) {
                $subtotal     = $material['qty_ordered'] * $material['unit_price'];
                $totalAmount += $subtotal;

                PurchaseDetail::create([
                    'purchase_id'  => $purchase->purchase_id,
                    'material_id'  => $material['material_id'],
                    'qty_ordered'  => $material['qty_ordered'],
                    'unit_price'   => $material['unit_price'],
                    'subtotal'     => $subtotal,
                ]);
            }

            $purchase->update(['total_amount' => $totalAmount]);

            // Update jurnal jika ada dan masih draft
            if ($purchase->jurnal && $purchase->jurnal->status === 'draft') {
                $paymentLabel = $validated['payment_type'] === 'cash'
                    ? 'Cash/Tunai'
                    : 'Tempo - Jatuh Tempo: ' . \Carbon\Carbon::parse($validated['tempo_date'])->format('d/m/Y');

                $purchase->jurnal->update([
                    'tanggal'    => $validated['purchase_date'],
                    'keterangan' => "Pembelian Material ({$paymentLabel}) - {$validated['supplier_name']} - PO: {$purchase->purchase_number}",
                ]);

                // Update item jurnal amounts
                foreach ($purchase->jurnal->items as $item) {
                    if ($item->debet > 0) {
                        $item->update(['debet' => $totalAmount]);
                    } else {
                        $item->update(['kredit' => $totalAmount]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase Order berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    public function postJurnal(Purchase $purchase)
    {
        if (!$purchase->jurnal) {
            return back()->with('error', 'Jurnal tidak ditemukan.');
        }

        if ($purchase->jurnal->status !== 'draft') {
            return back()->with('error', 'Jurnal sudah di-posting.');
        }

        DB::beginTransaction();
        try {
            $purchase->jurnal->update(['status' => 'posted']);

            foreach ($purchase->jurnal->items as $item) {
                $perkiraan = $item->perkiraan;
                if ($perkiraan) {
                    if ($item->debet > 0) {
                        $perkiraan->increment('saldo_debet', $item->debet);
                    }
                    if ($item->kredit > 0) {
                        $perkiraan->increment('saldo_kredit', $item->kredit);
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Jurnal berhasil di-posting.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal posting jurnal: ' . $e->getMessage());
        }
    }

    private function generatePurchaseNumber(): string
    {
        $date  = date('Ymd');
        $count = Purchase::whereDate('created_at', today())->count() + 1;
        return 'PO-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private function generateJurnalNumber(): string
    {
        $date  = date('Ymd');
        $count = Jurnal::whereDate('created_at', today())->count() + 1;
        return 'JU-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}