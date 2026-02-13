<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\StockMutation;
use Illuminate\Http\Request;

class MutationController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMutation::with('warehouse', 'material', 'creator');

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('type')) {
            $query->where('mutation_type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $mutations = $query->latest()->paginate(50);

        return view('mutations.index', compact('mutations'));
    }
}