<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\PurchaseRequest;
use App\Models\Activity;
use App\Models\StockBatch;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_materials' => Material::where('is_active', true)->count(),
            'pending_requests' => PurchaseRequest::where('status', 'pending')->count(),
            'active_activities' => Activity::where('status', 'ongoing')->count(),
            'low_stock_items' => 0,
        ];

        $recentActivities = Activity::with('location')->latest()->take(5)->get();
        $pendingRequests = PurchaseRequest::with('requester')->where('status', 'pending')->latest()->take(5)->get();
        $lowStockMaterials = collect();
        $stockValue = StockBatch::where('status', 'active')->sum(\DB::raw('qty_remaining * unit_price'));

        return view('dashboard.index', compact('stats', 'recentActivities', 'pendingRequests', 'lowStockMaterials', 'stockValue'));
    }
}
