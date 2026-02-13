@extends('layouts.app')

@section('title', 'Edit Lokasi Proyek')
@section('breadcrumb', 'Project / Lokasi Proyek / Edit')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Edit Lokasi Proyek">
        <form method="POST" action="{{ route('locations.update', $location) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-4">
                <x-input 
                    label="Kavling" 
                    name="kavling" 
                    :required="true"
                    :value="old('kavling', $location->kavling)"
                    :error="$errors->first('kavling')"
                    placeholder="A-01, B-02, dll"
                />

                <x-input 
                    label="Blok" 
                    name="blok" 
                    :required="true"
                    :value="old('blok', $location->blok)"
                    :error="$errors->first('blok')"
                    placeholder="Blok A, Blok B, dll"
                />
            </div>

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea 
                    name="address" 
                    rows="3" 
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan alamat lengkap lokasi proyek..."
                >{{ old('address', $location->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                <select name="is_active" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="1" {{ old('is_active', $location->is_active) == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $location->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Info tentang kegiatan yang menggunakan lokasi ini -->
            @if($location->activities && $location->activities->count() > 0)
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded text-xs mb-4">
                    <p class="font-semibold text-yellow-800 mb-1">⚠️ Perhatian:</p>
                    <p class="text-yellow-700">Lokasi ini sedang digunakan oleh <strong>{{ $location->activities->count() }}</strong> kegiatan. Pastikan perubahan tidak mempengaruhi kegiatan yang sedang berjalan.</p>
                </div>
            @endif

            <div class="flex items-center justify-end gap-2 pt-4 border-t">
                <x-button type="button" variant="secondary" href="{{ route('locations.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">Update Lokasi</x-button>
            </div>
        </form>
    </x-card>

    <!-- Kegiatan yang menggunakan lokasi ini -->
    @if($location->activities && $location->activities->count() > 0)
        <x-card title="Kegiatan di Lokasi Ini" class="mt-4">
            <div class="space-y-2">
                @foreach($location->activities as $activity)
                    <div class="flex items-center justify-between py-2 border-b last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $activity->activity_name }}</p>
                            <p class="text-xs text-gray-600">{{ $activity->activity_code }} • {{ $activity->start_date->format('d M Y') }}</p>
                        </div>
                        <x-badge :variant="$activity->status === 'ongoing' ? 'success' : 'default'">
                            {{ ucfirst($activity->status) }}
                        </x-badge>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif
</div>
@endsection