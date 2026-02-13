@extends('layouts.app')

@section('title', 'Laporan Stok')
@section('breadcrumb', 'Laporan / Stok Gudang')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Laporan Stok Gudang</h2>
        <div class="flex gap-2">
            <x-button variant="secondary" href="{{ route('reports.stock.movements') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                Laporan Mutasi
            </x-button>
            <form action="{{ route('reports.stock.export') }}" method="GET" class="inline">
                @foreach(request()->all() as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <x-button variant="primary" type="submit">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </x-button>
            </form>
        </div>
    </div>

    <!-- Filter Form -->
    <x-card>
        <form method="GET" action="{{ route('reports.stock.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gudang</label>
                <select name="warehouse_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Gudang</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->warehouse_id }}" {{ request('warehouse_id') == $warehouse->warehouse_id ? 'selected' : '' }}>
                            {{ $warehouse->warehouse_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Stok</label>
                <select name="stock_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Habis</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="normal" {{ request('stock_status') == 'normal' ? 'selected' : '' }}>Normal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <x-button variant="primary" type="submit" class="flex-1">Filter</x-button>
                <x-button variant="secondary" href="{{ route('reports.stock.index') }}">Reset</x-button>
            </div>
        </form>
    </x-card>
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-card>
            <div class="text-sm text-gray-600">Total Item</div>
            <div class="text-2xl font-bold text-gray-900">{{ $summary['total_items'] }}</div>
        </x-card>
        <x-card>
            <div class="text-sm text-gray-600">Total Nilai Stok</div>
            <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($summary['total_value'], 0) }}</div>
        </x-card>
        <x-card>
            <div class="text-sm text-gray-600">Stok Menipis</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $summary['low_stock'] }}</div>
        </x-card>
        <x-card>
            <div class="text-sm text-gray-600">Stok Habis</div>
            <div class="text-2xl font-bold text-red-600">{{ $summary['out_of_stock'] }}</div>
        </x-card>
    </div>

    <!-- Stock by Warehouse -->
    <x-card>
        <h3 class="text-lg font-semibold mb-4">Ringkasan per Gudang</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Gudang</th>
                        <th class="px-3 py-2 text-center font-semibold">Jumlah Item</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Quantity</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($byWarehouse as $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $data['warehouse'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $data['total_items'] }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($data['total_quantity'], 2) }}</td>
                            <td class="px-3 py-2 text-right font-medium">Rp {{ number_format($data['total_value'], 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Stock by Category -->
    <x-card>
        <h3 class="text-lg font-semibold mb-4">Ringkasan per Kategori</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Kategori</th>
                        <th class="px-3 py-2 text-center font-semibold">Jumlah Item</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Quantity</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($byCategory as $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $data['category'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $data['total_items'] }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($data['total_quantity'], 2) }}</td>
                            <td class="px-3 py-2 text-right font-medium">Rp {{ number_format($data['total_value'], 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Stock Detail -->
    <x-card>
        <h3 class="text-lg font-semibold mb-4">Detail Stok Material</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Kode Material</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Material</th>
                        <th class="px-3 py-2 text-left font-semibold">Gudang</th>
                        <th class="px-3 py-2 text-left font-semibold">Kategori</th>
                        <th class="px-3 py-2 text-center font-semibold">Stok</th>
                        <th class="px-3 py-2 text-center font-semibold">Min. Stok</th>
                        <th class="px-3 py-2 text-right font-semibold">Harga Rata-rata</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Nilai</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $stock->material->material_code }}</td>
                            <td class="px-3 py-2">{{ $stock->material->material_name }}</td>
                            <td class="px-3 py-2">{{ $stock->warehouse->warehouse_name }}</td>
                            <td class="px-3 py-2">{{ $stock->material->category }}</td>
                            <td class="px-3 py-2 text-center">{{ number_format($stock->current_stock, 2) }} {{ $stock->material->unit }}</td>
                            <td class="px-3 py-2 text-center">{{ number_format($stock->material->min_stock, 2) }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($stock->average_price, 0) }}</td>
                            <td class="px-3 py-2 text-right font-medium">Rp {{ number_format($stock->current_stock * $stock->average_price, 0) }}</td>
                            <td class="px-3 py-2 text-center">
                                @if($stock->current_stock == 0)
                                    <x-badge variant="danger">Habis</x-badge>
                                @elseif($stock->current_stock <= $stock->material->min_stock)
                                    <x-badge variant="warning">Menipis</x-badge>
                                @else
                                    <x-badge variant="success">Normal</x-badge>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection