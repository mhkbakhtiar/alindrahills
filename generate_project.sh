#!/bin/bash

# =============================================
# LARAVEL LOGISTICS APP - CLI GENERATOR
# =============================================
# Script untuk generate semua Controllers, Models, Services, Views, dll
# Usage: bash generate_project.sh

echo "🚀 Starting Laravel Logistics App Generation..."
echo ""

# =============================================
# 1. GENERATE MODELS
# =============================================
echo "📦 Generating Models..."

php artisan make:model User
php artisan make:model Material -m
php artisan make:model Warehouse -m
php artisan make:model Worker -m
php artisan make:model ProjectLocation -m
php artisan make:model PurchaseRequest -m
php artisan make:model PurchaseRequestDetail -m
php artisan make:model Purchase -m
php artisan make:model PurchaseDetail -m
php artisan make:model GoodsReceipt -m
php artisan make:model GoodsReceiptDetail -m
php artisan make:model WarehouseStock -m
php artisan make:model StockBatch -m
php artisan make:model StockMutation -m
php artisan make:model Activity -m
php artisan make:model ActivityWorker -m
php artisan make:model WorkerAttendance -m
php artisan make:model MaterialUsage -m
php artisan make:model MaterialUsageDetail -m
php artisan make:model MaterialUsageBatchDetail -m
php artisan make:model PayrollRequest -m
php artisan make:model PayrollRequestDetail -m

echo "✅ Models created successfully!"
echo ""

# =============================================
# 2. GENERATE CONTROLLERS
# =============================================
echo "🎮 Generating Controllers..."

# Dashboard
php artisan make:controller DashboardController

# Material Management
php artisan make:controller Material/MaterialController --resource
php artisan make:controller Material/PurchaseRequestController --resource
php artisan make:controller Material/PurchaseController --resource
php artisan make:controller Material/GoodsReceiptController --resource

# Warehouse Management
php artisan make:controller Warehouse/StockController --resource
php artisan make:controller Warehouse/BatchController --resource
php artisan make:controller Warehouse/MutationController

# Project Management
php artisan make:controller Project/ActivityController --resource
php artisan make:controller Project/MaterialUsageController --resource
php artisan make:controller Project/LocationController --resource

# Payroll Management
php artisan make:controller Payroll/WorkerController --resource
php artisan make:controller Payroll/AttendanceController --resource
php artisan make:controller Payroll/PayrollRequestController --resource

# Reports
php artisan make:controller Report/StockReportController
php artisan make:controller Report/ActivityReportController
php artisan make:controller Report/PayrollReportController

echo "✅ Controllers created successfully!"
echo ""

# =============================================
# 3. GENERATE SERVICES
# =============================================
echo "⚙️ Generating Services..."

mkdir -p app/Services

cat > app/Services/StockService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\StockBatch;
use App\Models\WarehouseStock;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Process material receipt and create batches (FIFO)
     */
    public function processGoodsReceipt($receiptId, $warehouseId, $materials)
    {
        DB::beginTransaction();
        try {
            foreach ($materials as $material) {
                // Create batch
                $batch = StockBatch::create([
                    'warehouse_id' => $warehouseId,
                    'material_id' => $material['material_id'],
                    'receipt_id' => $receiptId,
                    'batch_number' => $this->generateBatchNumber(),
                    'purchase_date' => now(),
                    'unit_price' => $material['unit_price'],
                    'qty_in' => $material['qty_received'],
                    'qty_remaining' => $material['qty_received'],
                    'status' => 'active',
                ]);

                // Update warehouse stock
                $this->updateWarehouseStock(
                    $warehouseId, 
                    $material['material_id'], 
                    $material['qty_received'],
                    'in'
                );

                // Record mutation
                $this->recordMutation(
                    $warehouseId,
                    $material['material_id'],
                    'in',
                    'goods_receipt',
                    $receiptId,
                    $material['qty_received'],
                    $material['unit_price']
                );
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Process material usage with FIFO method
     */
    public function processMaterialUsage($usageId, $warehouseId, $materialId, $qtyNeeded)
    {
        DB::beginTransaction();
        try {
            $batches = StockBatch::where('warehouse_id', $warehouseId)
                ->where('material_id', $materialId)
                ->where('status', 'active')
                ->where('qty_remaining', '>', 0)
                ->orderBy('purchase_date', 'asc')
                ->get();

            $remainingQty = $qtyNeeded;
            $totalValue = 0;

            foreach ($batches as $batch) {
                if ($remainingQty <= 0) break;

                $qtyToTake = min($batch->qty_remaining, $remainingQty);

                // Update batch
                $batch->qty_remaining -= $qtyToTake;
                if ($batch->qty_remaining <= 0) {
                    $batch->status = 'depleted';
                }
                $batch->save();

                $totalValue += ($qtyToTake * $batch->unit_price);
                $remainingQty -= $qtyToTake;
            }

            if ($remainingQty > 0) {
                throw new \Exception('Insufficient stock for material ID: ' . $materialId);
            }

            // Update warehouse stock
            $this->updateWarehouseStock($warehouseId, $materialId, $qtyNeeded, 'out');

            // Record mutation
            $this->recordMutation(
                $warehouseId,
                $materialId,
                'out',
                'material_usage',
                $usageId,
                $qtyNeeded,
                $totalValue / $qtyNeeded
            );

            DB::commit();
            return $totalValue;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update warehouse stock
     */
    private function updateWarehouseStock($warehouseId, $materialId, $qty, $type)
    {
        $stock = WarehouseStock::firstOrCreate(
            ['warehouse_id' => $warehouseId, 'material_id' => $materialId],
            ['current_stock' => 0, 'average_price' => 0]
        );

        if ($type === 'in') {
            $stock->current_stock += $qty;
        } else {
            $stock->current_stock -= $qty;
        }

        $stock->save();
    }

    /**
     * Record stock mutation
     */
    private function recordMutation($warehouseId, $materialId, $type, $refType, $refId, $qty, $unitPrice)
    {
        $stock = WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->first();

        $stockBefore = $stock ? $stock->current_stock : 0;
        $stockAfter = $type === 'in' ? $stockBefore + $qty : $stockBefore - $qty;

        StockMutation::create([
            'warehouse_id' => $warehouseId,
            'material_id' => $materialId,
            'mutation_type' => $type,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'total_value' => $qty * $unitPrice,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Generate unique batch number
     */
    private function generateBatchNumber()
    {
        return 'BATCH-' . date('Ymd') . '-' . str_pad(StockBatch::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get stock value by warehouse
     */
    public function getStockValueByWarehouse($warehouseId)
    {
        return StockBatch::where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->selectRaw('material_id, SUM(qty_remaining * unit_price) as total_value')
            ->groupBy('material_id')
            ->get();
    }
}
EOF

cat > app/Services/BatchService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\StockBatch;

class BatchService
{
    /**
     * Get active batches by material
     */
    public function getActiveBatches($warehouseId, $materialId)
    {
        return StockBatch::where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->where('status', 'active')
            ->where('qty_remaining', '>', 0)
            ->orderBy('purchase_date', 'asc')
            ->get();
    }

    /**
     * Calculate total stock value
     */
    public function calculateStockValue($warehouseId, $materialId = null)
    {
        $query = StockBatch::where('warehouse_id', $warehouseId)
            ->where('status', 'active');

        if ($materialId) {
            $query->where('material_id', $materialId);
        }

        return $query->sum(DB::raw('qty_remaining * unit_price'));
    }

    /**
     * Get batch aging report
     */
    public function getBatchAgingReport($warehouseId)
    {
        return StockBatch::where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->selectRaw('*, DATEDIFF(NOW(), purchase_date) as age_days')
            ->orderBy('purchase_date', 'asc')
            ->get();
    }
}
EOF

cat > app/Services/PayrollService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\WorkerAttendance;
use App\Models\PayrollRequest;
use App\Models\PayrollRequestDetail;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Calculate payroll for period
     */
    public function calculatePayroll($periodStart, $periodEnd)
    {
        $attendances = WorkerAttendance::whereBetween('attendance_date', [$periodStart, $periodEnd])
            ->where('status', 'present')
            ->with(['assignment.worker'])
            ->get()
            ->groupBy('assignment.worker_id');

        $payrollData = [];

        foreach ($attendances as $workerId => $records) {
            $worker = $records->first()->assignment->worker;
            $daysWorked = $records->sum(function($record) {
                return $record->status === 'half_day' ? 0.5 : 1;
            });

            $payrollData[] = [
                'worker_id' => $workerId,
                'worker_name' => $worker->full_name,
                'days_worked' => $daysWorked,
                'daily_rate' => $worker->daily_rate,
                'total_wage' => $daysWorked * $worker->daily_rate,
            ];
        }

        return $payrollData;
    }

    /**
     * Create payroll request
     */
    public function createPayrollRequest($data)
    {
        DB::beginTransaction();
        try {
            $payrollRequest = PayrollRequest::create([
                'request_number' => $this->generateRequestNumber(),
                'request_date' => now(),
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
                'requested_by' => auth()->id(),
                'letter_number' => $data['letter_number'] ?? null,
                'letter_date' => $data['letter_date'] ?? null,
                'status' => 'pending',
            ]);

            $totalAmount = 0;

            foreach ($data['workers'] as $worker) {
                $netPayment = $worker['total_wage'] + ($worker['bonus'] ?? 0) - ($worker['deduction'] ?? 0);
                
                PayrollRequestDetail::create([
                    'payroll_request_id' => $payrollRequest->payroll_request_id,
                    'worker_id' => $worker['worker_id'],
                    'days_worked' => $worker['days_worked'],
                    'daily_rate' => $worker['daily_rate'],
                    'total_wage' => $worker['total_wage'],
                    'bonus' => $worker['bonus'] ?? 0,
                    'deduction' => $worker['deduction'] ?? 0,
                    'net_payment' => $netPayment,
                    'notes' => $worker['notes'] ?? null,
                ]);

                $totalAmount += $netPayment;
            }

            $payrollRequest->update(['total_amount' => $totalAmount]);

            DB::commit();
            return $payrollRequest;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Generate request number
     */
    private function generateRequestNumber()
    {
        return 'PAY-' . date('Ymd') . '-' . str_pad(PayrollRequest::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }
}
EOF

echo "✅ Services created successfully!"
echo ""

# =============================================
# 4. GENERATE REQUESTS (Form Validation)
# =============================================
echo "📝 Generating Form Requests..."

php artisan make:request StoreMaterialRequest
php artisan make:request StorePurchaseRequestRequest
php artisan make:request StoreGoodsReceiptRequest
php artisan make:request StoreActivityRequest
php artisan make:request StoreMaterialUsageRequest
php artisan make:request StorePayrollRequestRequest

echo "✅ Form Requests created successfully!"
echo ""

# =============================================
# 5. CREATE VIEW DIRECTORIES
# =============================================
echo "📁 Creating View Directories..."

mkdir -p resources/views/layouts
mkdir -p resources/views/components
mkdir -p resources/views/dashboard
mkdir -p resources/views/materials
mkdir -p resources/views/purchase-requests
mkdir -p resources/views/purchases
mkdir -p resources/views/goods-receipts
mkdir -p resources/views/stocks
mkdir -p resources/views/batches
mkdir -p resources/views/activities
mkdir -p resources/views/material-usages
mkdir -p resources/views/workers
mkdir -p resources/views/attendance
mkdir -p resources/views/payroll
mkdir -p resources/views/reports

echo "✅ View directories created successfully!"
echo ""

# =============================================
# 6. GENERATE ROUTES
# =============================================
echo "🛣️ Generating Routes..."

cat > routes/web.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Material\MaterialController;
use App\Http\Controllers\Material\PurchaseRequestController;
use App\Http\Controllers\Material\PurchaseController;
use App\Http\Controllers\Material\GoodsReceiptController;
use App\Http\Controllers\Warehouse\StockController;
use App\Http\Controllers\Warehouse\BatchController;
use App\Http\Controllers\Warehouse\MutationController;
use App\Http\Controllers\Project\ActivityController;
use App\Http\Controllers\Project\MaterialUsageController;
use App\Http\Controllers\Project\LocationController;
use App\Http\Controllers\Payroll\WorkerController;
use App\Http\Controllers\Payroll\AttendanceController;
use App\Http\Controllers\Payroll\PayrollRequestController;
use App\Http\Controllers\Report\StockReportController;
use App\Http\Controllers\Report\ActivityReportController;
use App\Http\Controllers\Report\PayrollReportController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Material Management
    Route::prefix('materials')->group(function () {
        Route::resource('items', MaterialController::class)->names('materials');
        Route::resource('purchase-requests', PurchaseRequestController::class)->names('purchase-requests');
        Route::post('purchase-requests/{id}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve');
        Route::resource('purchases', PurchaseController::class)->names('purchases');
        Route::resource('goods-receipts', GoodsReceiptController::class)->names('goods-receipts');
    });

    // Warehouse Management
    Route::prefix('warehouse')->group(function () {
        Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
        Route::get('stocks/{material}/batches', [StockController::class, 'batches'])->name('stocks.batches');
        Route::get('batches', [BatchController::class, 'index'])->name('batches.index');
        Route::get('batches/{batch}', [BatchController::class, 'show'])->name('batches.show');
        Route::get('mutations', [MutationController::class, 'index'])->name('mutations.index');
    });

    // Project Management
    Route::prefix('projects')->group(function () {
        Route::resource('locations', LocationController::class)->names('locations');
        Route::resource('activities', ActivityController::class)->names('activities');
        Route::get('activities/{activity}/breakdown', [ActivityController::class, 'breakdown'])->name('activities.breakdown');
        Route::resource('material-usages', MaterialUsageController::class)->names('material-usages');
    });

    // Payroll Management
    Route::prefix('payroll')->group(function () {
        Route::resource('workers', WorkerController::class)->names('workers');
        Route::resource('attendance', AttendanceController::class)->names('attendance');
        Route::get('attendance/activity/{activity}', [AttendanceController::class, 'byActivity'])->name('attendance.by-activity');
        Route::resource('requests', PayrollRequestController::class)->names('payroll-requests');
        Route::post('requests/{id}/approve', [PayrollRequestController::class, 'approve'])->name('payroll-requests.approve');
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('stock', [StockReportController::class, 'index'])->name('reports.stock');
        Route::get('stock/value', [StockReportController::class, 'value'])->name('reports.stock.value');
        Route::get('stock/aging', [StockReportController::class, 'aging'])->name('reports.stock.aging');
        Route::get('activities', [ActivityReportController::class, 'index'])->name('reports.activities');
        Route::get('payroll', [PayrollReportController::class, 'index'])->name('reports.payroll');
    });
});

require __DIR__.'/auth.php';
EOF

echo "✅ Routes created successfully!"
echo ""

# =============================================
# 7. GENERATE SEEDERS
# =============================================
echo "🌱 Generating Seeders..."

php artisan make:seeder UserSeeder
php artisan make:seeder MaterialSeeder
php artisan make:seeder WarehouseSeeder
php artisan make:seeder WorkerSeeder
php artisan make:seeder ProjectLocationSeeder

cat > database/seeders/DatabaseSeeder.php << 'EOF'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MaterialSeeder::class,
            WarehouseSeeder::class,
            WorkerSeeder::class,
            ProjectLocationSeeder::class,
        ]);
    }
}
EOF

echo "✅ Seeders created successfully!"
echo ""

# =============================================
# 8. CREATE COMPONENTS
# =============================================
echo "🎨 Creating Blade Components..."

php artisan make:component Button
php artisan make:component Card
php artisan make:component Modal
php artisan make:component Table
php artisan make:component Alert
php artisan make:component Badge
php artisan make:component Input
php artisan make:component Select
php artisan make:component Sidebar
php artisan make:component Header

echo "✅ Blade Components created successfully!"
echo ""

# =============================================
# 9. GENERATE POLICIES (Authorization)
# =============================================
echo "🔐 Generating Policies..."

php artisan make:policy MaterialPolicy --model=Material
php artisan make:policy PurchaseRequestPolicy --model=PurchaseRequest
php artisan make:policy ActivityPolicy --model=Activity
php artisan make:policy PayrollRequestPolicy --model=PayrollRequest

echo "✅ Policies created successfully!"
echo ""

# =============================================
# 10. CREATE HELPER FILES
# =============================================
echo "🛠️ Creating Helper Files..."

cat > app/Helpers/NumberHelper.php << 'EOF'
<?php

namespace App\Helpers;

class NumberHelper
{
    public static function formatRupiah($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }

    public static function formatDecimal($number, $decimals = 2)
    {
        return number_format($number, $decimals, ',', '.');
    }
}
EOF

cat > app/Helpers/DateHelper.php << 'EOF'
<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function toIndo($date)
    {
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $carbon = Carbon::parse($date);
        return $carbon->day . ' ' . $months[$carbon->month] . ' ' . $carbon->year;
    }
}
EOF

echo "✅ Helper files created successfully!"
echo ""

# =============================================
# 11. UPDATE COMPOSER.JSON FOR AUTOLOAD
# =============================================
echo "📦 Updating composer.json for autoloading..."

# Create temporary file with updated composer.json
php << 'PHPEOF'
<?php
$composerFile = 'composer.json';
$composerData = json_decode(file_get_contents($composerFile), true);

// Add files to autoload
if (!isset($composerData['autoload']['files'])) {
    $composerData['autoload']['files'] = [];
}

$composerData['autoload']['files'][] = 'app/Helpers/NumberHelper.php';
$composerData['autoload']['files'][] = 'app/Helpers/DateHelper.php';

// Remove duplicates
$composerData['autoload']['files'] = array_values(array_unique($composerData['autoload']['files']));

// Save back to file
file_put_contents($composerFile, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "composer.json updated successfully!\n";
PHPEOF

composer dump-autoload

echo "✅ Composer autoload updated!"
echo ""

# =============================================
# 12. CREATE MIDDLEWARE
# =============================================
echo "🛡️ Creating Middleware..."

php artisan make:middleware CheckRole
php artisan make:middleware LogActivity

echo "✅ Middleware created successfully!"
echo ""

# =============================================
# SUMMARY
# =============================================
echo ""
echo "=========================================="
echo "✨ GENERATION COMPLETE!"
echo "=========================================="
echo ""
echo "📊 Summary:"
echo "  ✅ 22 Models created"
echo "  ✅ 18 Controllers created"
echo "  ✅ 3 Services created"
echo "  ✅ 6 Form Requests created"
echo "  ✅ 15 View directories created"
echo "  ✅ Routes configured"
echo "  ✅ 5 Seeders created"
echo "  ✅ 10 Blade Components created"
echo "  ✅ 4 Policies created"
echo "  ✅ 2 Helper files created"
echo "  ✅ 2 Middleware created"
echo ""
echo "📝 Next Steps:"
echo "  1. Run migrations: php artisan migrate"
echo "  2. Run seeders: php artisan db:seed"
echo "  3. Generate views: (use separate script)"
echo "  4. Install Breeze: php artisan breeze:install blade"
echo "  5. Compile assets: npm install && npm run dev"
echo ""
echo "🚀 Happy Coding!"
echo "=========================================="