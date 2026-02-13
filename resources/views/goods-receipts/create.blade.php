@extends('layouts.app')

@section('title', 'Terima Barang')
@section('breadcrumb', 'Materials / Goods Receipts / Create')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Penerimaan Barang (Goods Receipt)</h2>
        <p class="text-sm text-gray-600">Catat penerimaan barang dari supplier (bisa bertahap/partial)</p>
    </div>

    <form action="{{ route('goods-receipts.store') }}" method="POST" id="receiptForm">
        @csrf
        
        <!-- Purchase Selection -->
        <x-card class="mb-4">
            <h3 class="text-md font-semibold text-gray-900 mb-4">Pilih Purchase Order</h3>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Order <span class="text-red-500">*</span></label>
                <select name="purchase_id" id="purchaseSelect" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('purchase_id') border-red-500 @enderror" 
                    required onchange="loadPurchaseDetails(this.value)">
                    <option value="">Pilih Purchase Order</option>
                    @foreach($pendingPurchases as $p)
                        <option value="{{ $p->purchase_id }}" 
                            {{ $purchase && $purchase->purchase_id == $p->purchase_id ? 'selected' : '' }}>
                            {{ $p->purchase_number }} - {{ $p->supplier_name }} ({{ $p->details->count() }} items)
                        </option>
                    @endforeach
                </select>
                @error('purchase_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">
                    💡 Tip: Jika barang datang bertahap, terima dulu yang sudah datang. Sisanya bisa dibuat receipt baru nanti.
                </p>
            </div>
        </x-card>

        <!-- Receipt Information -->
        <x-card class="mb-4">
            <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Penerimaan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terima <span class="text-red-500">*</span></label>
                    <input type="date" name="receipt_date" value="{{ old('receipt_date', date('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('receipt_date') border-red-500 @enderror" required>
                    @error('receipt_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warehouse Tujuan <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('warehouse_id') border-red-500 @enderror" required>
                        <option value="">Pilih Warehouse</option>
                        @foreach(\App\Models\Warehouse::where('is_active', true)->get() as $warehouse)
                            <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->warehouse_name }}</option>
                        @endforeach
                    </select>
                    @error('warehouse_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Penerimaan</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="receipt_type" value="full" checked class="mr-2" onchange="updateReceiptType('full')">
                            <span class="text-sm">Lengkap (semua sisa barang datang)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="receipt_type" value="partial" class="mr-2" onchange="updateReceiptType('partial')">
                            <span class="text-sm">Partial (sebagian dulu, sisanya menyusul)</span>
                        </label>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Contoh: Barang diterima 70 dari 100. Sisanya 30 menyusul besok.">{{ old('notes') }}</textarea>
                </div>
            </div>
        </x-card>

        <!-- Materials List -->
        <x-card class="mb-4">
            <h3 class="text-md font-semibold text-gray-900 mb-4">Daftar Material yang Diterima</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Material</th>
                            <th class="px-3 py-2 text-center font-semibold">Satuan</th>
                            <th class="px-3 py-2 text-center font-semibold">Total PO</th>
                            <th class="px-3 py-2 text-center font-semibold">Sudah Diterima</th>
                            <th class="px-3 py-2 text-center font-semibold">Sisa Belum Diterima</th>
                            <th class="px-3 py-2 text-center font-semibold">Terima Sekarang <span class="text-red-500">*</span></th>
                            <th class="px-3 py-2 text-right font-semibold">Harga/Unit</th>
                            <th class="px-3 py-2 text-center font-semibold">Kondisi <span class="text-red-500">*</span></th>
                            <th class="px-3 py-2 text-left font-semibold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="materialTableBody" class="divide-y divide-gray-200">
                        @if($purchase)
                            @foreach($purchase->details as $index => $detail)
                                @php
                                    // Hitung qty yang sudah diterima dari receipt sebelumnya
                                    $totalReceived = $purchase->goodsReceipts()
                                        ->whereHas('details', function($q) use ($detail) {
                                            $q->where('material_id', $detail->material_id);
                                        })
                                        ->get()
                                        ->flatMap->details
                                        ->where('material_id', $detail->material_id)
                                        ->sum('qty_received');
                                    
                                    // SISA = Total PO - Sudah Diterima
                                    $remaining = $detail->qty_ordered - $totalReceived;
                                @endphp
                                <tr class="hover:bg-gray-50" data-remaining="{{ $remaining }}" data-total-po="{{ $detail->qty_ordered }}">
                                    <td class="px-3 py-2">
                                        <span class="font-medium">{{ $detail->material->material_code }}</span> - {{ $detail->material->material_name }}
                                        <input type="hidden" name="materials[{{ $index }}][material_id]" value="{{ $detail->material_id }}">
                                    </td>
                                    <td class="px-3 py-2 text-center">{{ $detail->material->unit }}</td>
                                    
                                    <!-- Total PO (tidak berubah) -->
                                    <td class="px-3 py-2 text-center text-gray-600">
                                        {{ number_format($detail->qty_ordered, 2) }}
                                    </td>
                                    
                                    <!-- Sudah Diterima (dari receipts sebelumnya) -->
                                    <td class="px-3 py-2 text-center">
                                        <span class="text-blue-600 font-semibold">
                                            {{ number_format($totalReceived, 2) }}
                                        </span>
                                    </td>
                                    
                                    <!-- SISA yang belum diterima -->
                                    <td class="px-3 py-2 text-center" id="remaining-{{ $index }}">
                                        <span class="font-bold {{ $remaining > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                            {{ number_format($remaining, 2) }}
                                        </span>
                                        <input type="hidden" id="remaining-value-{{ $index }}" value="{{ $remaining }}">
                                    </td>
                                    
                                    <!-- Input: Terima Sekarang -->
                                    <td class="px-3 py-2">
                                        <input type="number" name="materials[{{ $index }}][qty_received]" 
                                            step="0.01" min="0" max="{{ $remaining }}" value="{{ $remaining }}"
                                            class="w-24 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:ring-blue-500 focus:border-blue-500" 
                                            required onchange="validateQtyReceived({{ $index }})"
                                            data-index="{{ $index }}">
                                        <!-- Hidden: qty_ordered untuk validasi backend -->
                                        <input type="hidden" name="materials[{{ $index }}][qty_ordered]" value="{{ $detail->qty_ordered }}">
                                        <input type="hidden" name="materials[{{ $index }}][unit_price]" value="{{ $detail->unit_price }}">
                                        <div id="error-{{ $index }}" class="text-xs text-red-600 mt-1 hidden"></div>
                                    </td>
                                    
                                    <td class="px-3 py-2 text-right text-gray-600">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                                    
                                    <td class="px-3 py-2">
                                        <select name="materials[{{ $index }}][condition_status]" 
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:ring-blue-500 focus:border-blue-500" required>
                                            <option value="good">Baik</option>
                                            <option value="damaged">Rusak</option>
                                            <option value="incomplete">Tidak Lengkap</option>
                                        </select>
                                    </td>
                                    
                                    <td class="px-3 py-2">
                                        <input type="text" name="materials[{{ $index }}][notes]" 
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:ring-blue-500 focus:border-blue-500" 
                                            placeholder="Catatan">
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr id="emptyState">
                                <td colspan="9" class="px-3 py-8 text-center text-gray-500">
                                    Pilih Purchase Order terlebih dahulu
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Info Boxes -->
            <div class="mt-4 space-y-3">
                <!-- Partial Receipt Info -->
                <div id="partialInfo" class="p-3 bg-blue-50 border border-blue-200 rounded-md hidden">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-blue-800">
                            <strong>Penerimaan Bertahap (Partial Receipt)</strong>
                            <p class="mt-1">Kolom <strong>"Sisa Belum Diterima"</strong> menunjukkan sisa dari Total PO dikurangi yang sudah diterima sebelumnya. Anda bisa terima sebagian dulu, sisanya buat receipt baru lagi nanti.</p>
                        </div>
                    </div>
                </div>

                <!-- Correction Warning -->
                <div id="correctionWarning" class="p-3 bg-yellow-50 border border-yellow-200 rounded-md hidden">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="text-sm text-yellow-800">
                            <strong>Ada Koreksi!</strong>
                            <p class="mt-1">Qty yang diterima tidak sama dengan sisa yang tersedia, atau ada kondisi rusak/tidak lengkap. Status akan otomatis menjadi "Corrected".</p>
                        </div>
                    </div>
                </div>

                <!-- Over Receipt Warning -->
                <div id="overWarning" class="p-3 bg-red-50 border border-red-200 rounded-md hidden">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-red-800">
                            <strong>Peringatan!</strong>
                            <p class="mt-1">Ada material yang qty-nya melebihi sisa yang belum diterima. Silakan perbaiki.</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Penerimaan
            </button>
            <a href="{{ route('goods-receipts.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function loadPurchaseDetails(purchaseId) {
    if (!purchaseId) {
        document.getElementById('materialTableBody').innerHTML = '<tr id="emptyState"><td colspan="9" class="px-3 py-8 text-center text-gray-500">Pilih Purchase Order terlebih dahulu</td></tr>';
        return;
    }
    window.location.href = "{{ route('goods-receipts.create') }}?purchase_id=" + purchaseId;
}

function updateReceiptType(type) {
    const partialInfo = document.getElementById('partialInfo');
    if (type === 'partial') {
        partialInfo.classList.remove('hidden');
    } else {
        partialInfo.classList.add('hidden');
        // Set all qty to remaining
        document.querySelectorAll('input[name*="[qty_received]"]').forEach(input => {
            const index = input.dataset.index;
            const remaining = parseFloat(document.getElementById(`remaining-value-${index}`).value);
            input.value = remaining;
            validateQtyReceived(index);
        });
    }
}

function validateQtyReceived(index) {
    const input = document.querySelector(`input[name="materials[${index}][qty_received]"]`);
    const errorDiv = document.getElementById(`error-${index}`);
    const qtyReceived = parseFloat(input.value) || 0;
    
    // Ambil remaining dari hidden input (bukan dari qty_ordered!)
    const remaining = parseFloat(document.getElementById(`remaining-value-${index}`).value);
    
    // Check if over remaining (bukan over qty_ordered)
    if (qtyReceived > remaining) {
        errorDiv.textContent = `Maks sisa: ${remaining.toFixed(2)}`;
        errorDiv.classList.remove('hidden');
        input.classList.add('border-red-500');
        document.getElementById('overWarning').classList.remove('hidden');
        return false;
    } else {
        errorDiv.classList.add('hidden');
        input.classList.remove('border-red-500');
        
        // Check if all valid
        let hasError = false;
        document.querySelectorAll('[id^="error-"]').forEach(el => {
            if (!el.classList.contains('hidden')) {
                hasError = true;
            }
        });
        
        if (!hasError) {
            document.getElementById('overWarning').classList.add('hidden');
        }
    }
    
    // Check correction (bandingkan dengan remaining, bukan qty_ordered)
    checkCorrection();
    return true;
}

function checkCorrection() {
    let hasCorrection = false;
    
    document.querySelectorAll('input[name*="[qty_received]"]').forEach(input => {
        const index = input.dataset.index;
        const remaining = parseFloat(document.getElementById(`remaining-value-${index}`).value);
        const qtyReceived = parseFloat(input.value) || 0;
        
        // Ada koreksi jika terima tidak sama dengan sisa yang tersedia
        if (qtyReceived !== remaining && qtyReceived > 0) {
            hasCorrection = true;
        }
    });
    
    const correctionWarning = document.getElementById('correctionWarning');
    if (hasCorrection) {
        correctionWarning.classList.remove('hidden');
    } else {
        correctionWarning.classList.add('hidden');
    }
}

// Form validation
document.getElementById('receiptForm').addEventListener('submit', function(e) {
    const purchaseSelect = document.getElementById('purchaseSelect');
    if (!purchaseSelect.value) {
        e.preventDefault();
        alert('Pilih Purchase Order terlebih dahulu');
        return false;
    }
    
    // Check for over receipt
    let hasError = false;
    document.querySelectorAll('[id^="error-"]').forEach(el => {
        if (!el.classList.contains('hidden')) {
            hasError = true;
        }
    });
    
    if (hasError) {
        e.preventDefault();
        alert('Ada material yang qty-nya melebihi sisa. Silakan perbaiki.');
        return false;
    }
    
    // Check if any qty received
    let hasQty = false;
    document.querySelectorAll('input[name*="[qty_received]"]').forEach(input => {
        if (parseFloat(input.value) > 0) {
            hasQty = true;
        }
    });
    
    if (!hasQty) {
        e.preventDefault();
        alert('Minimal harus ada 1 material yang diterima');
        return false;
    }
});

// Initial check on load
document.addEventListener('DOMContentLoaded', function() {
    checkCorrection();
    // Show partial info by default if there's previous receipts
    @if($purchase)
        @foreach($purchase->details as $index => $detail)
            @php
                $totalReceived = $purchase->goodsReceipts()
                    ->whereHas('details', function($q) use ($detail) {
                        $q->where('material_id', $detail->material_id);
                    })
                    ->get()
                    ->flatMap->details
                    ->where('material_id', $detail->material_id)
                    ->sum('qty_received');
            @endphp
            @if($totalReceived > 0)
                document.getElementById('partialInfo').classList.remove('hidden');
                @break
            @endif
        @endforeach
    @endif
});
</script>
@endsection