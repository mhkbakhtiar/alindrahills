@extends('layouts.app')

@section('title', 'Buat Kegiatan')
@section('breadcrumb', 'Project / Kegiatan / Buat')

@section('content')
<div class="max-w-4xl mx-auto">
    <x-card title="Buat Kegiatan Baru">
        <form method="POST" action="{{ route('activities.store') }}" x-data="activityForm()">
            @csrf
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <x-input 
                    label="Kode Kegiatan" 
                    name="activity_code" 
                    :required="true"
                    :value="old('activity_code')"
                    placeholder="ACT-001"
                />

                <x-input 
                    label="Nama Kegiatan" 
                    name="activity_name" 
                    :required="true"
                    :value="old('activity_name')"
                    placeholder="Pengecoran Lantai"
                />

                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                    <select name="location_id" required class="w-full px-3 py-2 text-sm border rounded">
                        <option value="">Pilih Lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->location_id }}">{{ $location->kavling }} - {{ $location->blok }}</option>
                        @endforeach
                    </select>
                </div>

                <x-input 
                    label="Jenis Kegiatan" 
                    name="activity_type" 
                    :required="true"
                    :value="old('activity_type')"
                    placeholder="Pengecoran, Pemasangan, dll"
                />

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 text-sm border rounded">
                        <option value="planned">Planned</option>
                        <option value="ongoing" selected>Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <x-input 
                    label="Tanggal Mulai" 
                    name="start_date" 
                    type="date"
                    :required="true"
                    :value="old('start_date', date('Y-m-d'))"
                />

                <x-input 
                    label="Tanggal Selesai" 
                    name="end_date" 
                    type="date"
                    :value="old('end_date')"
                />
            </div>

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 text-sm border rounded" placeholder="Deskripsi detail kegiatan...">{{ old('description') }}</textarea>
            </div>

            <!-- Worker Assignment Section -->
            <div class="border-t pt-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-semibold text-gray-700">Penugasan Tukang</label>
                    <button type="button" @click="addWorker()" class="text-xs text-blue-600 hover:text-blue-800 font-medium">+ Tambah Tukang</button>
                </div>

                <div class="space-y-2">
                    <template x-for="(worker, index) in workers" :key="index">
                        <div class="grid grid-cols-12 gap-2 p-2 border rounded bg-gray-50">
                            <div class="col-span-5">
                                <select :name="'workers[' + index + '][worker_id]'" class="w-full px-2 py-1.5 text-xs border rounded">
                                    <option value="">Pilih Tukang</option>
                                    @foreach($availableWorkers as $w)
                                        <option value="{{ $w->worker_id }}">
                                            {{ $w->worker_code }} - {{ $w->full_name }} ({{ $w->worker_type }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <input type="date" :name="'workers[' + index + '][assigned_date]'" :value="today" class="w-full px-2 py-1.5 text-xs border rounded">
                            </div>
                            <div class="col-span-4">
                                <input type="text" :name="'workers[' + index + '][work_description]'" placeholder="Deskripsi pekerjaan" class="w-full px-2 py-1.5 text-xs border rounded">
                            </div>
                            <div class="col-span-1 flex items-center justify-center">
                                <button type="button" @click="removeWorker(index)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <p class="text-xs text-gray-600 mt-2">*Penugasan tukang bersifat opsional. Anda bisa menambahkan tukang nanti setelah kegiatan dibuat.</p>
            </div>

            <!-- Contractor Assignment Section -->
            <div class="border-t pt-4 mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    Contractor Terlibat
                </label>

                <div class="grid grid-cols-2 gap-2">
                    @forelse($contractors as $c)
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox"
                                name="contractors[]"
                                value="{{ $c->contractor_id }}"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            {{ $c->contractor_name }}
                        </label>
                    @empty
                        <p class="text-xs text-gray-500 col-span-2">
                            Belum ada data contractor
                        </p>
                    @endforelse
                </div>
            </div>


            <div class="flex justify-end gap-2 pt-4 border-t">
                <x-button type="button" variant="secondary" href="{{ route('activities.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan Kegiatan</x-button>
            </div>
        </form>
    </x-card>
</div>

<script>
function activityForm() {
    return {
        workers: [],
        today: '{{ date("Y-m-d") }}',
        addWorker() {
            this.workers.push({});
        },
        removeWorker(index) {
            this.workers.splice(index, 1);
        }
    }
}
</script>
@endsection