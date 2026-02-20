{{-- resources/views/accounting/jurnal/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Jurnal')
@section('breadcrumb', 'Accounting / Jurnal / Edit')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Edit Jurnal: {{ $jurnal->nomor_bukti }}</h2>
        <x-button variant="secondary" href="{{ route('accounting.jurnal.show', $jurnal) }}">
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

    @if($jurnal->status !== 'draft')
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm">
            ⚠️ Perhatian: Hanya jurnal dengan status DRAFT yang dapat diedit.
        </div>
    @endif

    <form action="{{ route('accounting.jurnal.update', $jurnal) }}" method="POST" id="jurnalForm">
        @csrf
        @method('PUT')
        
        {{-- Header Information --}}
        <x-card>
            <h3 class="text-sm font-semibold mb-4">Informasi Jurnal</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal *</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $jurnal->tanggal) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Jurnal *</label>
                    <select name="jenis_jurnal" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="umum" {{ old('jenis_jurnal', $jurnal->jenis_jurnal) == 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="penyesuaian" {{ old('jenis_jurnal', $jurnal->jenis_jurnal) == 'penyesuaian' ? 'selected' : '' }}>Penyesuaian</option>
                        <option value="penutup" {{ old('jenis_jurnal', $jurnal->jenis_jurnal) == 'penutup' ? 'selected' : '' }}>Penutup</option>
                        <option value="pembalik" {{ old('jenis_jurnal', $jurnal->jenis_jurnal) == 'pembalik' ? 'selected' : '' }}>Pembalik</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan', $jurnal->keterangan) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Departemen</label>
                    <input type="text" name="departemen" value="{{ old('departemen', $jurnal->departemen) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </x-card>

        {{-- Journal Items --}}
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold">Detail Item Jurnal</h3>
                <button type="button" onclick="addItemRow()" class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    + Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs" id="itemsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-semibold w-8">#</th>
                            <th class="px-2 py-2 text-left font-semibold" style="min-width: 200px;">Perkiraan *</th>
                            <th class="px-2 py-2 text-left font-semibold" style="min-width: 200px;">Keterangan</th>
                            <th class="px-2 py-2 text-right font-semibold" style="min-width: 120px;">Debet (Rp)</th>
                            <th class="px-2 py-2 text-right font-semibold" style="min-width: 120px;">Kredit (Rp)</th>
                            <th class="px-2 py-2 text-left font-semibold" style="min-width: 150px;">Kavling</th>
                            <th class="px-2 py-2 text-left font-semibold" style="min-width: 150px;">User</th>
                            <th class="px-2 py-2 text-center font-semibold w-12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @foreach($jurnal->items as $index => $item)
                        <tr class="item-row border-b">
                            <td class="px-2 py-2 text-center row-number">{{ $index + 1 }}</td>
                            <td class="px-2 py-2">
                                <select name="items[{{ $index }}][kode_perkiraan]" class="w-full px-2 py-1 border rounded text-xs" required>
                                    <option value="">-- Pilih Perkiraan --</option>
                                    @foreach($perkiraan as $p)
                                        <option value="{{ $p->kode_perkiraan }}" {{ $item->kode_perkiraan == $p->kode_perkiraan ? 'selected' : '' }}>
                                            {{ $p->kode_perkiraan }} - {{ $p->nama_perkiraan }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[{{ $index }}][keterangan]" value="{{ $item->keterangan }}" 
                                    class="w-full px-2 py-1 border rounded text-xs">
                            </td>
                            <td class="px-2 py-2">
                                <input type="number" name="items[{{ $index }}][debet]" value="{{ $item->debet }}" step="0.01" min="0" 
                                    class="w-full px-2 py-1 border rounded text-xs text-right debet-input" onchange="calculateBalance()">
                            </td>
                            <td class="px-2 py-2">
                                <input type="number" name="items[{{ $index }}][kredit]" value="{{ $item->kredit }}" step="0.01" min="0" 
                                    class="w-full px-2 py-1 border rounded text-xs text-right kredit-input" onchange="calculateBalance()">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[{{ $index }}][kode_kavling]" value="{{ $item->kode_kavling }}" 
                                    class="w-full px-2 py-1 border rounded text-xs">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[{{ $index }}][id_user]" value="{{ $item->id_user }}" 
                                    class="w-full px-2 py-1 border rounded text-xs">
                            </td>
                            <td class="px-2 py-2 text-center">
                                <button type="button" onclick="removeItemRow(this)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td colspan="3" class="px-2 py-2 text-right">TOTAL:</td>
                            <td class="px-2 py-2 text-right" id="totalDebet">Rp 0</td>
                            <td class="px-2 py-2 text-right" id="totalKredit">Rp 0</td>
                            <td colspan="3" class="px-2 py-2">
                                <span id="balanceStatus" class="text-xs"></span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>

        {{-- Action Buttons --}}
        <x-card>
            <div class="flex justify-between items-center">
                <div>
                    @if($jurnal->status === 'draft')
                        <form action="{{ route('accounting.jurnal.destroy', $jurnal) }}" method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus jurnal ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="danger">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus Jurnal
                            </x-button>
                        </form>
                    @endif
                </div>
                <div class="flex gap-2">
                    <x-button type="button" variant="secondary" href="{{ route('accounting.jurnal.show', $jurnal) }}">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Jurnal
                    </x-button>
                </div>
            </div>
        </x-card>
    </form>
</div>

@push('scripts')
<script>
let itemIndex = {{ $jurnal->items->count() }};

function addItemRow() {
    const tbody = document.getElementById('itemsBody');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row border-b';
    
    newRow.innerHTML = `
        <td class="px-2 py-2 text-center row-number">${itemIndex + 1}</td>
        <td class="px-2 py-2">
            <select name="items[${itemIndex}][kode_perkiraan]" class="w-full px-2 py-1 border rounded text-xs" required>
                <option value="">-- Pilih Perkiraan --</option>
                @foreach($perkiraan as $p)
                    <option value="{{ $p->kode_perkiraan }}">{{ $p->kode_perkiraan }} - {{ $p->nama_perkiraan }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-2 py-2">
            <input type="text" name="items[${itemIndex}][keterangan]" class="w-full px-2 py-1 border rounded text-xs">
        </td>
        <td class="px-2 py-2">
            <input type="number" name="items[${itemIndex}][debet]" value="0" step="0.01" min="0" 
                class="w-full px-2 py-1 border rounded text-xs text-right debet-input" onchange="calculateBalance()">
        </td>
        <td class="px-2 py-2">
            <input type="number" name="items[${itemIndex}][kredit]" value="0" step="0.01" min="0" 
                class="w-full px-2 py-1 border rounded text-xs text-right kredit-input" onchange="calculateBalance()">
        </td>
        <td class="px-2 py-2">
            <input type="text" name="items[${itemIndex}][kode_kavling]" class="w-full px-2 py-1 border rounded text-xs">
        </td>
        <td class="px-2 py-2">
            <input type="text" name="items[${itemIndex}][id_user]" class="w-full px-2 py-1 border rounded text-xs">
        </td>
        <td class="px-2 py-2 text-center">
            <button type="button" onclick="removeItemRow(this)" class="text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    itemIndex++;
    updateRowNumbers();
    calculateBalance();
}

function removeItemRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 2) {
        alert('Minimal harus ada 2 baris item jurnal!');
        return;
    }
    
    btn.closest('tr').remove();
    updateRowNumbers();
    calculateBalance();
}

function updateRowNumbers() {
    const rows = document.querySelectorAll('.item-row');
    rows.forEach((row, index) => {
        row.querySelector('.row-number').textContent = index + 1;
    });
}

function calculateBalance() {
    let totalDebet = 0;
    let totalKredit = 0;
    
    document.querySelectorAll('.debet-input').forEach(input => {
        totalDebet += parseFloat(input.value) || 0;
    });
    
    document.querySelectorAll('.kredit-input').forEach(input => {
        totalKredit += parseFloat(input.value) || 0;
    });
    
    document.getElementById('totalDebet').textContent = 'Rp ' + totalDebet.toLocaleString('id-ID');
    document.getElementById('totalKredit').textContent = 'Rp ' + totalKredit.toLocaleString('id-ID');
    
    const statusEl = document.getElementById('balanceStatus');
    const difference = totalDebet - totalKredit;
    
    if (Math.abs(difference) < 0.01) {
        statusEl.textContent = '✓ BALANCE';
        statusEl.className = 'text-xs text-green-600 font-semibold';
    } else {
        statusEl.textContent = `✗ TIDAK BALANCE (Selisih: Rp ${Math.abs(difference).toLocaleString('id-ID')})`;
        statusEl.className = 'text-xs text-red-600 font-semibold';
    }
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateBalance();
});
</script>
@endpush
@endsection