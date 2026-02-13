<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\WarehouseStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WarehouseStock::with('warehouse', 'material');

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('search')) {
            $query->whereHas('material', function($q) use ($request) {
                $q->where('material_name', 'like', '%' . $request->search . '%')
                  ->orWhere('material_code', 'like', '%' . $request->search . '%');
            });
        }

        $stocks = $query->paginate(20);
        $warehouses = Warehouse::where('is_active', true)->get();

        $summary = [
            'total_items' => WarehouseStock::count(),
            'total_value' => WarehouseStock::sum(\DB::raw('current_stock * average_price')),
            'low_stock_count' => WarehouseStock::whereHas('material', function($q) {
                $q->whereRaw('warehouse_stocks.current_stock <= materials.min_stock');
            })->count(),
            'out_of_stock_count' => WarehouseStock::where('current_stock', 0)->count(),
        ];

        return view('stocks.index', compact('stocks', 'warehouses', 'summary'));
    }

    public function batches(Request $request)
    {
        $materialId = $request->material;
        $warehouseId = $request->warehouse;

        $batches = \App\Models\StockBatch::where('material_id', $materialId)
            ->where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->orderBy('purchase_date', 'asc')
            ->get();

        return view('stocks.batches', compact('batches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
