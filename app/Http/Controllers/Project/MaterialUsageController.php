<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\MaterialUsage;
use App\Models\MaterialUsageDetail;
use App\Models\Activity;
use App\Models\Warehouse;
use App\Models\Material;
use App\Models\StockMutation;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialUsageController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $query = MaterialUsage::with('activity.location', 'warehouse', 'issuer');

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('usage_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('usage_date', '<=', $request->date_to);
        }

        $usages = $query->latest()->paginate(20);

        $activities = Activity::whereIn('status', ['planned', 'ongoing'])->get();

        return view('material-usages.index', compact('usages', 'activities'));
    }

    public function create(Request $request)
    {
        $activities = Activity::whereIn('status', ['planned', 'ongoing'])->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $availableMaterials = Material::where('is_active', true)->get();
        
        // Ambil activity_id dari query string
        $selectedActivityId = $request->query('activity_id');
        
        // Optional: Validasi apakah activity_id valid
        $selectedActivity = null;
        if ($selectedActivityId) {
            $selectedActivity = Activity::find($selectedActivityId);
        }

        return view('material-usages.create', compact(
            'activities', 
            'warehouses', 
            'availableMaterials',
            'selectedActivityId',
            'selectedActivity'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'warehouse_id' => 'required|exists:warehouses,warehouse_id',
            'usage_date' => 'required|date',
            'notes' => 'nullable',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,material_id',
            'materials.*.qty_used' => 'required|numeric|min:0.01',
            'materials.*.notes' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $materialUsage = MaterialUsage::create([
                'usage_number' => $this->generateUsageNumber(),
                'activity_id' => $validated['activity_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'usage_date' => $validated['usage_date'],
                'issued_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            $totalValue = 0;

            foreach ($validated['materials'] as $material) {
                // Process material usage with FIFO
                $value = $this->stockService->processMaterialUsage(
                    $materialUsage->usage_id,
                    $validated['warehouse_id'],
                    $material['material_id'],
                    $material['qty_used']
                );

                $totalValue += $value;

                MaterialUsageDetail::create([
                    'usage_id' => $materialUsage->usage_id,
                    'material_id' => $material['material_id'],
                    'qty_used' => $material['qty_used'],
                    'average_unit_price' => $value / $material['qty_used'],
                    'subtotal' => $value,
                    'notes' => $material['notes'],
                ]);
            }

            $materialUsage->update(['total_value' => $totalValue]);

            DB::commit();

            return redirect()->route('material-usages.index')
                ->with('success', 'Pengeluaran material berhasil dicatat');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Gagal mencatat pengeluaran: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(MaterialUsage $materialUsage)
    {
        $materialUsage->load([
            'activity.location', 
            'warehouse', 
            'details.material',
            'issuer'
        ]);

        // Get stock mutations for this usage
        $mutations = StockMutation::where('reference_type', 'material_usage')
            ->where('reference_id', $materialUsage->usage_id)
            ->with('material')
            ->get();

        return view('material-usages.show', compact('materialUsage', 'mutations'));
    }

    public function edit(MaterialUsage $materialUsage)
    {
        // Edit not recommended for material usages because it affects stock
        return view('material-usages.edit', compact('materialUsage'));
    }

    private function generateUsageNumber()
    {
        $date = date('Ymd');
        $count = MaterialUsage::whereDate('created_at', today())->count() + 1;
        return 'MU-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}