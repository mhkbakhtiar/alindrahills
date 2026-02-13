@extends('layouts.app')

@section('title', 'Buat Pengajuan Pembelian')
@section('breadcrumb', 'Purchase / Pengajuan Pembelian / Buat')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Buat Pengajuan Pembelian Material</h2>
        <p class="text-sm text-gray-600">Buat pengajuan untuk pembelian material</p>
    </div>

    <form action="{{ route('purchase-requests.store') }}" method="POST" id="requestForm">
        @csrf
        
        <x-card class="mb-4">
            <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Pengajuan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengajuan <span class="text-red-500">*</span></label>
                    <input type="date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('request_date') border-red-500 @enderror" required>
                    @error('request_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="letter_number" value="{{ old('letter_number') }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('letter_number') border-red-500 @enderror" 
                        placeholder="Contoh: SPB/001/I/2026" required>
                    @error('letter_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" name="letter_date" value="{{ old('letter_date', date('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('letter_date') border-red-500 @enderror" required>
                    @error('letter_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pembelian <span class="text-red-500">*</span></label>
                    <textarea name="purpose" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('purpose') border-red-500 @enderror" 
                        placeholder="Jelaskan tujuan/keperluan pembelian material ini" required>{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-card>

        <x-card class="mb-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-900">Daftar Material</h3>
                <button type="button" onclick="addMaterial()" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Material
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Material</th>
                            <th class="px-3 py-2 text-center font-semibold">Satuan</th>
                            <th class="px-3 py-2 text-center font-semibold">Qty</th>
                            <th class="px-3 py-2 text-left font-semibold">Catatan</th>
                            <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="materialTableBody" class="divide-y divide-gray-200">
                        <tr id="emptyState">
                            <td colspan="5" class="px-3 py-8 text-center text-gray-500">
                                Belum ada material ditambahkan. Klik tombol "Tambah Material" untuk menambah.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-card>

        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Pengajuan
            </button>
            <a href="{{ route('purchase-requests.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
let materialIndex = 0;
const materials = @json($materials);

function addMaterial() {
    const emptyState = document.getElementById('emptyState');
    if (emptyState) {
        emptyState.remove();
    }

    const tbody = document.getElementById('materialTableBody');
    const row = document.createElement('tr');
    row.id = `material-row-${materialIndex}`;
    row.className = 'hover:bg-gray-50';
    
    row.innerHTML = `
        <td class="px-3 py-2">
            <select name="materials[${materialIndex}][material_id]" 
                onchange="updateUnit(${materialIndex})"
                class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Pilih Material</option>
                ${materials.map(m => `<option value="${m.material_id}" data-unit="${m.unit}">${m.material_code} - ${m.material_name}</option>`).join('')}
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="text" id="unit-${materialIndex}" readonly
                class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-center bg-gray-50" 
                placeholder="-">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="materials[${materialIndex}][qty_requested]" 
                step="0.01" min="0.01" value="1"
                class="w-24 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:ring-blue-500 focus:border-blue-500" required>
        </td>
        <td class="px-3 py-2">
            <input type="text" name="materials[${materialIndex}][notes]" 
                class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:ring-blue-500 focus:border-blue-500" 
                placeholder="Catatan (opsional)">
        </td>
        <td class="px-3 py-2 text-center">
            <button type="button" onclick="removeMaterial(${materialIndex})" class="text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    materialIndex++;
}

function updateUnit(index) {
    const select = document.querySelector(`select[name="materials[${index}][material_id]"]`);
    const selectedOption = select.options[select.selectedIndex];
    const unit = selectedOption.dataset.unit || '-';
    
    const unitInput = document.getElementById(`unit-${index}`);
    unitInput.value = unit;
}

function removeMaterial(index) {
    const row = document.getElementById(`material-row-${index}`);
    if (row) {
        row.remove();
        
        const tbody = document.getElementById('materialTableBody');
        if (tbody.children.length === 0) {
            tbody.innerHTML = '<tr id="emptyState"><td colspan="5" class="px-3 py-8 text-center text-gray-500">Belum ada material ditambahkan. Klik tombol "Tambah Material" untuk menambah.</td></tr>';
        }
    }
}

document.getElementById('requestForm').addEventListener('submit', function(e) {
    const tbody = document.getElementById('materialTableBody');
    if (tbody.children.length === 0 || document.getElementById('emptyState')) {
        e.preventDefault();
        alert('Minimal harus ada 1 material dalam pengajuan');
        return false;
    }
});

window.addEventListener('load', function() {
    addMaterial();
});
</script>
@endsection