@extends('layouts.app')

@section('title', 'Master Material')
@section('breadcrumb', 'Material Management / Master Material')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Master Material</h2>
        <x-button variant="primary" href="{{ route('materials.create') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Material
        </x-button>
    </div>

    <x-card>
        <form method="GET" class="flex gap-3 mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama..." class="flex-1 px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-blue-500">
            <select name="category" class="px-3 py-2 text-sm border rounded">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>
            <x-button type="submit" variant="secondary">Filter</x-button>
            <x-button type="button" variant="secondary" href="{{ route('materials.index') }}">Reset</x-button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Nama Material</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Kategori</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Satuan</th>
                        <th class="px-3 py-2 text-right font-semibold text-gray-700">Min Stock</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-700">Status</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($materials as $material)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $material->material_code }}</td>
                            <td class="px-3 py-2 font-medium">{{ $material->material_name }}</td>
                            <td class="px-3 py-2">{{ $material->category }}</td>
                            <td class="px-3 py-2">{{ $material->unit }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($material->min_stock, 2) }}</td>
                            <td class="px-3 py-2 text-center">
                                <x-badge :variant="$material->is_active ? 'success' : 'danger'">
                                    {{ $material->is_active ? 'Active' : 'Inactive' }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('materials.show', $material) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                                    <a href="{{ route('materials.edit', $material) }}" class="text-green-600 hover:text-green-800">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-gray-500">Tidak ada data material</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $materials->links() }}
        </div>
    </x-card>
</div>
@endsection