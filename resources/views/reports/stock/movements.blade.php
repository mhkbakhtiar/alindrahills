@extends('layouts.app')

@section('title', 'Laporan Mutasi Stok')
@section('breadcrumb', 'Laporan / Mutasi Stok')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Laporan Mutasi Stok</h2>
        <div class="flex gap-2">
            <x-button variant="secondary" href="{{ route('reports.stock.index') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </x-button>
            <form action="{{ route('reports.stock.export-movements') }}" method="GET" class="inline">
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
        <form method="GET" action="{{ route('reports.stock.movements') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Mutasi</label>
                <select name="movement_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="in" {{ request('movement_type') == 'in' ? 'selected' : '' }}>Masuk</option>
                    <option value="out" {{ request('movement_type') == 'out' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Material</label>
                <select name="material_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Material</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->material_id }}" {{ request('material_id') == $material->material_id ? 'selected' : '' }}>
                            {{ $material->material_code }} - {{ $material->material_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-5 flex gap-2">
                <x-button variant="primary" type="submit">Filter</x-button>
                <x-button variant="secondary" href="{{ route('reports.stock.movements') }}">Reset</x-button>
            </div>
        </form>
    </x-card>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-card>
            <div class="text-sm text-gray-600">Total Mutasi</div>
            <div class="text-2xl font-bold text-gray-900">{{ $summary['total_movements'] }}</div>
        </x-card>
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600">Total Masuk</div>
                    <div class="text-2xl font-bold text-green-600">{{ number_format($summary['total_in'], 2) }}</div>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                    </svg>
                </div>
            </div>
            <div class="text-xs text-gray-500 mt-1">Nilai: Rp {{ number_format($summary['total_in_value'], 0) }}</div>
        </x-card>
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600">Total Keluar</div>
                    <div class="text-2xl font-bold text-red-600">{{ number_format($summary['total_out'], 2) }}</div>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                    </svg>
                </div>
            </div>
            <div class="text-xs text-gray-500 mt-1">Nilai: Rp {{ number_format($summary['total_out_value'], 0) }}</div>
        </x-card>
    </div>

    <!-- Movement by Type -->
    <x-card>
        <h3 class="text-lg font-semibold mb-4">Ringkasan per Tipe</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Tipe</th>
                        <th class="px-3 py-2 text-center font-semibold">Jumlah Transaksi</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Quantity</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($byType as $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">
                                @if($data['type'] == 'in')
                                    <x-badge variant="success">Masuk</x-badge>
                                @else
                                    <x-badge variant="danger">Keluar</x-badge>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">{{ $data['count'] }}</td>
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

    <!-- Daily Movements -->
    @if(isset($dailyMovements) && $dailyMovements->isNotEmpty())
    <x-card>
        <h3 class="text-lg font-semibold mb-4">Mutasi Harian</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-right font-semibold">Masuk</th>
                        <th class="px-3 py-2 text-right font-semibold">Keluar</th>
                        <th class="px-3 py-2 text-right font-semibold">Selisih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($dailyMovements as $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $data['date'] }}</td>
                            <td class="px-3 py-2 text-right text-green-600">{{ number_format($data['in'], 2) }}</td>
                            <td class="px-3 py-2 text-right text-red-600">{{ number_format($data['out'], 2) }}</td>
                            <td class="px-3 py-2 text-right font-medium {{ ($data['in'] - $data['out']) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($data['in'] - $data['out'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
    @endif

    <!-- Movement Details -->
    <x-card>
        <h3 class="text-lg font-semibold mb-4">Detail Mutasi Stok</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">Material</th>
                        <th class="px-3 py-2 text-left font-semibold">Gudang</th>
                        <th class="px-3 py-2 text-center font-semibold">Tipe</th>
                        <th class="px-3 py-2 text-right font-semibold">Quantity</th>
                        <th class="px-3 py-2 text-right font-semibold">Harga</th>
                        <th class="px-3 py-2 text-right font-semibold">Total</th>
                        <th class="px-3 py-2 text-left font-semibold">Referensi</th>
                        <th class="px-3 py-2 text-left font-semibold">User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($mutations as $mutation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $mutation->created_at->format('d M Y H:i') }}</td>
                            <td class="px-3 py-2">{{ $mutation->material->material_code }} - {{ $mutation->material->material_name }}</td>
                            <td class="px-3 py-2">{{ $mutation->warehouse->warehouse_name }}</td>
                            <td class="px-3 py-2 text-center">
                                @if($mutation->mutation_type == 'in')
                                    <x-badge variant="success">Masuk</x-badge>
                                @else
                                    <x-badge variant="danger">Keluar</x-badge>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">{{ number_format($mutation->qty, 2) }} {{ $mutation->material->unit }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($mutation->unit_price, 0) }}</td>
                            <td class="px-3 py-2 text-right font-medium">Rp {{ number_format($mutation->total_value, 0) }}</td>
                            <td class="px-3 py-2">
                                {{ $mutation->reference_type ?? '-' }}
                                @if($mutation->reference_id)
                                    #{{ $mutation->reference_id }}
                                @endif
                            </td>
                            <td class="px-3 py-2">{{ $mutation->creator->name ?? '-' }}</td>
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