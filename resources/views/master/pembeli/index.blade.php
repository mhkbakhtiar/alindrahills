{{-- resources/views/master/pembeli/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Pembeli')
@section('breadcrumb', 'Master / Pembeli')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Data Pembeli</h2>
        <x-button variant="primary" href="{{ route('master.pembeli.create') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pembeli
        </x-button>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Telepon</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Identitas</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Jumlah Kavling</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pembeli as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                {{ ($pembeli->currentPage() - 1) * $pembeli->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->telepon ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->no_identitas ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <x-badge variant="{{ $item->kavlings_count > 0 ? 'success' : 'secondary' }}">
                                    {{ $item->kavlings_count }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-badge variant="{{ $item->is_active ? 'success' : 'danger' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-1">
                                    <x-button variant="info" href="{{ route('master.pembeli.show', $item) }}" class="!py-1 !px-2">
                                        Detail
                                    </x-button>
                                    <x-button variant="warning" href="{{ route('master.pembeli.edit', $item) }}" class="!py-1 !px-2">
                                        Edit
                                    </x-button>
                                    <form action="{{ route('master.pembeli.destroy', $item) }}" method="POST" 
                                        onsubmit="return confirm('Yakin ingin menghapus pembeli ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="danger" class="!py-1 !px-2">
                                            Hapus
                                        </x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data pembeli
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $pembeli->links() }}
        </div>
    </x-card>
</div>
@endsection