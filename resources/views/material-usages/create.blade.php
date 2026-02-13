@extends('layouts.app')

@section('title', 'Pengeluaran Material')
@section('breadcrumb', 'Project / Pengeluaran Material / Buat')

@section('content')
<div class="max-w-4xl mx-auto">
    <x-card title="Pengeluaran Material untuk Kegiatan">
        <form method="POST" action="{{ route('material-usages.store') }}" x-data="materialUsageForm()">
            @csrf
            
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kegiatan <span class="text-red-500">*</span></label>
                    <select name="activity_id" required class="w-full px-3 py-2 text-sm border rounded" @change="loadActivityInfo($event)">
                        <option value="">Pilih Kegiatan</option>
                        @foreach($activities as $act)
                            <option value="{{ $act->activity_id }}" 
                                    data-location="{{ $act->location->kavling }} - {{ $act->location->blok }}"
                                    {{ old('activity_id', $selectedActivityId) == $act->activity_id ? 'selected' : '' }}>
                                {{ $act->activity_code }} - {{ $act->activity_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('activity_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-600 mt-1" x-show="activityLocation" x-text="'Lokasi: ' + activityLocation"></p>
                </div>

                <x-input 
                    label="Tanggal" 
                    name="usage_date" 
                    type="date"
                    :required="true"
                    :value="old('usage_date', date('Y-m-d'))"
                />

                <div class="col-span-3">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Gudang <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" required class="w-full px-3 py-2 text-sm border rounded">
                        <option value="">Pilih Gudang</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->warehouse_name }} ({{ $warehouse->location }})</option>
                        @endforeach
                    </select>
                    @error('warehouse_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border-t pt-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-semibold text-gray-700">Detail Material yang Digunakan</label>
                    <button type="button" @click="addMaterial()" class="text-xs text-blue-600 hover:text-blue-800 font-medium">+ Tambah Material</button>
                </div>

                <div class="space-y-2">
                    <template x-for="(material, index) in materials" :key="index">
                        <div class="grid grid-cols-12 gap-2 p-2 border rounded bg-gray-50">
                            <div class="col-span-5">
                                <select :name="'materials[' + index + '][material_id]'" required class="w-full px-2 py-1.5 text-xs border rounded">
                                    <option value="">Pilih Material</option>
                                    @foreach($availableMaterials as $mat)
                                        <option value="{{ $mat->material_id }}">
                                            {{ $mat->material_code }} - {{ $mat->material_name }} ({{ $mat->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <input type="number" :name="'materials[' + index + '][qty_used]'" step="0.01" required placeholder="Jumlah" class="w-full px-2 py-1.5 text-xs border rounded">
                            </div>
                            <div class="col-span-4">
                                <input type="text" :name="'materials[' + index + '][notes]'" placeholder="Catatan (opsional)" class="w-full px-2 py-1.5 text-xs border rounded">
                            </div>
                            <div class="col-span-1 flex items-center justify-center">
                                <button type="button" @click="removeMaterial(index)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                @error('materials')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded text-xs">
                    <p class="font-semibold text-blue-800 mb-1">ℹ️ Informasi Penting:</p>
                    <ul class="list-disc list-inside text-blue-700 space-y-1">
                        <li>Sistem akan otomatis menggunakan metode <strong>FIFO (First In First Out)</strong></li>
                        <li>Material akan diambil dari batch tertua terlebih dahulu</li>
                        <li>Pastikan stok mencukupi di gudang yang dipilih</li>
                        <li>Pengeluaran akan otomatis mengurangi stok gudang</li>
                    </ul>
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Umum</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 text-sm border rounded" placeholder="Catatan tambahan tentang pengeluaran material ini...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <x-button type="button" variant="secondary" href="{{ route('material-usages.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan Pengeluaran</x-button>
            </div>
        </form>
    </x-card>
</div>

<script>
function materialUsageForm() {
    return {
        materials: [{}],
        activityLocation: '',
        addMaterial() {
            this.materials.push({});
        },
        removeMaterial(index) {
            if (this.materials.length > 1) {
                this.materials.splice(index, 1);
            }
        },
        loadActivityInfo(event) {
            const option = event.target.options[event.target.selectedIndex];
            this.activityLocation = option.dataset.location || '';
        }
    }
}
</script>
@endsection