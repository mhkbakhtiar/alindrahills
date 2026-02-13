@extends('layouts.app')

@section('title', 'Batch Tracking')
@section('breadcrumb', 'Warehouse / Batch Tracking')

@section('content')
<div class="space-y-4">
    <h2 class="text-lg font-semibold text-gray-900">Batch Tracking</h2>

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
            
            <select name="status" class="px-3 py-2 text-sm border rounded">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="depleted" {{ request('status') == 'depleted' ? 'selected' : '' }}>Depleted</option>
            </select>

            <x-button type="submit" variant="secondary">Filter</x-button>
            <x-button type="button" variant="secondary" href="{{ route('batches.index') }}">Reset</x-button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Batch Number</th>
                        <th class="px-3 py-2 text-left font-semibold">Material</th>
                        <th class="px-3 py-2 text-left font-semibold">Gudang</th>
                        <th class="px-3 py-2 text-left font-semibold">Tgl Beli</th>
                        <th class="px-3 py-2 text-right font-semibold">Harga/Unit</th>
                        <th class="px-3 py-2 text-right font-semibold">Qty Masuk</th>
                        <th class="px-3 py-2 text-right font-semibold">Qty Sisa</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Nilai</th>
                        <th class="px-3 py-2 text-center font-semibold">Umur (hari)</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $batch->batch_number }}</td>
                            <td class="px-3 py-2">
                                <div class="font-medium">{{ $batch->material->material_name }}</div>
                                <div class="text-gray-600">{{ $batch->material->material_code }}</div>
                            </td>
                            <td class="px-3 py-2">{{ $batch->warehouse->warehouse_name }}</td>
                            <td class="px-3 py-2">{{ $batch->purchase_date->format('d M Y') }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($batch->unit_price, 0) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($batch->qty_in, 2) }}</td>
                            <td class="px-3 py-2 text-right font-medium">{{ number_format($batch->qty_remaining, 2) }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($batch->total_value, 0) }}</td>
                            <td class="px-3 py-2 text-center">{{ $batch->age_days }}</td>
                            <td class="px-3 py-2 text-center">
                                <x-badge :variant="$batch->status === 'active' ? 'success' : 'default'">
                                    {{ ucfirst($batch->status) }}
                                </x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-3 py-8 text-center text-gray-500">Tidak ada batch</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $batches->links() }}
        </div>
    </x-card>

    <!-- Summary -->
    <x-card title="Ringkasan Batch">
        <div class="grid grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-xs text-gray-600">Total Batch</p>
                <p class="text-2xl font-bold text-gray-900">{{ $batches->total() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Batch Aktif</p>
                <p class="text-2xl font-bold text-green-600">{{ $batches->where('status', 'active')->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Batch Habis</p>
                <p class="text-2xl font-bold text-gray-600">{{ $batches->where('status', 'depleted')->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Total Nilai (halaman ini)</p>
                <p class="text-base font-bold text-blue-600">Rp {{ number_format($batches->where('status', 'active')->sum('total_value'), 0) }}</p>
            </div>
        </div>
    </x-card>
</div>
@endsection