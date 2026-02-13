@extends('layouts.app')

@section('title', 'Stok Material')
@section('breadcrumb', 'Warehouse / Stok Material')

@section('content')
<div class="space-y-4">
    <h2 class="text-lg font-semibold text-gray-900">Stok Material per Gudang</h2>

    <x-card>
        <form method="GET" class="flex gap-3 mb-4">
            <select name="warehouse_id" class="px-3 py-2 text-sm border rounded">
                <option value="">Semua Gudang</option>
                @foreach($warehouses as $wh)
                    <option value="{{ $wh->warehouse_id }}" {{ request('warehouse_id') == $wh->warehouse_id ? 'selected' : '' }}>
                        {{ $wh->warehouse_name }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari material..." class="flex-1 px-3 py-2 text-sm border rounded">
            <x-button type="submit" variant="secondary">Filter</x-button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Material</th>
                        <th class="px-3 py-2 text-left font-semibold">Gudang</th>
                        <th class="px-3 py-2 text-center font-semibold">Satuan</th>
                        <th class="px-3 py-2 text-right font-semibold">Stok</th>
                        <th class="px-3 py-2 text-right font-semibold">Min Stock</th>
                        <th class="px-3 py-2 text-right font-semibold">Harga Rata-rata</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Nilai</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-gray-50 {{ $stock->current_stock <= $stock->material->min_stock ? 'bg-red-50' : '' }}">
                            <td class="px-3 py-2">{{ $stock->material->material_code }}</td>
                            <td class="px-3 py-2 font-medium">{{ $stock->material->material_name }}</td>
                            <td class="px-3 py-2">{{ $stock->warehouse->warehouse_name }}</td>
                            <td class="px-3 py-2 text-center">{{ $stock->material->unit }}</td>
                            <td class="px-3 py-2 text-right font-medium">{{ number_format($stock->current_stock, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($stock->material->min_stock, 2) }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($stock->average_price, 0) }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($stock->total_value, 0) }}</td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('stocks.batches', ['material' => $stock->material_id, 'warehouse' => $stock->warehouse_id]) }}" class="text-blue-600 hover:text-blue-800">Batch</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-8 text-center text-gray-500">Tidak ada data stok</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $stocks->links() }}
        </div>
    </x-card>

    <x-card title="Ringkasan Stok">
        <div class="grid grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-xs text-gray-600">Total Item</p>
                <p class="text-xl font-bold text-gray-900">{{ $summary['total_items'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Total Nilai</p>
                <p class="text-xl font-bold text-gray-900">Rp {{ number_format($summary['total_value'], 0) }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Low Stock</p>
                <p class="text-xl font-bold text-red-600">{{ $summary['low_stock_count'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Out of Stock</p>
                <p class="text-xl font-bold text-red-600">{{ $summary['out_of_stock_count'] }}</p>
            </div>
        </div>
    </x-card>
</div>
@endsection