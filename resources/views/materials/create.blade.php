@extends('layouts.app')

@section('title', 'Tambah Material')
@section('breadcrumb', 'Material Management / Master Material / Tambah')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Tambah Material Baru">
        <form method="POST" action="{{ route('materials.store') }}">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">

                <x-input 
                    label="Nama Material" 
                    name="material_name" 
                    :required="true"
                    :value="old('material_name')"
                    :error="$errors->first('material_name')"
                />

                <x-input 
                    label="Kategori" 
                    name="category" 
                    :required="true"
                    :value="old('category')"
                    :error="$errors->first('category')"
                    placeholder="Contoh: Semen, Pasir, Besi"
                />

                <x-input 
                    label="Satuan" 
                    name="unit" 
                    :required="true"
                    :value="old('unit')"
                    :error="$errors->first('unit')"
                    placeholder="Contoh: sak, m3, kg"
                />

                <x-input 
                    label="Minimum Stock" 
                    name="min_stock" 
                    type="number"
                    step="0.01"
                    :value="old('min_stock', 0)"
                    :error="$errors->first('min_stock')"
                />

                <x-select
                    label="Metode Costing"
                    name="costing_method"
                    :required="true"
                    :options="['FIFO' => 'FIFO (First In First Out)', 'LIFO' => 'LIFO (Last In First Out)', 'AVERAGE' => 'Average']"
                    :error="$errors->first('costing_method')"
                />
            </div>

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t">
                <x-button type="button" variant="secondary" href="{{ route('materials.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection