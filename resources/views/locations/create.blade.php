@extends('layouts.app')

@section('title', 'Tambah Lokasi Proyek')
@section('breadcrumb', 'Project / Lokasi Proyek / Tambah')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Tambah Lokasi Proyek Baru">
        <form method="POST" action="{{ route('locations.store') }}">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <x-input 
                    label="Kavling" 
                    name="kavling" 
                    :required="true"
                    :value="old('kavling')"
                    :error="$errors->first('kavling')"
                    placeholder="A-01, B-02, dll"
                />

                <x-input 
                    label="Blok" 
                    name="blok" 
                    :required="true"
                    :value="old('blok')"
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
                >{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                <select name="is_active" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="p-3 bg-blue-50 border border-blue-200 rounded text-xs mb-4">
                <p class="font-semibold text-blue-800 mb-1">ℹ️ Informasi:</p>
                <ul class="list-disc list-inside text-blue-700 space-y-1">
                    <li>Kombinasi Kavling dan Blok harus unik</li>
                    <li>Lokasi yang sudah digunakan untuk kegiatan tidak bisa dihapus</li>
                    <li>Status Inactive untuk lokasi yang sudah selesai/tidak digunakan</li>
                </ul>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t">
                <x-button type="button" variant="secondary" href="{{ route('locations.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan Lokasi</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection