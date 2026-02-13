@extends('layouts.app')

@section('title', 'Lokasi Proyek')
@section('breadcrumb', 'Project / Lokasi Proyek')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Lokasi Proyek (Kavling & Blok)</h2>
        <x-button variant="primary" href="{{ route('locations.create') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Lokasi
        </x-button>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Kavling</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Blok</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Alamat</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-700">Status</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-700">Dibuat</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($locations as $location)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $location->kavling }}</td>
                            <td class="px-3 py-2 font-medium">{{ $location->blok }}</td>
                            <td class="px-3 py-2">{{ $location->address ?: '-' }}</td>
                            <td class="px-3 py-2 text-center">
                                <x-badge :variant="$location->is_active ? 'success' : 'danger'">
                                    {{ $location->is_active ? 'Active' : 'Inactive' }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-center">{{ $location->created_at->format('d M Y') }}</td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('locations.edit', $location) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                    <form method="POST" action="{{ route('locations.destroy', $location) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin ingin menghapus lokasi ini?')" class="text-red-600 hover:text-red-800">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-gray-500">Tidak ada data lokasi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $locations->links() }}
        </div>
    </x-card>

    <!-- Summary Card -->
    <x-card title="Ringkasan">
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-xs text-gray-600">Total Lokasi</p>
                <p class="text-2xl font-bold text-gray-900">{{ $locations->total() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Aktif</p>
                <p class="text-2xl font-bold text-green-600">{{ $locations->where('is_active', true)->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600">Tidak Aktif</p>
                <p class="text-2xl font-bold text-red-600">{{ $locations->where('is_active', false)->count() }}</p>
            </div>
        </div>
    </x-card>
</div>
@endsection