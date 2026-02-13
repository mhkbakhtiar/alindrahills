@extends('layouts.app')

@section('title', 'Kegiatan Proyek')
@section('breadcrumb', 'Project / Kegiatan')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Kegiatan Pembangunan</h2>
        <x-button variant="primary" href="{{ route('activities.create') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Kegiatan
        </x-button>
    </div>

    <x-card>
        <form method="GET" class="flex gap-3 mb-4">
            <select name="status" class="pe-8 py-2 text-sm border rounded">
                <option value="">Semua Status</option>
                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kegiatan..." class="flex-1 px-3 py-2 text-sm border rounded">
            <x-button type="submit" variant="secondary">Filter</x-button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Kegiatan</th>
                        <th class="px-3 py-2 text-left font-semibold">Lokasi</th>
                        <th class="px-3 py-2 text-left font-semibold">Jenis</th>
                        <th class="px-3 py-2 text-left font-semibold">Mulai</th>
                        <th class="px-3 py-2 text-left font-semibold">Selesai</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $activity->activity_code }}</td>
                            <td class="px-3 py-2">{{ $activity->activity_name }}</td>
                            <td class="px-3 py-2">{{ $activity->location->kavling }} - {{ $activity->location->blok }}</td>
                            <td class="px-3 py-2">{{ $activity->activity_type }}</td>
                            <td class="px-3 py-2">{{ $activity->start_date->format('d M Y') }}</td>
                            <td class="px-3 py-2">{{ $activity->end_date ? $activity->end_date->format('d M Y') : '-' }}</td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $statusVariants = [
                                        'planned' => 'default',
                                        'ongoing' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                @endphp
                                <x-badge :variant="$statusVariants[$activity->status]">
                                    {{ ucfirst($activity->status) }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('activities.show', $activity) }}" class="text-blue-600 hover:text-blue-800 mr-2">Detail</a>
                                <a href="{{ route('activities.edit', $activity) }}" class="text-green-600 hover:text-green-800">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-gray-500">Tidak ada kegiatan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $activities->links() }}
        </div>
    </x-card>
</div>
@endsection