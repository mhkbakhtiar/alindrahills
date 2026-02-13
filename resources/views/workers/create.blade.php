@extends('layouts.app')

@section('title', 'Tambah Tukang')
@section('breadcrumb', 'Payroll / Data Tukang / Tambah')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Tambah Tukang Baru">
        <form method="POST" action="{{ route('workers.store') }}">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <x-input 
                    label="Kode Tukang" 
                    name="worker_code" 
                    :required="true"
                    :value="old('worker_code')"
                    placeholder="TK-001"
                />

                <x-input 
                    label="Nama Lengkap" 
                    name="full_name" 
                    :required="true"
                    :value="old('full_name')"
                />

                <x-input 
                    label="No. Telepon" 
                    name="phone" 
                    :value="old('phone')"
                    placeholder="08123456789"
                />

                <x-input 
                    label="Jenis Tukang" 
                    name="worker_type" 
                    :required="true"
                    :value="old('worker_type')"
                    placeholder="Tukang Batu, Tukang Kayu, dll"
                />

                <x-input 
                    label="Upah Harian" 
                    name="daily_rate" 
                    type="number"
                    step="1000"
                    :required="true"
                    :value="old('daily_rate')"
                    placeholder="150000"
                />

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                    <select name="is_active" class="w-full px-3 py-2 text-sm border rounded">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat</label>
                <textarea name="address" rows="2" class="w-full px-3 py-2 text-sm border rounded">{{ old('address') }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <x-button type="button" variant="secondary" href="{{ route('workers.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection