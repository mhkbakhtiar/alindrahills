@extends('layouts.app')

@section('title', 'Penggunaan Material')
@section('breadcrumb', 'Project / Penggunaan Material')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Penggunaan Material</h2>
        <x-button variant="primary" href="{{ route('material-usages.create') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Catat Pengeluaran
        </x-button>
    </div>

    <x-card>
        <form method="GET" class="flex gap-3 mb-4">
            <select name="activity_id" class="px-3 py-2 text-sm border rounded">
                <option value="">Semua Kegiatan</option>
                @foreach($activities as $act)
                    <option value="{{ $act->activity_id }}" {{ request('activity_id') == $act->activity_id ? 'selected' : '' }}>
                        {{ $act->activity_code }} - {{ $act->activity_name }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari Tanggal" class="px-3 py-2 text-sm border rounded">
            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai Tanggal" class="px-3 py-2 text-sm border rounded">
            <x-button type="submit" variant="secondary">Filter</x-button>
            <x-button type="button" variant="secondary" href="{{ route('material-usages.index') }}">Reset</x-button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No. Pengeluaran</th>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">Kegiatan</th>
                        <th class="px-3 py-2 text-left font-semibold">Lokasi</th>
                        <th class="px-3 py-2 text-left font-semibold">Gudang</th>
                        <th class="px-3 py-2 text-center font-semibold">Jml Item</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Nilai</th>
                        <th class="px-3 py-2 text-left font-semibold">Dikeluarkan Oleh</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($usages as $usage)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $usage->usage_number }}</td>
                            <td class="px-3 py-2">{{ $usage->usage_date->format('d M Y') }}</td>
                            <td class="px-3 py-2">
                                <div class="font-medium">{{ $usage->activity->activity_name }}</div>
                                <div class="text-gray-600">{{ $usage->activity->activity_code }}</div>
                            </td>
                            <td class="px-3 py-2">{{ $usage->activity->location->kavling }} - {{ $usage->activity->location->blok }}</td>
                            <td class="px-3 py-2">{{ $usage->warehouse->warehouse_name }}</td>
                            <td class="px-3 py-2 text-center">{{ $usage->details->count() }}</td>
                            <td class="px-3 py-2 text-right font-medium">Rp {{ number_format($usage->total_value, 0) }}</td>
                            <td class="px-3 py-2">{{ $usage->issuer->full_name }}</td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('material-usages.show', $usage) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-8 text-center text-gray-500">Tidak ada data pengeluaran</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $usages->links() }}
        </div>
    </x-card>

    <!-- Summary Card -->
    <x-card title="Ringkasan">
        <div class="grid grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-xs text-gray-600">Total Pengeluaran</p>
                <p class="text-2xl font-bold text-gray-900">{{ $usages->total() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Bulan Ini</p>
                <p class="text-2xl font-bold text-blue-600">{{ $usages->where('usage_date', '>=', now()->startOfMonth())->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Total Nilai (halaman ini)</p>
                <p class="text-base font-bold text-green-600">Rp {{ number_format($usages->sum('total_value'), 0) }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Kegiatan Aktif</p>
                <p class="text-2xl font-bold text-orange-600">{{ $activities->count() }}</p>
            </div>
        </div>
    </x-card>
</div>
@endsection