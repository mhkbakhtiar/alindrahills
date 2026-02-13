{{-- resources/views/purchases/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Buat Purchase Order')
@section('breadcrumb', 'Material / Purchase / Create')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Buat Purchase Order</h2>
        <x-button variant="secondary" href="{{ route('purchases.index') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </x-button>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
        @csrf
        
        {{-- Header Information --}}
        <x-card>
            <h3 class="text-sm font-semibold mb-4">Informasi Purchase Order</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Purchase Request *</label>
                    <select name="request_id" id="requestSelect" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih Purchase Request --</option>
                        @foreach($approvedRequests as $req)
                            <option value="{{ $req->request_id }}" 
                                {{ old('request_id', $purchaseRequest?->request_id) == $req->request_id ? 'selected' : '' }}
                                data-details="{{ json_encode($req->details) }}">
                                {{ $req->request_number }} - {{ $req->requester->name ?? '' }} ({{ $req->request_date->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Purchase *</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Supplier *</label>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kontak Supplier</label>
                    <input type="text" name="supplier_contact" value="{{ old('supplier_contact') }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </x-card>

        {{-- Konfigurasi Jurnal Otomatis --}}
        <x-card>
            <h3 class="text-sm font-semibold mb-4">Konfigurasi Jurnal Otomatis</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Perkiraan Persediaan/Inventory *</label>
                    <select name="perkiraan_inventory" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih Perkiraan --</option>
                        @if($perkiraanInventory)
                            <option value="{{ $perkiraanInventory->kode_perkiraan }}" selected>
                                {{ $perkiraanInventory->kode_perkiraan }} - {{ $perkiraanInventory->nama_perkiraan }}
                            </option>
                        @endif
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Akun yang akan di-debet (bertambah)</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Perkiraan Hutang Usaha *</label>
                    <select name="perkiraan_hutang" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih Perkiraan --</option>
                        @if($perkiraanHutang)
                            <option value="{{ $perkiraanHutang->kode_perkiraan }}" selected>
                                {{ $perkiraanHutang->kode_perkiraan }} - {{ $perkiraanHutang->nama_perkiraan }}
                            </option>
                        @endif
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Akun yang akan di-kredit (bertambah)</p>
                </div>
            </div>
            <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs text-blue-800">
                    <strong>ℹ️ Info:</strong> Jurnal akan dibuat otomatis dengan status <strong>draft</strong>. 
                    Anda bisa me-review dan posting jurnal setelah Purchase Order dibuat.
                </p>
            </div>
        </x-card>

        {{-- Materials --}}
        <x-card>
            <h3 class="text-sm font-semibold mb-4">Detail Material</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs" id="materialsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-semibold">Material</th>
                            <th class="px-2 py-2 text-left font-semibold">Qty Diminta</th>
                            <th class="px-2 py-2 text-right font-semibold">Qty Order *</th>
                            <th class="px-2 py-2 text-right font-semibold">Harga Satuan (Rp) *</th>
                            <th class="px-2 py-2 text-right font-semibold">Subtotal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody id="materialsBody">
                        @if($purchaseRequest)
                            @foreach($purchaseRequest->details as $index => $detail)
                                <tr class="material-row border-b">
                                    <td class="px-2 py-2">
                                        <input type="hidden" name="materials[{{ $index }}][material_id]" value="{{ $detail->material_id }}">
                                        <span class="text-xs">{{ $detail->material->name ?? '' }}</span>
                                    </td>
                                    <td class="px-2 py-2 text-gray-600">
                                        {{ $detail->qty_requested }} {{ $detail->material->unit ?? '' }}
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" name="materials[{{ $index }}][qty_ordered]" 
                                            value="{{ old("materials.{$index}.qty_ordered", $detail->qty_requested) }}" 
                                            step="0.01" min="0.01" 
                                            class="w-full px-2 py-1 border rounded text-xs text-right qty-input" 
                                            onchange="calculateSubtotal({{ $index }})" required>
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" name="materials[{{ $index }}][unit_price]" 
                                            value="{{ old("materials.{$index}.unit_price", 0) }}" 
                                            step="0.01" min="0" 
                                            class="w-full px-2 py-1 border rounded text-xs text-right price-input" 
                                            onchange="calculateSubtotal({{ $index }})" required>
                                    </td>
                                    <td class="px-2 py-2 text-right subtotal-cell" id="subtotal-{{ $index }}">
                                        Rp 0
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-2 py-4 text-center text-gray-500">
                                    Pilih Purchase Request terlebih dahulu
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td colspan="4" class="px-2 py-2 text-right">TOTAL:</td>
                            <td class="px-2 py-2 text-right" id="totalAmount">Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>

        {{-- Action Buttons --}}
        <x-card>
            <div class="flex justify-end gap-2">
                <x-button type="button" variant="secondary" href="{{ route('purchases.index') }}">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Buat Purchase Order
                </x-button>
            </div>
        </x-card>
    </form>
</div>

<script>
    function calculateSubtotal(index) {
        const qtyInput = document.querySelector(`input[name="materials[${index}][qty_ordered]"]`);
        const priceInput = document.querySelector(`input[name="materials[${index}][unit_price]"]`);
        const subtotalCell = document.getElementById(`subtotal-${index}`);
        
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const subtotal = qty * price;
        
        subtotalCell.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.material-row').forEach((row, index) => {
            const qtyInput = row.querySelector('.qty-input');
            const priceInput = row.querySelector('.price-input');
            
            const qty = parseFloat(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            total += qty * price;
        });
        
        document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    // Load materials when PR is selected
    document.getElementById('requestSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const details = JSON.parse(selectedOption.getAttribute('data-details') || '[]');
        
        const tbody = document.getElementById('materialsBody');
        tbody.innerHTML = '';
        
        if (details.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-2 py-4 text-center text-gray-500">Tidak ada material</td></tr>';
            return;
        }
        
        details.forEach((detail, index) => {
            const row = document.createElement('tr');
            row.className = 'material-row border-b';
            row.innerHTML = `
                <td class="px-2 py-2">
                    <input type="hidden" name="materials[${index}][material_id]" value="${detail.material_id}">
                    <span class="text-xs">${detail.material.name}</span>
                </td>
                <td class="px-2 py-2 text-gray-600">
                    ${detail.qty_requested} ${detail.material.unit || ''}
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="materials[${index}][qty_ordered]" 
                        value="${detail.qty_requested}" 
                        step="0.01" min="0.01" 
                        class="w-full px-2 py-1 border rounded text-xs text-right qty-input" 
                        onchange="calculateSubtotal(${index})" required>
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="materials[${index}][unit_price]" 
                        value="0" 
                        step="0.01" min="0" 
                        class="w-full px-2 py-1 border rounded text-xs text-right price-input" 
                        onchange="calculateSubtotal(${index})" required>
                </td>
                <td class="px-2 py-2 text-right subtotal-cell" id="subtotal-${index}">
                    Rp 0
                </td>
            `;
            tbody.appendChild(row);
        });
        
        calculateTotal();
    });

    // Calculate on page load
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
    });
</script>

@endsection