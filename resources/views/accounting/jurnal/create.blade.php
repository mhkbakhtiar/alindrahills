{{-- resources/views/accounting/jurnal/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Buat Jurnal Baru')
@section('breadcrumb', 'Accounting / Jurnal / Create')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Buat Jurnal Baru</h2>
        <x-button variant="secondary" href="{{ route('accounting.jurnal.index') }}">
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

    <form action="{{ route('accounting.jurnal.store') }}" method="POST" id="jurnalForm">
        @csrf
        
        {{-- Header Information --}}
        <x-card>
            <h3 class="text-sm font-semibold mb-4">Informasi Jurnal</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal *</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Jurnal *</label>
                    <select name="jenis_jurnal" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="umum" {{ old('jenis_jurnal') == 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="penyesuaian" {{ old('jenis_jurnal') == 'penyesuaian' ? 'selected' : '' }}>Penyesuaian</option>
                        <option value="penutup" {{ old('jenis_jurnal') == 'penutup' ? 'selected' : '' }}>Penutup</option>
                        <option value="pembalik" {{ old('jenis_jurnal') == 'pembalik' ? 'selected' : '' }}>Pembalik</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Departemen</label>
                    <input type="text" name="departemen" value="{{ old('departemen') }}" 
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
                            <th class="px-2 py-2 text-left font-semibold" style="min-width: 200px;">Kavling - Pembeli</th>
                            <th class="px-2 py-2 text-center font-semibold w-12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <!-- Default 2 rows -->
                        <tr class="item-row border-b">
                            <td class="px-2 py-2 text-center row-number">1</td>
                            <td class="px-2 py-2">
                                <select name="items[0][kode_perkiraan]" class="w-full px-2 py-1 border rounded text-xs" required>
                                    <option value="">-- Pilih Perkiraan --</option>
                                    @foreach($perkiraan as $p)
                                        <option value="{{ $p->kode_perkiraan }}">{{ $p->kode_perkiraan }} - {{ $p->nama_perkiraan }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[0][keterangan]" class="w-full px-2 py-1 border rounded text-xs">
                            </td>
                            <td class="px-2 py-2">
                                <input type="number" name="items[0][debet]" value="0" step="0.01" min="0" 
                                    class="w-full px-2 py-1 border rounded text-xs text-right debet-input" onchange="calculateBalance()">
                            </td>
                            <td class="px-2 py-2">
                                <input type="number" name="items[0][kredit]" value="0" step="0.01" min="0" 
                                    class="w-full px-2 py-1 border rounded text-xs text-right kredit-input" onchange="calculateBalance()">
                            </td>
                            <td class="px-2 py-2">
                                <select name="items[0][kavling_pembeli_id]" class="w-full px-2 py-1 border rounded text-xs kavling-pembeli-select" onchange="updateKavlingPembeli(this, 0)">
                                    <option value="">-- Pilih Kavling & Pembeli --</option>
                                    @foreach($kavlingPembeli as $kp)
                                        <option value="{{ $kp->id }}" 
                                            data-kavling="{{ $kp->kavling->kavling ?? '' }}" 
                                            data-user="{{ $kp->user_id }}">
                                            {{ $kp->kavling->kavling ?? '' }} - {{ $kp->kavling->blok ?? '' }} | {{ $kp->pembeli->nama ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="items[0][kode_kavling]" id="kode_kavling_0">
                                <input type="hidden" name="items[0][id_user]" id="id_user_0">
                            </td>
                            <td class="px-2 py-2 text-center">
                                <button type="button" onclick="removeItemRow(this)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr class="item-row border-b">
                            <td class="px-2 py-2 text-center row-number">2</td>
                            <td class="px-2 py-2">
                                <select name="items[1][kode_perkiraan]" class="w-full px-2 py-1 border rounded text-xs" required>
                                    <option value="">-- Pilih Perkiraan --</option>
                                    @foreach($perkiraan as $p)
                                        <option value="{{ $p->kode_perkiraan }}">{{ $p->kode_perkiraan }} - {{ $p->nama_perkiraan }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[1][keterangan]" class="w-full px-2 py-1 border rounded text-xs">
                            </td>
                            <td class="px-2 py-2">
                                <input type="number" name="items[1][debet]" value="0" step="0.01" min="0" 
                                    class="w-full px-2 py-1 border rounded text-xs text-right debet-input" onchange="calculateBalance()">
                            </td>
                            <td class="px-2 py-2">
                                <input type="number" name="items[1][kredit]" value="0" step="0.01" min="0" 
                                    class="w-full px-2 py-1 border rounded text-xs text-right kredit-input" onchange="calculateBalance()">
                            </td>
                            <td class="px-2 py-2">
                                <select name="items[1][kavling_pembeli_id]" class="w-full px-2 py-1 border rounded text-xs kavling-pembeli-select" onchange="updateKavlingPembeli(this, 1)">
                                    <option value="">-- Pilih Kavling & Pembeli --</option>
                                    @foreach($kavlingPembeli as $kp)
                                        <option value="{{ $kp->id }}" 
                                            data-kavling="{{ $kp->kavling->kavling ?? '' }}" 
                                            data-user="{{ $kp->user_id }}">
                                            {{ $kp->kavling->kavling ?? '' }} - {{ $kp->kavling->blok ?? '' }} | {{ $kp->pembeli->nama ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="items[1][kode_kavling]" id="kode_kavling_1">
                                <input type="hidden" name="items[1][id_user]" id="id_user_1">
                            </td>
                            <td class="px-2 py-2 text-center">
                                <button type="button" onclick="removeItemRow(this)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td colspan="3" class="px-2 py-2 text-right">TOTAL:</td>
                            <td class="px-2 py-2 text-right" id="totalDebet">Rp 0</td>
                            <td class="px-2 py-2 text-right" id="totalKredit">Rp 0</td>
                            <td colspan="2" class="px-2 py-2">
                                <span id="balanceStatus" class="text-xs"></span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>

        {{-- Action Buttons --}}
        <x-card>
            <div class="flex justify-end gap-2">
                <x-button type="button" variant="secondary" href="{{ route('accounting.jurnal.index') }}">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Jurnal
                </x-button>
            </div>
        </x-card>
    </form>
</div>

<script>
    let itemIndex = 2; // Start from 2 since we have 2 default rows

    // Data kavling pembeli untuk JavaScript
    const kavlingPembeliData = @json($kavlingPembeli);

    function updateKavlingPembeli(selectElement, index) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const kodeKavling = selectedOption.getAttribute('data-kavling');
        const userId = selectedOption.getAttribute('data-user');
        
        document.getElementById('kode_kavling_' + index).value = kodeKavling || '';
        document.getElementById('id_user_' + index).value = userId || '';
    }

    function addItemRow() {
        const tbody = document.getElementById('itemsBody');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row border-b';
        
        let kavlingPembeliOptions = '<option value="">-- Pilih Kavling & Pembeli --</option>';
        kavlingPembeliData.forEach(kp => {
            const kavlingText = kp.kavling ? kp.kavling.kavling : '';
            const blokText = kp.kavling ? kp.kavling.blok : '';
            const pembeliText = kp.pembeli ? kp.pembeli.nama : '';
            kavlingPembeliOptions += `<option value="${kp.id}" data-kavling="${kavlingText}" data-user="${kp.user_id}">${kavlingText} - ${blokText} | ${pembeliText}</option>`;
        });
        
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
                <select name="items[${itemIndex}][kavling_pembeli_id]" class="w-full px-2 py-1 border rounded text-xs kavling-pembeli-select" onchange="updateKavlingPembeli(this, ${itemIndex})">
                    ${kavlingPembeliOptions}
                </select>
                <input type="hidden" name="items[${itemIndex}][kode_kavling]" id="kode_kavling_${itemIndex}">
                <input type="hidden" name="items[${itemIndex}][id_user]" id="id_user_${itemIndex}">
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

@endsection