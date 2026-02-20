@extends('layouts.app')

@section('title', 'Edit Kegiatan')
@section('breadcrumb', 'Project / Kegiatan / Edit')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    @php
        $selectedContractors = $activity->contractors
            ->pluck('contractor_id')
            ->toArray();
    @endphp

    <!-- Basic Info Card -->
    <x-card title="Edit Kegiatan">
        <form method="POST" action="{{ route('activities.update', $activity) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-4 mb-4">

                <x-input 
                    label="Nama Kegiatan" 
                    name="activity_name" 
                    :required="true"
                    :value="old('activity_name', $activity->activity_name)"
                />

                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                    <select name="location_id" required class="w-full px-3 py-2 text-sm border rounded">
                        @foreach($locations as $location)
                            <option value="{{ $location->location_id }}" {{ $activity->location_id == $location->location_id ? 'selected' : '' }}>
                                {{ $location->kavling }} - {{ $location->blok }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-input 
                    label="Jenis Kegiatan" 
                    name="activity_type" 
                    :required="true"
                    :value="old('activity_type', $activity->activity_type)"
                />

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 text-sm border rounded">
                        <option value="planned" {{ $activity->status == 'planned' ? 'selected' : '' }}>Planned</option>
                        <option value="ongoing" {{ $activity->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ $activity->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $activity->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <x-input 
                    label="Tanggal Mulai" 
                    name="start_date" 
                    type="date"
                    :required="true"
                    :value="old('start_date', $activity->start_date->format('Y-m-d'))"
                />

                <x-input 
                    label="Tanggal Selesai" 
                    name="end_date" 
                    type="date"
                    :value="old('end_date', $activity->end_date?->format('Y-m-d'))"
                />
            </div>

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 text-sm border rounded">{{ old('description', $activity->description) }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <x-button type="button" variant="secondary" href="{{ route('activities.show', $activity) }}">Batal</x-button>
                <x-button type="submit" variant="primary">Update Kegiatan</x-button>
            </div>
        </form>
    </x-card>

    <!-- Worker Management Card -->
    <x-card title="Penugasan Tukang">
        <div class="mb-4">
            <form method="POST" action="{{ route('activity-workers.store') }}" class="flex gap-2" x-data="{ assigned_date: '{{ date('Y-m-d') }}' }">
                @csrf
                <input type="hidden" name="activity_id" value="{{ $activity->activity_id }}">
                
                <select name="worker_id" required class="flex-1 px-3 py-2 text-sm border rounded">
                    <option value="">Pilih Tukang</option>
                    @foreach($availableWorkers as $w)
                        <option value="{{ $w->worker_id }}">{{ $w->worker_code }} - {{ $w->full_name }} ({{ $w->worker_type }})</option>
                    @endforeach
                </select>
                
                <input type="date" name="assigned_date" x-model="assigned_date" required class="px-3 py-2 text-sm border rounded">
                
                <input type="text" name="work_description" placeholder="Deskripsi pekerjaan" class="flex-1 px-3 py-2 text-sm border rounded">
                
                <x-button type="submit" variant="primary" size="sm">Tambah</x-button>
            </form>
        </div>

        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold">Kode</th>
                    <th class="px-3 py-2 text-left font-semibold">Nama Tukang</th>
                    <th class="px-3 py-2 text-left font-semibold">Jenis</th>
                    <th class="px-3 py-2 text-left font-semibold">Tanggal Ditugaskan</th>
                    <th class="px-3 py-2 text-left font-semibold">Deskripsi Pekerjaan</th>
                    <th class="px-3 py-2 text-center font-semibold">Status</th>
                    <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($activity->activityWorkers as $assignment)
                    <tr>
                        <td class="px-3 py-2 font-medium">{{ $assignment->worker->worker_code }}</td>
                        <td class="px-3 py-2">{{ $assignment->worker->full_name }}</td>
                        <td class="px-3 py-2">{{ $assignment->worker->worker_type }}</td>
                        <td class="px-3 py-2">{{ $assignment->assigned_date->format('d M Y') }}</td>
                        <td class="px-3 py-2">{{ $assignment->work_description ?: '-' }}</td>
                        <td class="px-3 py-2 text-center">
                            <x-badge :variant="$assignment->is_active ? 'success' : 'danger'">
                                {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                            </x-badge>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <form method="POST" action="{{ route('activity-workers.destroy', $assignment) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus penugasan tukang ini?')" class="text-red-600 hover:text-red-800">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-4 text-center text-gray-500">Belum ada tukang yang ditugaskan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <!-- Contractor Management Card -->
    <x-card title="Penugasan Contractor">
        <form method="POST" action="{{ route('activities.contractors.update', $activity) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-2 mb-4">
                @forelse($contractors as $c)
                    <label class="flex items-center gap-2 text-xs">
                        <input type="checkbox"
                            name="contractors[]"
                            value="{{ $c->contractor_id }}"
                            {{ in_array($c->contractor_id, $selectedContractors) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        {{ $c->contractor_name }}
                    </label>
                @empty
                    <p class="text-xs text-gray-500 col-span-2">
                        Belum ada contractor
                    </p>
                @endforelse
            </div>

            <div class="flex justify-end gap-2 border-t pt-3">
                <x-button type="submit" variant="primary" size="sm">
                    Update Contractor
                </x-button>
            </div>
        </form>
    </x-card>

</div>
@endsection