#!/bin/bash

# =============================================
# COMPLETE LARAVEL LOGISTICS FILES GENERATOR
# =============================================

echo "🚀 Generating Complete Laravel Logistics Application..."
echo ""

# =============================================
# 1. MODELS WITH RELATIONSHIPS
# =============================================
echo "📦 Creating Models with relationships..."

# User Model
cat > app/Models/User.php << 'USEREOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'role',
        'email',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeknik()
    {
        return $this->role === 'teknik';
    }
}
USEREOF

# Material Model
cat > app/Models/Material.php << 'MATERIALEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $primaryKey = 'material_id';

    protected $fillable = [
        'material_code',
        'material_name',
        'category',
        'unit',
        'min_stock',
        'description',
        'costing_method',
        'is_active',
    ];

    protected $casts = [
        'min_stock' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class, 'material_id');
    }

    public function batches()
    {
        return $this->hasMany(StockBatch::class, 'material_id');
    }
}
MATERIALEOF

# Warehouse Model
cat > app/Models/Warehouse.php << 'WAREHOUSEEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $primaryKey = 'warehouse_id';
    public $timestamps = false;

    protected $fillable = [
        'warehouse_code',
        'warehouse_name',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stocks()
    {
        return $this->hasMany(WarehouseStock::class, 'warehouse_id');
    }
}
WAREHOUSEEOF

# PurchaseRequest Model
cat > app/Models/PurchaseRequest.php << 'PURCHASEREQUESTEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'request_number',
        'request_date',
        'requested_by',
        'letter_number',
        'letter_date',
        'purpose',
        'status',
        'approved_by',
        'approved_date',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'date',
        'letter_date' => 'date',
        'approved_date' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseRequestDetail::class, 'request_id');
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class, 'request_id');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function canBeApproved()
    {
        return $this->isPending() && auth()->user()->isAdmin();
    }
}
PURCHASEREQUESTEOF

# PurchaseRequestDetail Model
cat > app/Models/PurchaseRequestDetail.php << 'PURCHASEREQUESTDETAILEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_id';
    public $timestamps = false;

    protected $fillable = [
        'request_id',
        'material_id',
        'qty_requested',
        'notes',
    ];

    protected $casts = [
        'qty_requested' => 'decimal:2',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'request_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
PURCHASEREQUESTDETAILEOF

# Purchase Model
cat > app/Models/Purchase.php << 'PURCHASEEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $primaryKey = 'purchase_id';

    protected $fillable = [
        'purchase_number',
        'request_id',
        'purchase_date',
        'purchased_by',
        'supplier_name',
        'supplier_contact',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'request_id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }

    public function goodsReceipt()
    {
        return $this->hasOne(GoodsReceipt::class, 'purchase_id');
    }
}
PURCHASEEOF

# PurchaseDetail Model
cat > app/Models/PurchaseDetail.php << 'PURCHASEDETAILEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_id';
    public $timestamps = false;

    protected $fillable = [
        'purchase_id',
        'material_id',
        'qty_ordered',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'qty_ordered' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
PURCHASEDETAILEOF

# GoodsReceipt Model
cat > app/Models/GoodsReceipt.php << 'GOODSRECEIPTEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $primaryKey = 'receipt_id';

    protected $fillable = [
        'receipt_number',
        'purchase_id',
        'receipt_date',
        'received_by',
        'status',
        'is_corrected',
        'notes',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'is_corrected' => 'boolean',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function details()
    {
        return $this->hasMany(GoodsReceiptDetail::class, 'receipt_id');
    }

    public function batches()
    {
        return $this->hasMany(StockBatch::class, 'receipt_id');
    }
}
GOODSRECEIPTEOF

# GoodsReceiptDetail Model
cat > app/Models/GoodsReceiptDetail.php << 'GOODSRECEIPTDETAILEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_id';
    public $timestamps = false;

    protected $fillable = [
        'receipt_id',
        'material_id',
        'qty_ordered',
        'qty_received',
        'unit_price',
        'condition_status',
        'notes',
    ];

    protected $casts = [
        'qty_ordered' => 'decimal:2',
        'qty_received' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
GOODSRECEIPTDETAILEOF

# StockBatch Model
cat > app/Models/StockBatch.php << 'STOCKBATCHEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    use HasFactory;

    protected $primaryKey = 'batch_id';

    protected $fillable = [
        'warehouse_id',
        'material_id',
        'receipt_id',
        'batch_number',
        'purchase_date',
        'unit_price',
        'qty_in',
        'qty_remaining',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'unit_price' => 'decimal:2',
        'qty_in' => 'decimal:2',
        'qty_remaining' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
STOCKBATCHEOF

# WarehouseStock Model
cat > app/Models/WarehouseStock.php << 'WAREHOUSESTOCKEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $primaryKey = 'stock_id';

    protected $fillable = [
        'warehouse_id',
        'material_id',
        'current_stock',
        'average_price',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'average_price' => 'decimal:2',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
WAREHOUSESTOCKEOF

# StockMutation Model
cat > app/Models/StockMutation.php << 'STOCKMUTATIONEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    use HasFactory;

    protected $primaryKey = 'mutation_id';

    protected $fillable = [
        'warehouse_id',
        'material_id',
        'mutation_type',
        'reference_type',
        'reference_id',
        'qty',
        'unit_price',
        'total_value',
        'stock_before',
        'stock_after',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_value' => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
    ];
}
STOCKMUTATIONEOF

# Worker Model
cat > app/Models/Worker.php << 'WORKEREOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $primaryKey = 'worker_id';

    protected $fillable = [
        'worker_code',
        'full_name',
        'phone',
        'address',
        'worker_type',
        'daily_rate',
        'is_active',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
WORKEREOF

# ProjectLocation Model
cat > app/Models/ProjectLocation.php << 'PROJECTLOCATIONEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectLocation extends Model
{
    use HasFactory;

    protected $primaryKey = 'location_id';

    protected $fillable = [
        'kavling',
        'blok',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
PROJECTLOCATIONEOF

# Activity Model
cat > app/Models/Activity.php << 'ACTIVITYEOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $primaryKey = 'activity_id';

    protected $fillable = [
        'activity_code',
        'activity_name',
        'location_id',
        'activity_type',
        'start_date',
        'end_date',
        'status',
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function location()
    {
        return $this->belongsTo(ProjectLocation::class, 'location_id');
    }
}
ACTIVITYEOF

echo "✅ Models created successfully!"
echo ""

# =============================================
# 2. DASHBOARD CONTROLLER
# =============================================
echo "🎮 Creating Controllers..."

cat > app/Http/Controllers/DashboardController.php << 'DASHBOARDEOF'
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
DASHBOARDEOF

echo "✅ Controllers created successfully!"
echo ""

echo "=========================================="
echo "✨ Generation Complete!"
echo "=========================================="
echo ""
echo "Next: Run the other scripts for views and components"
echo "=========================================="