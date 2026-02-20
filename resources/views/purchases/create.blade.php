{{-- resources/views/purchases/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Buat Purchase Order')
@section('breadcrumb', 'Material / Purchase Orders / Create')

@section('content')
<div class="space-y-4">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Buat Purchase Order</h2>
            <p class="text-xs text-gray-500 mt-0.5">Jurnal akuntansi dibuat otomatis sesuai metode pembayaran</p>
        </div>
        <a href="{{ route('purchases.index') }}"
            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- ── Alerts ───────────────────────────────────────────────────────────── --}}
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
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

    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
        @csrf

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- STEP 1 — Informasi PO                                             --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold flex-shrink-0">1</span>
                <h3 class="text-sm font-semibold text-gray-800">Informasi Purchase Order</h3>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Purchase Request <span class="text-red-500">*</span>
                    </label>
                    <select name="request_id" id="requestSelect"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('request_id') border-red-400 @enderror"
                        required>
                        <option value="">-- Pilih Purchase Request --</option>
                        @foreach($approvedRequests as $req)
                            <option value="{{ $req->request_id }}"
                                {{ old('request_id', $purchaseRequest?->request_id) == $req->request_id ? 'selected' : '' }}
                                data-details="{{ json_encode($req->details) }}">
                                {{ $req->request_number }} — {{ $req->requester->name ?? '' }}
                                ({{ $req->request_date->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('request_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Tanggal Purchase <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="purchase_date"
                        value="{{ old('purchase_date', date('Y-m-d')) }}"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('purchase_date') border-red-400 @enderror"
                        required>
                    @error('purchase_date')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Nama Supplier <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="supplier_name"
                        value="{{ old('supplier_name') }}"
                        placeholder="PT. Sumber Makmur"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('supplier_name') border-red-400 @enderror"
                        required>
                    @error('supplier_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kontak Supplier</label>
                    <input type="text" name="supplier_contact"
                        value="{{ old('supplier_contact') }}"
                        placeholder="Telp / Email"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2"
                        placeholder="Catatan tambahan (opsional)"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- STEP 2 — Metode Pembayaran                                        --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold flex-shrink-0">2</span>
                <h3 class="text-sm font-semibold text-gray-800">Metode Pembayaran</h3>
            </div>
            <div class="p-4">

                {{-- Toggle Cash / Tempo --}}
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_type" value="cash" id="pay_cash"
                            class="sr-only peer"
                            {{ old('payment_type', 'cash') === 'cash' ? 'checked' : '' }}>
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
                            {{ old('payment_type') === 'tempo' ? 'checked' : '' }}>
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
                    class="{{ old('payment_type') === 'tempo' ? '' : 'hidden' }} bg-orange-50 border border-orange-200 rounded-lg p-3 mb-4">
                    <label class="block text-xs font-semibold text-orange-800 mb-1">
                        Tanggal Jatuh Tempo <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tempo_date" id="tempoDate"
                        value="{{ old('tempo_date') }}"
                        class="px-3 py-2 text-xs border border-orange-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                    @error('tempo_date')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-orange-600 mt-1.5">
                        ⚠️ Tanggal ini dicantumkan di keterangan jurnal hutang usaha.
                    </p>
                </div>

                {{-- Jurnal Preview Cash --}}
                <div id="previewCash" class="{{ old('payment_type') === 'tempo' ? 'hidden' : '' }}">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <p class="text-xs font-semibold text-green-800 mb-2">📋 Jurnal otomatis yang akan dibuat:</p>
                        <div class="font-mono text-xs space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Debet — Persediaan/Inventory</span>
                                <span id="cashDebet" class="font-bold text-green-700">Rp 0</span>
                            </div>
                            <div class="border-t border-green-200 my-1"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Kredit — Kas / Bank</span>
                                <span id="cashKredit" class="font-bold text-red-600">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Jurnal Preview Tempo --}}
                <div id="previewTempo" class="{{ old('payment_type') === 'tempo' ? '' : 'hidden' }}">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                        <p class="text-xs font-semibold text-orange-800 mb-2">📋 Jurnal otomatis yang akan dibuat:</p>
                        <div class="font-mono text-xs space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Debet — Persediaan/Inventory</span>
                                <span id="tempoDebet" class="font-bold text-green-700">Rp 0</span>
                            </div>
                            <div class="border-t border-orange-200 my-1"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Kredit — Hutang Usaha</span>
                                <span id="tempoKredit" class="font-bold text-red-600">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- STEP 3 — Akun Jurnal                                              --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold flex-shrink-0">3</span>
                <h3 class="text-sm font-semibold text-gray-800">Konfigurasi Akun Jurnal</h3>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Akun Persediaan / Inventory
                        <span class="text-xs text-green-600 font-semibold ml-1">(DEBET)</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="perkiraan_inventory"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('perkiraan_inventory') border-red-400 @enderror"
                        required>
                        <option value="">-- Pilih Perkiraan --</option>
                        @foreach($perkiraanList as $p)
                            <option value="{{ $p->kode_perkiraan }}"
                                {{ old('perkiraan_inventory') == $p->kode_perkiraan ? 'selected' : '' }}>
                                {{ $p->kode_perkiraan }} — {{ $p->nama_perkiraan }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Akun yang bertambah saat barang dibeli</p>
                    @error('perkiraan_inventory')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label id="labelPerkiraanBayar" class="block text-xs font-medium text-gray-700 mb-1">
                        Akun Kas / Bank
                        <span class="text-xs text-red-600 font-semibold ml-1">(KREDIT)</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="perkiraan_bayar" id="selectPerkiraanBayar"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('perkiraan_bayar') border-red-400 @enderror"
                        required>
                        <option value="">-- Pilih Perkiraan --</option>
                        @foreach($perkiraanList as $p)
                            <option value="{{ $p->kode_perkiraan }}"
                                {{ old('perkiraan_bayar') == $p->kode_perkiraan ? 'selected' : '' }}>
                                {{ $p->kode_perkiraan }} — {{ $p->nama_perkiraan }}
                            </option>
                        @endforeach
                    </select>
                    <p id="hintPerkiraanBayar" class="text-xs text-gray-400 mt-1">
                        Akun kas/bank yang berkurang saat bayar
                    </p>
                    @error('perkiraan_bayar')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2.5 text-xs text-blue-800">
                    <strong>ℹ️</strong> Jurnal dibuat dengan status <strong>Draft</strong>.
                    Posting jurnal dari halaman Detail Purchase Order setelah review.
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- STEP 4 — Detail Material                                          --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold flex-shrink-0">4</span>
                <h3 class="text-sm font-semibold text-gray-800">Detail Material</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs" id="materialsTable">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Material</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Qty Diminta</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">
                                Qty Order <span class="text-red-500">*</span>
                            </th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">
                                Harga/Unit (Rp) <span class="text-red-500">*</span>
                            </th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Subtotal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody id="materialsBody" class="divide-y divide-gray-100">
                        @if($purchaseRequest)
                            @foreach($purchaseRequest->details as $index => $detail)
                                <tr class="material-row hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <input type="hidden"
                                            name="materials[{{ $index }}][material_id]"
                                            value="{{ $detail->material_id }}">
                                        <p class="font-medium text-gray-800">{{ $detail->material->name ?? '' }}</p>
                                        <p class="text-gray-400 mt-0.5">{{ $detail->material->material_code ?? '' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-500">
                                        {{ $detail->qty_requested }} {{ $detail->material->unit ?? '' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number"
                                            name="materials[{{ $index }}][qty_ordered]"
                                            value="{{ old("materials.{$index}.qty_ordered", $detail->qty_requested) }}"
                                            step="0.01" min="0.01"
                                            class="w-28 px-2 py-1.5 border rounded-lg text-xs text-center qty-input focus:outline-none focus:ring-2 focus:ring-blue-400"
                                            onchange="calculateSubtotal({{ $index }})"
                                            required>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <input type="number"
                                            name="materials[{{ $index }}][unit_price]"
                                            value="{{ old("materials.{$index}.unit_price", 0) }}"
                                            step="0.01" min="0"
                                            class="w-36 px-2 py-1.5 border rounded-lg text-xs text-right price-input focus:outline-none focus:ring-2 focus:ring-blue-400"
                                            onchange="calculateSubtotal({{ $index }})"
                                            required>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-800 subtotal-cell"
                                        id="subtotal-{{ $index }}">
                                        Rp 0
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr id="emptyRow">
                                <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm">Pilih Purchase Request untuk menampilkan material</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot class="border-t-2 bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-700 text-sm">
                                TOTAL PEMBELIAN:
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900 text-sm" id="totalAmount">
                                Rp 0
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ── Action Buttons ───────────────────────────────────────────────── --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('purchases.index') }}"
                class="inline-flex items-center px-4 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Buat Purchase Order
            </button>
        </div>

    </form>
</div>

<script>
    // ── Payment type toggle ────────────────────────────────────────────────────
    function handlePaymentType(val) {
        const tempoSection = document.getElementById('tempoSection');
        const tempoDate    = document.getElementById('tempoDate');
        const previewCash  = document.getElementById('previewCash');
        const previewTempo = document.getElementById('previewTempo');
        const labelBayar   = document.getElementById('labelPerkiraanBayar');
        const hintBayar    = document.getElementById('hintPerkiraanBayar');

        if (val === 'tempo') {
            tempoSection.classList.remove('hidden');
            tempoDate.required = true;
            previewCash.classList.add('hidden');
            previewTempo.classList.remove('hidden');
            labelBayar.innerHTML = `Akun Hutang Usaha
                <span class="text-xs text-red-600 font-semibold ml-1">(KREDIT)</span>
                <span class="text-red-500">*</span>`;
            hintBayar.textContent = 'Akun hutang yang bertambah saat beli tempo';
        } else {
            tempoSection.classList.add('hidden');
            tempoDate.required = false;
            previewCash.classList.remove('hidden');
            previewTempo.classList.add('hidden');
            labelBayar.innerHTML = `Akun Kas / Bank
                <span class="text-xs text-red-600 font-semibold ml-1">(KREDIT)</span>
                <span class="text-red-500">*</span>`;
            hintBayar.textContent = 'Akun kas/bank yang berkurang saat bayar';
        }
        updatePreview();
    }

    document.querySelectorAll('input[name="payment_type"]').forEach(r => {
        r.addEventListener('change', () => handlePaymentType(r.value));
    });

    // ── Subtotal per row ──────────────────────────────────────────────────────
    function calculateSubtotal(index) {
        const qty      = parseFloat(document.querySelector(`input[name="materials[${index}][qty_ordered]"]`)?.value) || 0;
        const price    = parseFloat(document.querySelector(`input[name="materials[${index}][unit_price]"]`)?.value) || 0;
        const cell     = document.getElementById(`subtotal-${index}`);
        if (cell) cell.textContent = 'Rp ' + (qty * price).toLocaleString('id-ID');
        calculateTotal();
    }

    function getTotal() {
        let total = 0;
        document.querySelectorAll('.material-row').forEach(row => {
            const qty   = parseFloat(row.querySelector('.qty-input')?.value) || 0;
            const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
            total += qty * price;
        });
        return total;
    }

    function calculateTotal() {
        const total = getTotal();
        document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');
        updatePreview(total);
    }

    function updatePreview(total = null) {
        if (total === null) total = getTotal();
        const fmt = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('cashDebet').textContent   = fmt;
        document.getElementById('cashKredit').textContent  = fmt;
        document.getElementById('tempoDebet').textContent  = fmt;
        document.getElementById('tempoKredit').textContent = fmt;
    }

    // ── Load materials from PR ─────────────────────────────────────────────────
    document.getElementById('requestSelect').addEventListener('change', function () {
        const details = JSON.parse(this.options[this.selectedIndex].getAttribute('data-details') || '[]');
        const tbody   = document.getElementById('materialsBody');
        tbody.innerHTML = '';

        if (!details.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm">
                Tidak ada material dalam request ini</td></tr>`;
            calculateTotal();
            return;
        }

        details.forEach((detail, index) => {
            const row = document.createElement('tr');
            row.className = 'material-row hover:bg-gray-50 border-b border-gray-100';
            row.innerHTML = `
                <td class="px-4 py-3">
                    <input type="hidden" name="materials[${index}][material_id]" value="${detail.material_id}">
                    <p class="font-medium text-xs text-gray-800">${detail.material?.name ?? ''}</p>
                    <p class="text-gray-400 text-xs mt-0.5">${detail.material?.material_code ?? ''}</p>
                </td>
                <td class="px-4 py-3 text-center text-xs text-gray-500">
                    ${detail.qty_requested} ${detail.material?.unit ?? ''}
                </td>
                <td class="px-4 py-3 text-center">
                    <input type="number" name="materials[${index}][qty_ordered]"
                        value="${detail.qty_requested}" step="0.01" min="0.01"
                        class="w-28 px-2 py-1.5 border rounded-lg text-xs text-center qty-input focus:outline-none focus:ring-2 focus:ring-blue-400"
                        onchange="calculateSubtotal(${index})" required>
                </td>
                <td class="px-4 py-3 text-right">
                    <input type="number" name="materials[${index}][unit_price]"
                        value="0" step="0.01" min="0"
                        class="w-36 px-2 py-1.5 border rounded-lg text-xs text-right price-input focus:outline-none focus:ring-2 focus:ring-blue-400"
                        onchange="calculateSubtotal(${index})" required>
                </td>
                <td class="px-4 py-3 text-right font-semibold text-xs text-gray-800 subtotal-cell" id="subtotal-${index}">
                    Rp 0
                </td>`;
            tbody.appendChild(row);
        });

        calculateTotal();
    });

    // ── Init ───────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const checked = document.querySelector('input[name="payment_type"]:checked');
        if (checked) handlePaymentType(checked.value);
        calculateTotal();
    });
</script>
@endsection