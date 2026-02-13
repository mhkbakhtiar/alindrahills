@extends('layouts.app')

@section('title', 'Edit Pembelian')
@section('breadcrumb', 'Materials / Purchases / Edit')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Edit Pembelian Material</h2>
        <p class="text-sm text-gray-600">{{ $purchase->purchase_number }}</p>
    </div>

    @if($purchase->goodsReceipt)
        <x-card class="mb-4 bg-yellow-50 border-yellow-200">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm text-yellow-800">Pembelian ini tidak dapat diubah karena barang sudah diterima.</p>
            </div>
        </x-card>
    @endif

    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PATCH')
        
        <x-card class="mb-4">
            <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Pembelian</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Request</label>
                    <input type="text" value="{{ $purchase->purchaseRequest->request_number ?? '-' }}" readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembelian <span class="text-red-500">*</span></label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('purchase_date') border-red-500 @enderror" 
                        {{ $purchase->goodsReceipt ? 'disabled' : 'required' }}>
                    @error('purchase_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier <span class="text-red-500">*</span></label>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name', $purchase->supplier_name) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('supplier_name') border-red-500 @enderror" 
                        placeholder="PT. Supplier Material"
                        {{ $purchase->goodsReceipt ? 'disabled' : 'required' }}>
                    @error('supplier_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Supplier</label>
                    <input type="text" name="supplier_contact" value="{{ old('supplier_contact', $purchase->supplier_contact) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Telp/Email"
                        {{ $purchase->goodsReceipt ? 'disabled' : '' }}>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Catatan pembelian (opsional)"
                        {{ $purchase->goodsReceipt ? 'disabled' : '' }}>{{ old('notes', $purchase->notes) }}</textarea>
                </div>
            </div>
        </x-card>

        <x-card class="mb-4">
            <h3 class="text-md font-semibold text-gray-900 mb-4">Daftar Material</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Material</th>
                            <th class="px-3 py-2 text-center font-semibold">Satuan</th>
                            <th class="px-3 py-2 text-center font-semibold">Qty</th>
                            <th class="px-3 py-2 text-right font-semibold">Harga/Unit</th>
                            <th class="px-3 py-2 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="materialTableBody" class="divide-y divide-gray-200">
                        @foreach($purchase->details as $index => $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <span class="font-medium">{{ $detail->material->material_code }}</span> - {{ $detail->material->material_name }}
                                    <input type="hidden" name="materials[{{ $index }}][material_id]" value="{{ $detail->material_id }}">
                                </td>
                                <td class="px-3 py-2 text-center">{{ $detail->material->unit }}</td>
                                <td class="px-3 py-2">
                                    <input type="number" name="materials[{{ $index }}][qty_ordered]" 
                                        step="0.01" min="0.01" value="{{ $detail->qty_ordered }}"
                                        class="w-24 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:ring-blue-500 focus:border-blue-500" 
                                        {{ $purchase->goodsReceipt ? 'disabled' : 'required' }}
                                        onchange="calculateSubtotal({{ $index }})">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="materials[{{ $index }}][unit_price]" 
                                        step="0.01" min="0" value="{{ $detail->unit_price }}"
                                        class="w-32 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:ring-blue-500 focus:border-blue-500" 
                                        {{ $purchase->goodsReceipt ? 'disabled' : 'required' }}
                                        onchange="calculateSubtotal({{ $index }})">
                                </td>
                                <td class="px-3 py-2 text-right font-bold">
                                    <span id="subtotal-{{ $index }}">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-3 py-3 text-right font-bold text-gray-900">TOTAL:</td>
                            <td class="px-3 py-3 text-right font-bold text-gray-900">
                                <span id="grandTotal">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>

        <div class="flex gap-2">
            @if(!$purchase->goodsReceipt)
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan
                </button>
            @endif
            <a href="{{ route('purchases.show', $purchase) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md">
                Kembali
            </a>
        </div>
    </form>
</div>

@if(!$purchase->goodsReceipt)
<script>
function calculateSubtotal(index) {
    const qtyInput = document.querySelector(`input[name="materials[${index}][qty_ordered]"]`);
    const priceInput = document.querySelector(`input[name="materials[${index}][unit_price]"]`);
    const subtotalSpan = document.getElementById(`subtotal-${index}`);
    
    const qty = parseFloat(qtyInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    const subtotal = qty * price;
    
    subtotalSpan.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    
    updateGrandTotal();
}

function updateGrandTotal() {
    let total = 0;
    const tbody = document.getElementById('materialTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    rows.forEach((row, index) => {
        const qtyInput = row.querySelector(`input[name="materials[${index}][qty_ordered]"]`);
        const priceInput = row.querySelector(`input[name="materials[${index}][unit_price]"]`);
        
        if (qtyInput && priceInput) {
            const qty = parseFloat(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            total += qty * price;
        }
    });
    
    document.getElementById('grandTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endif
@endsection