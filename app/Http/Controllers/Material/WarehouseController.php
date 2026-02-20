<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\MasterPrefixNomor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $query = Warehouse::withCount('stocks');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('warehouse_code', 'like', '%' . $request->search . '%')
                  ->orWhere('warehouse_name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $warehouses = $query->orderBy('warehouse_code')->paginate(15)->withQueryString();

        $totalActive   = Warehouse::where('is_active', true)->count();
        $totalInactive = Warehouse::where('is_active', false)->count();
        $totalAll      = Warehouse::count();

        return view('warehouses.index', compact('warehouses', 'totalActive', 'totalInactive', 'totalAll'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'warehouse_name' => 'required|max:100',
            'location'       => 'nullable|max:255',
            'is_active'      => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Generate kode otomatis dari master prefix
            $warehouseCode = MasterPrefixNomor::generateFor('GDG');

            $warehouse = Warehouse::create([
                'warehouse_code' => $warehouseCode,
                'warehouse_name' => $validated['warehouse_name'],
                'location'       => $validated['location'],
                'is_active'      => $request->boolean('is_active', true),
            ]);

            DB::commit();
            return redirect()->route('warehouses.show', $warehouse)
                ->with('success', "Gudang {$warehouse->warehouse_code} berhasil ditambahkan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal menyimpan gudang: ' . $e->getMessage());
        }
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load('stocks.material');
        return view('warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        $this->authorizeAdmin();
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'warehouse_name' => 'required|max:100',
            'location'       => 'nullable|max:255',
            'is_active'      => 'boolean',
        ]);

        $warehouse->update([
            'warehouse_name' => $validated['warehouse_name'],
            'location'       => $validated['location'],
            'is_active'      => $request->boolean('is_active'),
        ]);

        return redirect()->route('warehouses.show', $warehouse)
            ->with('success', "Gudang {$warehouse->warehouse_code} berhasil diperbarui.");
    }

    public function destroy(Warehouse $warehouse)
    {
        $this->authorizeAdmin();

        if ($warehouse->stocks()->exists()) {
            return back()->with('error',
                "Gudang <strong>{$warehouse->warehouse_code}</strong> tidak dapat dihapus karena masih memiliki data stok material.");
        }

        $code = $warehouse->warehouse_code;
        $warehouse->delete();

        return redirect()->route('warehouses.index')
            ->with('success', "Gudang <strong>{$code}</strong> berhasil dihapus.");
    }

    private function authorizeAdmin(): void
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }
    }
}