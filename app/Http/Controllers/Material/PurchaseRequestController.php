<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Material;
use App\Models\MasterPrefixNomor;
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

        if (auth()->user()->isTeknik() || (auth()->user()->isSuperadmin())) {
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
                'letter_number' => MasterPrefixNomor::generateFor('PR'),
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
    
    /**
     * Print purchase request invoice PDF
     */
    public function printInvoice(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load('requester', 'approver', 'details.material');

        $pdf = Pdf::loadView('purchase-requests.invoice', compact('purchaseRequest'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $fileName = 'Purchase_Request_' . $purchaseRequest->request_number . '.pdf';

        return $pdf->download($fileName);
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        // Only pending requests can be edited
        if (!$purchaseRequest->isPending()) {
            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Hanya pengajuan pending yang bisa diedit');
        }

        $materials = Material::where('is_active', true)->get();
        $purchaseRequest->load('details.material');
        
        return view('purchase-requests.edit', compact('purchaseRequest', 'materials'));
    }

    /**
     * Update purchase request
     */
    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Only pending requests can be updated
        if (!$purchaseRequest->isPending()) {
            return back()->withErrors(['error' => 'Hanya pengajuan pending yang bisa diubah']);
        }

        $validated = $request->validate([
            'request_date' => 'required|date',
            'letter_date' => 'required|date',
            'purpose' => 'required',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,material_id',
            'materials.*.qty_requested' => 'required|numeric|min:0.01',
            'materials.*.notes' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            // Update main record
            $purchaseRequest->update([
                'request_date' => $validated['request_date'],
                'letter_date' => $validated['letter_date'],
                'purpose' => $validated['purpose'],
            ]);

            // Delete existing details
            $purchaseRequest->details()->delete();

            // Create new details
            foreach ($validated['materials'] as $material) {
                PurchaseRequestDetail::create([
                    'request_id' => $purchaseRequest->request_id,
                    'material_id' => $material['material_id'],
                    'qty_requested' => $material['qty_requested'],
                    'notes' => $material['notes'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Pengajuan berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors([
                'error' => 'Gagal mengupdate pengajuan: ' . $e->getMessage()
            ])->withInput();
        }
    }
}
