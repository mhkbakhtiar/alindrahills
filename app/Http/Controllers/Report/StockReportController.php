<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\WarehouseStock;
use App\Models\Warehouse;
use App\Models\Material;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        $query = WarehouseStock::with('warehouse', 'material');

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'out_of_stock':
                    $query->where('current_stock', 0);
                    break;
                case 'low_stock':
                    $query->whereHas('material', function($q) {
                        $q->whereRaw('warehouse_stocks.current_stock <= materials.min_stock')
                          ->whereRaw('warehouse_stocks.current_stock > 0');
                    });
                    break;
                case 'normal':
                    $query->whereHas('material', function($q) {
                        $q->whereRaw('warehouse_stocks.current_stock > materials.min_stock');
                    });
                    break;
            }
        }

        // Filter by material category
        if ($request->filled('category')) {
            $query->whereHas('material', function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        $stocks = $query->get();

        // Summary statistics
        $summary = [
            'total_items' => $stocks->count(),
            'total_value' => $stocks->sum(function($stock) {
                return $stock->current_stock * $stock->average_price;
            }),
            'total_quantity' => $stocks->sum('current_stock'),
            'out_of_stock' => $stocks->where('current_stock', 0)->count(),
            'low_stock' => $stocks->filter(function($stock) {
                return $stock->current_stock > 0 && 
                       $stock->current_stock <= $stock->material->min_stock;
            })->count(),
            'normal_stock' => $stocks->filter(function($stock) {
                return $stock->current_stock > $stock->material->min_stock;
            })->count(),
        ];

        // Group by warehouse
        $byWarehouse = $stocks->groupBy('warehouse_id')->map(function($items) {
            return [
                'warehouse' => $items->first()->warehouse->warehouse_name ?? 'N/A',
                'total_items' => $items->count(),
                'total_value' => $items->sum(function($stock) {
                    return $stock->current_stock * $stock->average_price;
                }),
                'total_quantity' => $items->sum('current_stock'),
            ];
        });

        // Group by category
        $byCategory = $stocks->groupBy(function($stock) {
            return $stock->material->category ?? 'Uncategorized';
        })->map(function($items, $category) {
            return [
                'category' => $category,
                'total_items' => $items->count(),
                'total_value' => $items->sum(function($stock) {
                    return $stock->current_stock * $stock->average_price;
                }),
                'total_quantity' => $items->sum('current_stock'),
            ];
        });

        // Data for filters
        $warehouses = Warehouse::where('is_active', true)->get();
        $categories = Material::distinct()->pluck('category');

        return view('reports.stock.index', compact(
            'stocks',
            'summary',
            'byWarehouse',
            'byCategory',
            'warehouses',
            'categories'
        ));
    }

    public function movements(Request $request)
    {
        $query = StockMutation::with('material', 'warehouse', 'creator');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by mutation type
        if ($request->filled('mutation_type')) {
            $query->where('mutation_type', $request->mutation_type);
        }

        // Filter by material
        if ($request->filled('material_id')) {
            $query->where('material_id', $request->material_id);
        }

        $mutations = $query->latest('created_at')->get();

        // Summary
        $summary = [
            'total_movements' => $mutations->count(),
            'total_in' => $mutations->where('mutation_type', 'in')->sum('qty'),
            'total_out' => $mutations->where('mutation_type', 'out')->sum('qty'),
            'total_in_value' => $mutations->where('mutation_type', 'in')->sum('total_value'),
            'total_out_value' => $mutations->where('mutation_type', 'out')->sum('total_value'),
        ];

        // Group by type
        $byType = $mutations->groupBy('mutation_type')->map(function($items, $type) {
            return [
                'type' => $type,
                'count' => $items->count(),
                'total_quantity' => $items->sum('qty'),
                'total_value' => $items->sum('total_value'),
            ];
        });

        // Daily mutations
        $dailyMovements = $mutations->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function($items, $date) {
            return [
                'date' => Carbon::parse($date)->format('d M Y'),
                'in' => $items->where('mutation_type', 'in')->sum('qty'),
                'out' => $items->where('mutation_type', 'out')->sum('qty'),
            ];
        })->sortKeys();

        $warehouses = Warehouse::where('is_active', true)->get();
        $materials = Material::orderBy('material_name')->get();

        // ✅ Pastikan semua variabel di-pass ke view
        return view('reports.stock.movements', compact(
            'mutations',
            'summary',
            'byType',
            'dailyMovements', // ✅ Pastikan ada
            'warehouses',
            'materials'
        ));
    }

    public function export(Request $request)
    {
        $query = WarehouseStock::with('warehouse', 'material');

        // Apply same filters
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'out_of_stock':
                    $query->where('current_stock', 0);
                    break;
                case 'low_stock':
                    $query->whereHas('material', function($q) {
                        $q->whereRaw('warehouse_stocks.current_stock <= materials.min_stock')
                          ->whereRaw('warehouse_stocks.current_stock > 0');
                    });
                    break;
                case 'normal':
                    $query->whereHas('material', function($q) {
                        $q->whereRaw('warehouse_stocks.current_stock > materials.min_stock');
                    });
                    break;
            }
        }

        $stocks = $query->get();

        $summary = [
            'total_items' => $stocks->count(),
            'total_value' => $stocks->sum(function($stock) {
                return $stock->current_stock * $stock->average_price;
            }),
            'total_quantity' => $stocks->sum('current_stock'),
        ];

        $pdf = Pdf::loadView('reports.stock.export', compact('stocks', 'summary'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $fileName = 'Laporan_Stok_' . Carbon::now()->format('YmdHis') . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportMovements(Request $request)
    {
        $query = StockMutation::with('material', 'warehouse', 'creator');

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('mutation_type')) {
            $query->where('mutation_type', $request->mutation_type);
        }

        $mutations = $query->latest('created_at')->get();

        $summary = [
            'total_movements' => $mutations->count(),
            'total_in' => $mutations->where('mutation_type', 'in')->sum('qty'),
            'total_out' => $mutations->where('mutation_type', 'out')->sum('qty'),
        ];

        $pdf = Pdf::loadView('reports.stock.movements-export', compact('mutations', 'summary'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $fileName = 'Laporan_Mutasi_Stok_' . Carbon::now()->format('YmdHis') . '.pdf';

        return $pdf->download($fileName);
    }
}