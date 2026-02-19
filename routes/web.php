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
use App\Http\Controllers\Project\ContractorController;
use App\Http\Controllers\Project\ActivityWorkerController;
use App\Http\Controllers\Payroll\WorkerController;
use App\Http\Controllers\Payroll\WorkerAttendanceController;
use App\Http\Controllers\Payroll\PayrollRequestController;
use App\Http\Controllers\Report\StockReportController;
use App\Http\Controllers\Report\ActivityReportController;
use App\Http\Controllers\Report\PayrollReportController;
use App\Http\Controllers\Master\PembeliController;
use App\Http\Controllers\Master\KavlingPembeliController;

use App\Http\Controllers\Accounting\PerkiraanController;
use App\Http\Controllers\Accounting\JurnalController;
use App\Http\Controllers\Accounting\ItemJurnalController;
use App\Http\Controllers\Accounting\TahunAnggaranController;
use App\Http\Controllers\Accounting\DepartemenController;
use App\Http\Controllers\Accounting\LaporanController;




Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Material Management
    Route::prefix('materials')->group(function () {
        Route::resource('items', MaterialController::class)->parameters(['items' => 'material'])->names('materials');
        Route::resource('purchase-requests', PurchaseRequestController::class)->names('purchase-requests');
        Route::get('/purchase-requests/{purchaseRequest}/print-invoice', [PurchaseRequestController::class, 'printInvoice'])->name('purchase-requests.print-invoice');
        Route::PATCH('purchase-requests/{id}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve');
        
        Route::resource('purchases', PurchaseController::class)->names('purchases');
        Route::post('/{purchase}/post-jurnal', [PurchaseController::class, 'postJurnal'])->name('post-jurnal');
        
        Route::resource('goods-receipts', GoodsReceiptController::class)->names('goods-receipts');
        Route::get('/goods-receipts/{goodsReceipt}/print-invoice', [GoodsReceiptController::class, 'printInvoice'])->name('goods-receipts.print-invoice');
    });

    // Warehouse Management
    Route::prefix('warehouse')->group(function () {
        Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
        Route::get('stocks/{material}/batches', [StockController::class, 'batches'])->name('stocks.batches');
        Route::get('batches', [BatchController::class, 'index'])->name('batches.index');
        Route::get('batches/{batch}', [BatchController::class, 'show'])->name('batches.show');
        Route::get('mutations', [MutationController::class, 'index'])->name('mutations.index');
    });

    Route::prefix('master')->name('master.')->group(function () {
        // Pembeli
        Route::resource('pembeli', PembeliController::class);
        
        // Kavling Pembeli
        Route::resource('kavling-pembeli', KavlingPembeliController::class);
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
        
        Route::resource('attendances', WorkerAttendanceController::class);
        
        Route::post('attendances/bulk-create', [WorkerAttendanceController::class, 'bulkCreate'])->name('attendances.bulk-create');
        
        Route::get('attendances-report/monthly', [WorkerAttendanceController::class, 'monthlyReport'])->name('attendances.monthly-report');

        Route::get('attendances-report/export-pdf', [WorkerAttendanceController::class, 'exportPdf'])->name('attendances.export-pdf');
        
        Route::prefix('requests')->name('payroll-requests.')->group(function () {
            Route::get('/', [PayrollRequestController::class, 'index'])->name('index');
            Route::get('/create', [PayrollRequestController::class, 'create'])->name('create');
            Route::post('/', [PayrollRequestController::class, 'store'])->name('store');
            Route::get('/{payrollRequest}', [PayrollRequestController::class, 'show'])->name('show');
            Route::get('/{payrollRequest}/edit', [PayrollRequestController::class, 'edit'])->name('edit');
            Route::patch('/{payrollRequest}', [PayrollRequestController::class, 'update'])->name('update');
            Route::delete('/{payrollRequest}', [PayrollRequestController::class, 'destroy'])->name('destroy');
            
            // AJAX route untuk get activity workers
            Route::get('/activity/{activity}/workers', [PayrollRequestController::class, 'getActivityWorkers'])->name('activity-workers');
            
            // Approve route
            Route::patch('/{payrollRequest}/approve', [PayrollRequestController::class, 'approve'])->name('approve');

            Route::get('/payroll-requests/{payrollRequest}/print-invoice', [PayrollRequestController::class, 'printInvoice'])->name('print-invoice');

            Route::get('/payroll-requests/{payrollRequest}/print-slip/{workerId}', [PayrollRequestController::class, 'printWorkerSlip'])->name('print-slip');
        });

        Route::post('requests/{id}/approve', [PayrollRequestController::class, 'approve'])->name('approve');
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('stock', [StockReportController::class, 'index'])->name('reports.stock');
        Route::get('stock/value', [StockReportController::class, 'value'])->name('reports.stock.value');
        Route::get('stock/aging', [StockReportController::class, 'aging'])->name('reports.stock.aging');
        Route::get('activities', [ActivityReportController::class, 'index'])->name('reports.activities');
        Route::get('payroll', [PayrollReportController::class, 'index'])->name('reports.payroll');
    });

    Route::post('activity-workers', [ActivityWorkerController::class, 'store'])->name('activity-workers.store');
    Route::delete('activity-workers/{activityWorker}', [ActivityWorkerController::class, 'destroy'])->name('activity-workers.destroy');

    Route::resource('contractors', ContractorController::class);

    Route::put('activities/{activity}/contractors', [ActivityController::class, 'updateContractors'])->name('activities.contractors.update');

    // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        // Activity Reports
        Route::get('/activities', [ActivityReportController::class, 'index'])->name('activities.index');
        Route::get('/activities/export', [ActivityReportController::class, 'export'])->name('activities.export');
        Route::get('/activities/{activity}/detail', [ActivityReportController::class, 'detail'])->name('activities.detail');
        
        // Payroll Reports
        Route::get('/payroll', [PayrollReportController::class, 'index'])->name('payroll.index');
        Route::get('/payroll/export', [PayrollReportController::class, 'export'])->name('payroll.export');
        Route::get('/payroll/worker-summary', [PayrollReportController::class, 'workerSummary'])->name('payroll.worker-summary');
        
        // Stock Reports
        Route::get('/stock', [StockReportController::class, 'index'])->name('stock.index');
        Route::get('/stock/export', [StockReportController::class, 'export'])->name('stock.export');
        Route::get('/stock/movements', [StockReportController::class, 'movements'])->name('stock.movements');
        Route::get('/stock/movements/export', [StockReportController::class, 'exportMovements'])->name('stock.export-movements');
    });


    // Accounting Routes
    Route::prefix('accounting')->name('accounting.')->middleware(['auth'])->group(function () {
    
        // Dashboard
        Route::get('/', function () {
            return view('accounting.dashboard');
        })->name('dashboard');

        // ========== MASTER DATA ==========

        // Perkiraan CRUD
        Route::resource('perkiraan', PerkiraanController::class);
        
        // Export
        Route::get('perkiraan-export', [PerkiraanController::class, 'export'])->name('perkiraan.export');
        
        // Reports
        Route::get('perkiraan/{perkiraan}/ledger', [PerkiraanController::class, 'ledger'])->name('perkiraan.ledger');
        Route::get('perkiraan/{perkiraan}/ledger/print', [PerkiraanController::class, 'printLedger'])->name('perkiraan.ledger.print');
        
        Route::get('reports/trial-balance', [PerkiraanController::class, 'trialBalance'])->name('reports.trial-balance');
        
        Route::get('reports/balance-sheet', [PerkiraanController::class, 'balanceSheet'])->name('reports.balance-sheet');
        
        Route::get('reports/income-statement', [PerkiraanController::class, 'incomeStatement'])->name('reports.income-statement');
        
        // Utilities
        Route::post('perkiraan-recalculate', [PerkiraanController::class, 'recalculateBalances'])->name('perkiraan.recalculate');
        
        // Departemen
        Route::resource('departemen', DepartemenController::class);
        
        // Tahun Anggaran
        Route::resource('tahun-anggaran', TahunAnggaranController::class);
        Route::post('tahun-anggaran/{id}/activate', [TahunAnggaranController::class, 'activate'])->name('tahun-anggaran.activate');
        Route::post('tahun-anggaran/{id}/close', [TahunAnggaranController::class, 'close'])->name('tahun-anggaran.close');
        
        // ========== TRANSAKSI ==========
        
        // Jurnal
        Route::resource('jurnal', JurnalController::class);
        Route::post('jurnal/import', [JurnalController::class, 'import'])->name('jurnal.import');
        Route::post('jurnal/{jurnal}/post', [JurnalController::class, 'post'])->name('jurnal.post');
        Route::post('jurnal/{jurnal}/void', [JurnalController::class, 'void'])->name('jurnal.void');
        Route::get('jurnal/{jurnal}/print', [JurnalController::class, 'print'])->name('jurnal.print');

        Route::get('recalculate-saldo', [JurnalController::class, 'recalculateSaldo']);
        
        // Item Jurnal (AJAX endpoints)
        Route::prefix('item-jurnal')->name('item-jurnal.')->group(function () {
            Route::get('jurnal/{jurnalId}', [ItemJurnalController::class, 'getByJurnal'])->name('by-jurnal');
            Route::post('/', [ItemJurnalController::class, 'store'])->name('store');
            Route::put('{id}', [ItemJurnalController::class, 'update'])->name('update');
            Route::delete('{id}', [ItemJurnalController::class, 'destroy'])->name('destroy');
        });
        
        // ========== LAPORAN ==========
        
        Route::prefix('laporan')->name('laporan.')->group(function () {
            
            // Jurnal Umum
            Route::get('jurnal-umum', [LaporanController::class, 'jurnalUmum'])->name('jurnal-umum');
            Route::get('jurnal-umum/print', [LaporanController::class, 'jurnalUmumPrint'])->name('jurnal-umum.print');
            Route::get('jurnal-umum/excel', [LaporanController::class, 'jurnalUmumExcel'])->name('jurnal-umum.excel');
            
            // Buku Besar
            Route::get('buku-besar', [LaporanController::class, 'bukuBesar'])->name('buku-besar');
            Route::get('buku-besar/print', [LaporanController::class, 'bukuBesarPrint'])->name('buku-besar.print');
            Route::get('buku-besar/excel', [LaporanController::class, 'bukuBesarExcel'])->name('buku-besar.excel');
            
            // Buku Pembantu Per Kavling
            Route::get('buku-pembantu-kavling', [LaporanController::class, 'bukuPembantuKavling'])->name('buku-pembantu-kavling');
            Route::get('buku-pembantu-kavling/print', [LaporanController::class, 'bukuPembantuKavlingPrint'])->name('buku-pembantu-kavling.print');
            
            // Neraca
            Route::get('neraca', [LaporanController::class, 'neraca'])->name('neraca');
            Route::get('neraca/print', [LaporanController::class, 'neracaPrint'])->name('neraca.print');
            Route::get('neraca/excel', [LaporanController::class, 'neracaExcel'])->name('neraca.excel');
            
            // Laba Rugi
            Route::get('laba-rugi', [LaporanController::class, 'labaRugi'])->name('laba-rugi');
            Route::get('laba-rugi/print', [LaporanController::class, 'labaRugiPrint'])->name('laba-rugi.print');
            Route::get('laba-rugi/excel', [LaporanController::class, 'labaRugiExcel'])->name('laba-rugi.excel');
            
            // CALK (Catatan Atas Laporan Keuangan)
            Route::get('calk', [LaporanController::class, 'calk'])->name('calk');
            Route::get('calk/print', [LaporanController::class, 'calkPrint'])->name('calk.print');
        });
        
        // ========== API ENDPOINTS (untuk autocomplete, select2, dll) ==========
        
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('perkiraan/search', [PerkiraanController::class, 'search'])->name('perkiraan.search');
            Route::get('kavling/search', [JurnalController::class, 'searchKavling'])->name('kavling.search');
            Route::get('user/search', [JurnalController::class, 'searchUser'])->name('user.search');
            Route::get('jurnal/{id}/check-balance', [JurnalController::class, 'checkBalance'])->name('jurnal.check-balance');
        });
    });

});

require __DIR__.'/auth.php';
