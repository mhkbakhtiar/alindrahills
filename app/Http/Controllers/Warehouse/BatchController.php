<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\StockBatch;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockBatch::with('warehouse', 'material', 'goodsReceipt');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('material_id')) {
            $query->where('material_id', $request->material_id);
        }

        $batches = $query->orderBy('purchase_date', 'desc')->paginate(20);
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('batches.index', compact('batches', 'warehouses'));
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
    public function show(StockBatch $batch)
    {
        $batch->load('warehouse', 'material', 'goodsReceipt');
        return view('batches.show', compact('batch'));
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
