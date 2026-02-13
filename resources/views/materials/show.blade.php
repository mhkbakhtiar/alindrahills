@extends('layouts.app')

@section('title', 'Detail Material')
@section('breadcrumb', 'Material Management / Master Material / Detail')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Detail Material</h2>
        <div class="flex gap-2">
            <x-button variant="secondary" href="{{ route('materials.edit', $material) }}">Edit</x-button>
            <x-button variant="secondary" href="{{ route('materials.index') }}">Kembali</x-button>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <x-card title="Informasi Material" class="col-span-2">
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="font-semibold text-gray-700">Kode Material</dt>
                    <dd class="text-gray-900">{{ $material->material_code }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Nama Material</dt>
                    <dd class="text-gray-900">{{ $material->material_name }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Kategori</dt>
                    <dd class="text-gray-900">{{ $material->category }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Satuan</dt>
                    <dd class="text-gray-900">{{ $material->unit }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Minimum Stock</dt>
                    <dd class="text-gray-900">{{ number_format($material->min_stock, 2) }} {{ $material->unit }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Metode Costing</dt>
                    <dd class="text-gray-900">{{ $material->costing_method }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="font-semibold text-gray-700">Deskripsi</dt>
                    <dd class="text-gray-900">{{ $material->description ?: '-' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card title="Status">
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-gray-700">Status:</span>
                    <x-badge :variant="$material->is_active ? 'success' : 'danger'" class="ml-2">
                        {{ $material->is_active ? 'Active' : 'Inactive' }}
                    </x-badge>
                </div>
                <div>
                    <span class="text-gray-700">Dibuat:</span>
                    <span class="text-gray-900">{{ $material->created_at->format('d M Y H:i') }}</span>
                </div>
                <div>
                    <span class="text-gray-700">Diupdate:</span>
                    <span class="text-gray-900">{{ $material->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </x-card>
    </div>

    <x-card title="Stok per Gudang">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold">Gudang</th>
                    <th class="px-3 py-2 text-right font-semibold">Stok Saat Ini</th>
                    <th class="px-3 py-2 text-right font-semibold">Harga Rata-rata</th>
                    <th class="px-3 py-2 text-right font-semibold">Total Nilai</th>
                    <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($material->warehouseStocks as $stock)
                    <tr>
                        <td class="px-3 py-2">{{ $stock->warehouse->warehouse_name }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($stock->current_stock, 2) }} {{ $material->unit }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($stock->average_price, 0) }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($stock->total_value, 0) }}</td>
                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('stocks.batches', ['material' => $material->material_id, 'warehouse' => $stock->warehouse_id]) }}" class="text-blue-600 hover:text-blue-800">Lihat Batch</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">Belum ada stok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <x-card title="Batch Aktif">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold">Batch Number</th>
                    <th class="px-3 py-2 text-left font-semibold">Tanggal Beli</th>
                    <th class="px-3 py-2 text-right font-semibold">Harga/Unit</th>
                    <th class="px-3 py-2 text-right font-semibold">Qty Masuk</th>
                    <th class="px-3 py-2 text-right font-semibold">Qty Sisa</th>
                    <th class="px-3 py-2 text-right font-semibold">Total Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($material->batches as $batch)
                    <tr>
                        <td class="px-3 py-2">{{ $batch->batch_number }}</td>
                        <td class="px-3 py-2">{{ $batch->purchase_date->format('d M Y') }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($batch->unit_price, 0) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($batch->qty_in, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($batch->qty_remaining, 2) }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($batch->total_value, 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada batch aktif</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
</div>
@endsection