@extends('layouts.app')

@section('title', 'Laporan Kegiatan')
@section('breadcrumb', 'Laporan / Kegiatan')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Laporan Kegiatan Proyek</h2>
        <form action="{{ route('reports.activities.export') }}" method="GET" class="inline">
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

    <!-- Filter Form -->
    <x-card>
        <form method="GET" action="{{ route('reports.activities.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                <select name="location_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->location_id }}" {{ request('location_id') == $location->location_id ? 'selected' : '' }}>
                            {{ $location->location_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kegiatan</label>
                <select name="activity_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Tipe</option>
                    @foreach($activityTypes as $type)
                        <option value="{{ $type }}" {{ request('activity_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kontraktor</label>
                <select name="contractor_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kontraktor</option>
                    @foreach($contractors as $contractor)
                        <option value="{{ $contractor->contractor_id }}" {{ request('contractor_id') == $contractor->contractor_id ? 'selected' : '' }}>
                            {{ $contractor->contractor_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3 flex gap-2">
                <x-button variant="primary" type="submit">Filter</x-button>
                <x-button variant="secondary" href="{{ route('reports.activities.index') }}">Reset</x-button>
            </div>
        </form>
    </x-card>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-card>
            <div class="text-sm text-gray-600">Total Kegiatan</div>
            <div class="text-2xl font-bold text-gray-900">{{ $summary['total_activities'] }}</div>
        </x-card>
        <x-card>
            <div class="text-sm text-gray-600">Ongoing</div>
            <div class="text-2xl font-bold text-blue-600">{{ $summary['ongoing'] }}</div>
        </x-card>
        <x-card>
        <div class="text-sm text-gray-600">Completed</div>
            <div class="text-2xl font-bold text-green-600">{{ $summary['completed'] }}</div>
        </x-card>
        <x-card>
            <div class="text-sm text-gray-600">Total Tukang</div>
            <div class="text-2xl font-bold text-purple-600">{{ $summary['total_workers'] }}</div>
        </x-card>
    </div>

    <!-- Activity List -->
    <x-card>
        <h3 class="text-lg font-semibold mb-4">Daftar Kegiatan</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Kegiatan</th>
                        <th class="px-3 py-2 text-left font-semibold">Lokasi</th>
                        <th class="px-3 py-2 text-left font-semibold">Tipe</th>
                        <th class="px-3 py-2 text-left font-semibold">Periode</th>
                        <th class="px-3 py-2 text-center font-semibold">Tukang</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $activity->activity_code }}</td>
                            <td class="px-3 py-2">{{ $activity->activity_name }}</td>
                            <td class="px-3 py-2">{{ $activity->location->location_name ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $activity->activity_type }}</td>
                            <td class="px-3 py-2">{{ $activity->start_date->format('d M Y') }} - {{ $activity->end_date?->format('d M Y') ?? 'N/A' }}</td>
                            <td class="px-3 py-2 text-center">{{ $activity->activityWorkers->count() }}</td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $statusVariants = ['planned' => 'secondary', 'ongoing' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
                                @endphp
                                <x-badge :variant="$statusVariants[$activity->status]">{{ ucfirst($activity->status) }}</x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Summary by Location -->
    <x-card>
        <h3 class="text-lg font-semibold mb-4">Ringkasan per Lokasi</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Lokasi</th>
                        <th class="px-3 py-2 text-center font-semibold">Total</th>
                        <th class="px-3 py-2 text-center font-semibold">Planned</th>
                        <th class="px-3 py-2 text-center font-semibold">Ongoing</th>
                        <th class="px-3 py-2 text-center font-semibold">Completed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($byLocation as $data)
                        <tr>
                            <td class="px-3 py-2">{{ $data['location'] }}</td>
                            <td class="px-3 py-2 text-center font-medium">{{ $data['count'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $data['planned'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $data['ongoing'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $data['completed'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection