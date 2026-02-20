{{-- resources/views/purchases/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Purchase Order')
@section('breadcrumb', 'Material / Purchase Orders / Edit')

@section('content')
<div class="space-y-4">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Edit Purchase Order</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $purchase->purchase_number }}</p>
        </div>
        <a href="{{ route('purchases.show', $purchase) }}"
            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- ── Warning jika sudah ada GR ───────────────────────────────────────── --}}
    @if($purchase->goodsReceipt)
        <div class="bg-yellow-50 border border-yellow-300 rounded-lg px-4 py-3 flex items-start gap-2 text-sm text-yellow-800">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Purchase Order ini <strong>tidak dapat diubah</strong> karena barang sudah diterima (Goods Receipt sudah dibuat).</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <p class="font-semibold mb-1">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PATCH')

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- Informasi PO                                                       --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">Informasi Purchase Order</h3>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Purchase Request</label>
                    <input type="text"
                        value="{{ $purchase->purchaseRequest->request_number ?? '-' }}"
                        readonly
                        class="w-full px-3 py-2 text-xs border rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Tanggal Purchase <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="purchase_date"
                        value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('purchase_date') border-red-400 @enderror"
                        {{ $purchase->goodsReceipt ? 'disabled' : 'required' }}>
                    @error('purchase_date')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Nama Supplier <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="supplier_name"
                        value="{{ old('supplier_name', $purchase->supplier_name) }}"
                        placeholder="PT. Sumber Makmur"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('supplier_name') border-red-400 @enderror"
                        {{ $purchase->goodsReceipt ? 'disabled' : 'required' }}>
                    @error('supplier_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kontak Supplier</label>
                    <input type="text" name="supplier_contact"
                        value="{{ old('supplier_contact', $purchase->supplier_contact) }}"
                        placeholder="Telp / Email"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        {{ $purchase->goodsReceipt ? 'disabled' : '' }}>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Catatan tambahan (opsional)"
                        {{ $purchase->goodsReceipt ? 'disabled' : '' }}>{{ old('notes', $purchase->notes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- Metode Pembayaran                                                  --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">Metode Pembayaran</h3>
            </div>
            <div class="p-4">

                @if($purchase->goodsReceipt)
                    {{-- Readonly display jika sudah ada GR --}}
                    <div class="flex gap-3">
                        <div class="flex-1 border-2 rounded-xl p-4 text-center
                            {{ $purchase->payment_type === 'cash' ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-gray-50 opacity-50' }}">
                            <div class="text-2xl mb-1">💵</div>
                            <p class="text-xs font-bold text-gray-800">Cash / Tunai</p>
                        </div>
                        <div class="flex-1 border-2 rounded-xl p-4 text-center
                            {{ $purchase->payment_type === 'tempo' ? 'border-orange-500 bg-orange-50' : 'border-gray-200 bg-gray-50 opacity-50' }}">
                            <div class="text-2xl mb-1">📅</div>
                            <p class="text-xs font-bold text-gray-800">Tempo / Kredit</p>
                        </div>
                    </div>
                    {{-- Hidden input to keep value --}}
                    <input type="hidden" name="payment_type" value="{{ $purchase->payment_type }}">
                    @if($purchase->tempo_date)
                        <input type="hidden" name="tempo_date" value="{{ $purchase->tempo_date->format('Y-m-d') }}">
                    @endif
                @else
                    {{-- Editable toggle --}}
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_type" value="cash" id="pay_cash"
                                class="sr-only peer"
                                {{ old('payment_type', $purchase->payment_type) === 'cash' ? 'checked' : '' }}>
                            <div class="border-2 rounded-xl p-4 text-center transition-all
                                border-gray-200 hover:border-green-300
                                peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-sm">
                                <div class="text-2xl mb-1.5">💵</div>
                                <p class="text-xs font-bold text-gray-800">Cash / Tunai</p>
                                <p class="text-xs text-gray-500 mt-0.5">Bayar langsung saat PO dibuat</p>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_type" value="tempo" id="pay_tempo"
                                class="sr-only peer"
                                {{ old('payment_type', $purchase->payment_type) === 'tempo' ? 'checked' : '' }}>
                            <div class="border-2 rounded-xl p-4 text-center transition-all
                                border-gray-200 hover:border-orange-300
                                peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:shadow-sm">
                                <div class="text-2xl mb-1.5">📅</div>
                                <p class="text-xs font-bold text-gray-800">Tempo / Kredit</p>
                                <p class="text-xs text-gray-500 mt-0.5">Bayar di tanggal jatuh tempo</p>
                            </div>
                        </label>
                    </div>

                    {{-- Tempo date --}}
                    <div id="tempoSection"
                        class="{{ old('payment_type', $purchase->payment_type) === 'tempo' ? '' : 'hidden' }} bg-orange-50 border border-orange-200 rounded-lg p-3 mb-4">
                        <label class="block text-xs font-semibold text-orange-800 mb-1">
                            Tanggal Jatuh Tempo <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tempo_date" id="tempoDate"
                            value="{{ old('tempo_date', $purchase->tempo_date?->format('Y-m-d')) }}"
                            class="px-3 py-2 text-xs border border-orange-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                        @error('tempo_date')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-orange-600 mt-1.5">
                            ⚠️ Tanggal ini dicantumkan di keterangan jurnal hutang usaha.
                        </p>
                    </div>
                @endif

                @if($purchase->jurnal && $purchase->jurnal->status === 'posted')
                    <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2.5 text-xs text-yellow-800">
                        ⚠️ Jurnal sudah <strong>Posted</strong>. Perubahan metode pembayaran tidak akan memperbarui jurnal yang sudah posted.
                        Lakukan Void jurnal terlebih dahulu jika perlu mengubah akun.
                    </div>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- Detail Material                                                    --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">Detail Material</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Material</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Satuan</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">
                                Qty <span class="text-red-500">*</span>
                            </th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">
                                Harga/Unit (Rp) <span class="text-red-500">*</span>
                            </th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Subtotal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody id="materialTableBody" class="divide-y divide-gray-100">
                        @foreach($purchase->details as $index => $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <input type="hidden"
                                        name="materials[{{ $index }}][material_id]"
                                        value="{{ $detail->material_id }}">
                                    <p class="font-medium text-gray-800">{{ $detail->material->material_name }}</p>
                                    <p class="text-gray-400 mt-0.5">{{ $detail->material->material_code }}</p>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-500">
                                    {{ $detail->material->unit }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number"
                                        name="materials[{{ $index }}][qty_ordered]"
                                        value="{{ old("materials.{$index}.qty_ordered", $detail->qty_ordered) }}"
                                        step="0.01" min="0.01"
                                        class="w-28 px-2 py-1.5 border rounded-lg text-xs text-center focus:outline-none focus:ring-2 focus:ring-blue-400 qty-input"
                                        onchange="calculateSubtotal({{ $index }})"
                                        {{ $purchase->goodsReceipt ? 'disabled' : 'required' }}>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <input type="number"
                                        name="materials[{{ $index }}][unit_price]"
                                        value="{{ old("materials.{$index}.unit_price", $detail->unit_price) }}"
                                        step="0.01" min="0"
                                        class="w-36 px-2 py-1.5 border rounded-lg text-xs text-right focus:outline-none focus:ring-2 focus:ring-blue-400 price-input"
                                        onchange="calculateSubtotal({{ $index }})"
                                        {{ $purchase->goodsReceipt ? 'disabled' : 'required' }}>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800"
                                    id="subtotal-{{ $index }}">
                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-700 text-sm">
                                TOTAL:
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900 text-sm" id="grandTotal">
                                Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ── Action Buttons ───────────────────────────────────────────────── --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('purchases.show', $purchase) }}"
                class="inline-flex items-center px-4 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                Batal
            </a>
            @if(!$purchase->goodsReceipt)
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            @endif
        </div>

    </form>
</div>

@if(!$purchase->goodsReceipt)
<script>
    // ── Payment type toggle ────────────────────────────────────────────────────
    function handlePaymentType(val) {
        const tempoSection = document.getElementById('tempoSection');
        const tempoDate    = document.getElementById('tempoDate');

        if (val === 'tempo') {
            tempoSection.classList.remove('hidden');
            tempoDate.required = true;
        } else {
            tempoSection.classList.add('hidden');
            tempoDate.required = false;
        }
    }

    document.querySelectorAll('input[name="payment_type"]').forEach(r => {
        r.addEventListener('change', () => handlePaymentType(r.value));
    });

    // ── Subtotal per row ──────────────────────────────────────────────────────
    function calculateSubtotal(index) {
        const qty   = parseFloat(document.querySelector(`input[name="materials[${index}][qty_ordered]"]`)?.value) || 0;
        const price = parseFloat(document.querySelector(`input[name="materials[${index}][unit_price]"]`)?.value) || 0;
        const cell  = document.getElementById(`subtotal-${index}`);
        if (cell) cell.textContent = 'Rp ' + (qty * price).toLocaleString('id-ID');
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('#materialTableBody tr').forEach((row, index) => {
            const qty   = parseFloat(row.querySelector('.qty-input')?.value) || 0;
            const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
            total += qty * price;
        });
        document.getElementById('grandTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    // ── Init ───────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const checked = document.querySelector('input[name="payment_type"]:checked');
        if (checked) handlePaymentType(checked.value);
    });
</script>
@endif
@endsection