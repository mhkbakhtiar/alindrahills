@extends('layouts.app')

@section('title', 'Mutasi Stok')
@section('breadcrumb', 'Warehouse / Mutasi Stok')

@section('content')
<div class="space-y-4">
    <h2 class="text-lg font-semibold text-gray-900">Riwayat Mutasi Stok</h2>

    <x-card>
        <form method="GET" class="flex gap-3 mb-4">
            <select name="warehouse_id" class="px-3 py-2 text-sm border rounded">
                <option value="">Semua Gudang</option>
                @foreach($warehouses ?? [] as $wh)
                    <option value="{{ $wh->warehouse_id }}" {{ request('warehouse_id') == $wh->warehouse_id ? 'selected' : '' }}>
                        {{ $wh->warehouse_name }}
                    </option>
                @endforeach
            </select>
            
            <select name="type" class="px-3 py-2 text-sm border rounded">
                <option value="">Semua Tipe</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Masuk</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Keluar</option>
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari Tanggal" class="px-3 py-2 text-sm border rounded">
            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai Tanggal" class="px-3 py-2 text-sm border rounded">
            
            <x-button type="submit" variant="secondary">Filter</x-button>
            <x-button type="button" variant="secondary" href="{{ route('mutations.index') }}">Reset</x-button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-center font-semibold">Tipe</th>
                        <th class="px-3 py-2 text-left font-semibold">Material</th>
                        <th class="px-3 py-2 text-left font-semibold">Gudang</th>
                        <th class="px-3 py-2 text-right font-semibold">Qty</th>
                        <th class="px-3 py-2 text-right font-semibold">Stok Sebelum</th>
                        <th class="px-3 py-2 text-right font-semibold">Stok Sesudah</th>
                        <th class="px-3 py-2 text-right font-semibold">Nilai</th>
                        <th class="px-3 py-2 text-left font-semibold">Referensi</th>
                        <th class="px-3 py-2 text-left font-semibold">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($mutations as $mutation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $mutation->created_at->format('d M Y H:i') }}</td>
                            <td class="px-3 py-2 text-center">
                                @if($mutation->mutation_type === 'in')
                                    <x-badge variant="success">Masuk</x-badge>
                                @else
                                    <x-badge variant="danger">Keluar</x-badge>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <div class="font-medium">{{ $mutation->material->material_name }}</div>
                                <div class="text-gray-600">{{ $mutation->material->material_code }}</div>
                            </td>
                            <td class="px-3 py-2">{{ $mutation->warehouse->warehouse_name }}</td>
                            <td class="px-3 py-2 text-right font-medium {{ $mutation->mutation_type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $mutation->mutation_type === 'in' ? '+' : '-' }}{{ number_format($mutation->qty, 2) }}
                            </td>
                            <td class="px-3 py-2 text-right">{{ number_format($mutation->stock_before, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($mutation->stock_after, 2) }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($mutation->total_value, 0) }}</td>
                            <td class="px-3 py-2">
                                <div class="text-xs">{{ ucfirst(str_replace('_', ' ', $mutation->reference_type)) }}</div>
                                <div class="text-gray-600">#{{ $mutation->reference_id }}</div>
                            </td>
                            <td class="px-3 py-2">{{ $mutation->creator->full_name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-3 py-8 text-center text-gray-500">Tidak ada mutasi stok</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $mutations->links() }}
        </div>
    </x-card>

    <!-- Summary -->
    <x-card title="Ringkasan">
        <div class="grid grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-xs text-gray-600">Total Mutasi</p>
                <p class="text-2xl font-bold text-gray-900">{{ $mutations->total() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Masuk</p>
                <p class="text-2xl font-bold text-green-600">{{ $mutations->where('mutation_type', 'in')->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Keluar</p>
                <p class="text-2xl font-bold text-red-600">{{ $mutations->where('mutation_type', 'out')->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Total Nilai</p>
                <p class="text-base font-bold text-blue-600">Rp {{ number_format($mutations->sum('total_value'), 0) }}</p>
            </div>
        </div>
    </x-card>
</div>
@endsection