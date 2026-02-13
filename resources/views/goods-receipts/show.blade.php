@extends('layouts.app')

@section('title', 'Detail Penerimaan Barang')
@section('breadcrumb', 'Materials / Goods Receipts / Detail')

@section('content')

<div class="max-w-6xl mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Detail Penerimaan Barang</h2>
            <p class="text-sm text-gray-600">{{ $receipt->receipt_number }}</p>
        </div>
        <div class="flex gap-2">
            <!-- Print PDF Button -->
            <a href="{{ route('goods-receipts.print-invoice', $receipt) }}" 
               target="_blank"
               class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak PDF
            </a>
            
            <a href="{{ route('goods-receipts.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Status Badges -->
    <div class="mb-4 flex items-center gap-2">
        @if($receipt->status === 'received')
            <x-badge variant="success" class="text-sm px-4 py-2">✅ Received</x-badge>
        @elseif($receipt->status === 'corrected')
            <x-badge variant="warning" class="text-sm px-4 py-2">⚠️ Corrected</x-badge>
        @else
            <x-badge variant="info" class="text-sm px-4 py-2">{{ ucfirst($receipt->status) }}</x-badge>
        @endif

        @if($receipt->is_corrected)
            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                Ada Koreksi Qty/Kondisi
            </span>
        @endif

        @php
            $purchase = $receipt->purchase;
            $isPartialReceipt = false;
            $isFullyReceived = false;
            
            if ($purchase) {
                $isFullyReceived = $purchase->isFullyReceived();
                // Check if this receipt is partial (not receiving all ordered qty)
                foreach ($receipt->details as $detail) {
                    if ($detail->qty_received < $detail->qty_ordered) {
                        $isPartialReceipt = true;
                        break;
                    }
                }
            }
        @endphp

        @if($isPartialReceipt)
            <span class="px-3 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                🔄 Partial Receipt
            </span>
        @endif

        @if($isFullyReceived)
            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                ✓ PO Fully Received
            </span>
        @elseif($purchase)
            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                ⏳ Masih Ada Sisa
            </span>
        @endif
    </div>

    <!-- Purchase Order Info (if exists) -->
    @if($receipt->purchase)
        <x-card class="mb-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-semibold text-blue-900 mb-1">🛒 Purchase Order Terkait</h3>
                    <p class="text-base font-bold text-blue-900">{{ $receipt->purchase->purchase_number }}</p>
                    <p class="text-sm text-blue-800 mb-2">
                        Supplier: <strong>{{ $receipt->purchase->supplier_name }}</strong>
                        @if($receipt->purchase->supplier_contact)
                            | {{ $receipt->purchase->supplier_contact }}
                        @endif
                    </p>
                    
                    @php
                        $totalReceipts = $receipt->purchase->goodsReceipts->count();
                    @endphp
                    
                    @if($totalReceipts > 1)
                        <div class="mt-2 p-2 bg-blue-100 rounded text-xs text-blue-900">
                            📋 <strong>Multiple Receipts:</strong> PO ini memiliki {{ $totalReceipts }} penerimaan barang
                            @if(!$isFullyReceived)
                                <span class="text-orange-700">(Belum lengkap semua)</span>
                            @endif
                        </div>
                    @endif
                    
                    <a href="{{ route('purchases.show', $receipt->purchase) }}" 
                       class="inline-block mt-2 text-xs text-blue-700 hover:underline">
                        Lihat Detail Purchase Order →
                    </a>
                </div>
            </div>
        </x-card>
    @endif

    <!-- Receipt Information -->
    <x-card class="mb-4">
        <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Penerimaan</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Receipt</label>
                <p class="text-sm text-gray-900 font-medium">{{ $receipt->receipt_number }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Penerimaan</label>
                <p class="text-sm text-gray-900">{{ $receipt->receipt_date->format('d F Y') }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Diterima Oleh</label>
                <p class="text-sm text-gray-900">{{ $receipt->receiver->full_name ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Warehouse Tujuan</label>
                <p class="text-sm text-gray-900">{{ $receipt->warehouse->warehouse_name ?? '-' }}</p>
            </div>

            @if($receipt->notes)
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                    <div class="p-3 bg-gray-50 rounded-md">
                        <p class="text-sm text-gray-900">{{ $receipt->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </x-card>

    <!-- Material Details with Correction Info & Receipt History -->
    <x-card class="mb-4">
        <h3 class="text-md font-semibold text-gray-900 mb-4">Daftar Material yang Diterima</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No</th>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Material</th>
                        <th class="px-3 py-2 text-center font-semibold">Satuan</th>
                        <th class="px-3 py-2 text-right font-semibold">Total PO</th>
                        <th class="px-3 py-2 text-right font-semibold">Terima Ini</th>
                        <th class="px-3 py-2 text-right font-semibold">Selisih</th>
                        <th class="px-3 py-2 text-center font-semibold">Kondisi</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                        <th class="px-3 py-2 text-left font-semibold">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($receipt->details as $index => $detail)
                        @php
                            // Get PO detail untuk material ini
                            $poDetail = $receipt->purchase->details->where('material_id', $detail->material_id)->first();
                            $totalPO = $poDetail ? $poDetail->qty_ordered : $detail->qty_ordered;
                            
                            // Hitung selisih dari total PO (bukan qty_ordered di receipt ini)
                            $diff = $detail->qty_received - $totalPO;
                            $hasCorrection = $diff != 0 || $detail->condition_status !== 'good';
                            
                            // Get all receipts untuk material ini (untuk history)
                            $materialReceipts = $receipt->purchase->goodsReceipts()
                                ->with('details')
                                ->orderBy('receipt_date', 'asc')
                                ->get()
                                ->map(function($gr) use ($detail, $receipt) {
                                    $grDetail = $gr->details->where('material_id', $detail->material_id)->first();
                                    if ($grDetail) {
                                        return [
                                            'receipt_number' => $gr->receipt_number,
                                            'receipt_date' => $gr->receipt_date,
                                            'qty_received' => $grDetail->qty_received,
                                            'is_current' => $gr->receipt_id === $receipt->receipt_id
                                        ];
                                    }
                                    return null;
                                })
                                ->filter();
                                
                            $totalReceived = $materialReceipts->sum('qty_received');
                            $remaining = $totalPO - $totalReceived;
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $hasCorrection ? 'bg-yellow-50' : '' }}">
                            <td class="px-3 py-2">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-medium">{{ $detail->material->material_code }}</td>
                            <td class="px-3 py-2">
                                {{ $detail->material->material_name }}
                                
                                <!-- Receipt History Tooltip/Detail -->
                                @if($materialReceipts->count() > 1)
                                    <button type="button" 
                                            onclick="toggleHistory({{ $index }})" 
                                            class="ml-2 text-blue-600 hover:text-blue-800"
                                            title="Lihat history penerimaan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">{{ $detail->material->unit }}</td>
                            <td class="px-3 py-2 text-right text-gray-600">{{ number_format($totalPO, 2) }}</td>
                            <td class="px-3 py-2 text-right font-semibold text-green-600">{{ number_format($detail->qty_received, 2) }}</td>
                            <td class="px-3 py-2 text-right">
                                @if($materialReceipts->count() > 1)
                                    <button type="button" 
                                            onclick="toggleHistory({{ $index }})" 
                                            class="ml-2 text-blue-600 hover:text-blue-800"
                                            title="Lihat history penerimaan">
                                        Lihat History Selisih
                                    </button>
                                @else
                                    @if($diff != 0)
                                        <span class="font-semibold {{ $diff > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 2) }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($detail->condition_status === 'good')
                                    <x-badge variant="success">✓ Baik</x-badge>
                                @elseif($detail->condition_status === 'damaged')
                                    <x-badge variant="danger">✗ Rusak</x-badge>
                                @else
                                    <x-badge variant="warning">⚠ Tidak Lengkap</x-badge>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($detail->condition_status === 'good')
                                    <span class="text-xs text-green-600">✓ Masuk Stock</span>
                                @else
                                    <span class="text-xs text-red-600">✗ Tidak Masuk Stock</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-gray-600 text-xs">
                                {{ $detail->notes ?? '-' }}
                            </td>
                        </tr>
                        
                        <!-- Receipt History Row (collapsible) -->
                        @if($materialReceipts->count() > 1)
                            <tr id="history-{{ $index }}" class="hidden bg-blue-50 border-l-4 border-blue-500">
                                <td colspan="10" class="px-3 py-3">
                                    <div class="text-xs">
                                        <div class="font-semibold text-blue-900 mb-2">📋 History Penerimaan Material Ini:</div>
                                        <div class="space-y-1 ml-4">
                                            @foreach($materialReceipts as $idx => $hist)
                                                <div class="flex items-center gap-2 {{ $hist['is_current'] ? 'font-bold text-blue-900' : 'text-gray-700' }}">
                                                    <span class="w-4">{{ $idx + 1 }}.</span>
                                                    <span class="w-32">{{ $hist['receipt_number'] }}</span>
                                                    <span class="w-24">{{ $hist['receipt_date']->format('d M Y') }}</span>
                                                    <span class="font-semibold {{ $hist['is_current'] ? 'text-green-600' : 'text-blue-600' }}">
                                                        {{ number_format($hist['qty_received'], 2) }} {{ $detail->material->unit }}
                                                    </span>
                                                    @if($hist['is_current'])
                                                        <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs">← Receipt Ini</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            <div class="mt-2 pt-2 border-t border-blue-200 flex items-center gap-2 font-bold text-blue-900">
                                                <span class="w-4"></span>
                                                <span class="w-32">TOTAL DITERIMA:</span>
                                                <span class="w-24"></span>
                                                <span class="text-green-600">{{ number_format($totalReceived, 2) }} / {{ number_format($totalPO, 2) }} {{ $detail->material->unit }}</span>
                                            </div>
                                            @if($remaining > 0)
                                                <div class="ml-4 text-orange-700">
                                                    ⚠️ Sisa yang belum diterima: <strong>{{ number_format($remaining, 2) }} {{ $detail->material->unit }}</strong>
                                                </div>
                                            @elseif($remaining == 0)
                                                <div class="ml-4 text-green-700">
                                                    ✓ Material ini sudah lengkap diterima
                                                </div>
                                            @else
                                                <div class="ml-4 text-red-700">
                                                    ⚠️ Over receipt: <strong>{{ number_format(abs($remaining), 2) }} {{ $detail->material->unit }}</strong> lebih dari PO
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="10" class="px-3 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Correction Legend -->
        @if($receipt->is_corrected)
            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-yellow-800">
                        <strong>Keterangan Koreksi:</strong>
                        <ul class="mt-1 space-y-1 list-disc list-inside">
                            @foreach($receipt->details as $detail)
                                @php
                                    $diff = $detail->qty_received - $detail->qty_ordered;
                                @endphp
                                @if($diff != 0)
                                    <li>
                                        <strong>{{ $detail->material->material_name }}:</strong> 
                                        Qty berbeda 
                                        @if($materialReceipts->count() > 1)
                                            (Lihat History Selisih di tabel di atas)
                                        @else
                                            <span class="{{ $diff > 0 ? 'text-green-700' : 'text-red-700' }}">
                                                ({{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 2) }} {{ $detail->material->unit }})
                                            </span>
                                        @endif
                                        @if($diff < 0)
                                            - Kurang dari order
                                        @else
                                            - Lebih dari order
                                        @endif
                                    </li>
                                @endif
                                @if($detail->condition_status !== 'good')
                                    <li>
                                        <strong>{{ $detail->material->material_name }}:</strong> 
                                        Kondisi <span class="text-red-700">{{ ucfirst($detail->condition_status) }}</span>
                                        - Tidak masuk stock
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </x-card>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <x-card class="text-center">
            <div class="text-xs text-gray-500 mb-1">Total Item</div>
            <div class="text-2xl font-bold text-blue-600">{{ $receipt->details->count() }}</div>
        </x-card>

        <x-card class="text-center">
            <div class="text-xs text-gray-500 mb-1">Total Qty Diterima</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($receipt->details->sum('qty_received'), 2) }}</div>
        </x-card>

        <x-card class="text-center">
            <div class="text-xs text-gray-500 mb-1">Item dengan Koreksi</div>
            <div class="text-2xl font-bold text-yellow-600">
                {{ $receipt->details->filter(fn($d) => $d->qty_received != $d->qty_ordered || $d->condition_status !== 'good')->count() }}
            </div>
        </x-card>

        <x-card class="text-center">
            <div class="text-xs text-gray-500 mb-1">Masuk Stock</div>
            <div class="text-2xl font-bold text-purple-600">
                {{ $receipt->details->filter(fn($d) => $d->condition_status === 'good')->count() }} items
            </div>
        </x-card>
    </div>

    <!-- Partial Receipt Progress (if applicable) -->
    @if($purchase && !$isFullyReceived)
        <x-card class="bg-orange-50 border-orange-200">
            <h3 class="text-md font-semibold text-orange-900 mb-3">📦 Status Penerimaan PO</h3>
            
            <div class="space-y-3">
                @foreach($purchase->details as $poDetail)
                    @php
                        $totalReceived = $purchase->goodsReceipts()
                            ->whereHas('details', function($q) use ($poDetail) {
                                $q->where('material_id', $poDetail->material_id);
                            })
                            ->get()
                            ->flatMap->details
                            ->where('material_id', $poDetail->material_id)
                            ->sum('qty_received');
                        
                        $remaining = $poDetail->qty_ordered - $totalReceived;
                        $percentage = ($totalReceived / $poDetail->qty_ordered) * 100;
                    @endphp
                    
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-900">
                                {{ $poDetail->material->material_code }} - {{ $poDetail->material->material_name }}
                            </span>
                            <span class="text-xs text-gray-600">
                                {{ number_format($totalReceived, 2) }} / {{ number_format($poDetail->qty_ordered, 2) }} {{ $poDetail->material->unit }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                        @if($remaining > 0)
                            <p class="text-xs text-orange-700 mt-1">
                                ⚠️ Sisa: <strong>{{ number_format($remaining, 2) }} {{ $poDetail->material->unit }}</strong> belum diterima
                            </p>
                        @else
                            <p class="text-xs text-green-700 mt-1">
                                ✓ Lengkap
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4 p-3 bg-white rounded border border-orange-300">
                <p class="text-sm text-orange-900">
                    💡 <strong>Info:</strong> Masih ada material yang belum lengkap diterima. 
                    Buat Goods Receipt baru untuk mencatat penerimaan sisanya.
                </p>
            </div>
        </x-card>
    @elseif($purchase && $isFullyReceived)
        <x-card class="bg-green-50 border-green-200">
            <div class="flex items-center p-2">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-green-900">✅ Purchase Order Lengkap</h4>
                    <p class="text-xs text-green-700">
                        Semua material dari PO {{ $purchase->purchase_number }} sudah diterima lengkap
                        @if($purchase->goodsReceipts->count() > 1)
                            melalui {{ $purchase->goodsReceipts->count() }} penerimaan bertahap
                        @endif
                    </p>
                </div>
            </div>
        </x-card>
    @endif
</div>

<script>
function toggleHistory(index) {
    const historyRow = document.getElementById('history-' + index);
    if (historyRow) {
        historyRow.classList.toggle('hidden');
    }
}
</script>
@endsection