@extends('layouts.app')

@section('title', 'Detail Lokasi Proyek')
@section('breadcrumb', 'Project / Lokasi Proyek / Detail')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Detail Lokasi Proyek</h2>
        <div class="flex gap-2">
            <x-button variant="secondary" href="{{ route('locations.edit', $location) }}">Edit</x-button>
            <x-button variant="secondary" href="{{ route('locations.index') }}">Kembali</x-button>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <x-card title="Informasi Lokasi" class="col-span-2">
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="font-semibold text-gray-700">Kavling</dt>
                    <dd class="text-gray-900 text-lg font-bold">{{ $location->kavling }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Blok</dt>
                    <dd class="text-gray-900 text-lg font-bold">{{ $location->blok }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="font-semibold text-gray-700">Alamat Lengkap</dt>
                    <dd class="text-gray-900">{{ $location->address ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Status</dt>
                    <dd>
                        <x-badge :variant="$location->is_active ? 'success' : 'danger'">
                            {{ $location->is_active ? 'Active' : 'Inactive' }}
                        </x-badge>
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Dibuat</dt>
                    <dd class="text-gray-900">{{ $location->created_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card title="Statistik">
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-700">Total Kegiatan:</span>
                    <span class="font-bold text-gray-900">{{ $location->activities->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Sedang Berjalan:</span>
                    <span class="font-bold text-green-600">{{ $location->activities->where('status', 'ongoing')->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Selesai:</span>
                    <span class="font-bold text-blue-600">{{ $location->activities->where('status', 'completed')->count() }}</span>
                </div>
            </div>
        </x-card>
    </div>

    <x-card title="Riwayat Kegiatan">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold">Kode</th>
                    <th class="px-3 py-2 text-left font-semibold">Nama Kegiatan</th>
                    <th class="px-3 py-2 text-left font-semibold">Jenis</th>
                    <th class="px-3 py-2 text-left font-semibold">Periode</th>
                    <th class="px-3 py-2 text-center font-semibold">Status</th>
                    <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($location->activities as $activity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium">{{ $activity->activity_code }}</td>
                        <td class="px-3 py-2">{{ $activity->activity_name }}</td>
                        <td class="px-3 py-2">{{ $activity->activity_type }}</td>
                        <td class="px-3 py-2">{{ $activity->start_date->format('d M Y') }} - {{ $activity->end_date ? $activity->end_date->format('d M Y') : 'Ongoing' }}</td>
                        <td class="px-3 py-2 text-center">
                            @php
                                $statusVariants = ['planned' => 'default', 'ongoing' => 'warning', 'completed' => 'success', 'cancelled' => 'danger'];
                            @endphp
                            <x-badge :variant="$statusVariants[$activity->status]">{{ ucfirst($activity->status) }}</x-badge>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('activities.show', $activity) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-gray-500">Belum ada kegiatan di lokasi ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
</div>
@endsection