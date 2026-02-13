{{-- resources/views/master/kavling-pembeli/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengaitan Kavling & Pembeli')
@section('breadcrumb', 'Master / Kavling & Pembeli')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Pengaitan Kavling & Pembeli</h2>
        <x-button variant="primary" href="{{ route('master.kavling-pembeli.create') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pengaitan
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
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Kavling</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Blok</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Pembeli</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tgl Booking</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tgl Akad</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Harga Jual</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($kavlingPembeli as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                {{ ($kavlingPembeli->currentPage() - 1) * $kavlingPembeli->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $item->kavling->kavling ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $item->kavling->blok ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-900">
                                {{ $item->pembeli->nama ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $item->tanggal_booking ? $item->tanggal_booking->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $item->tanggal_akad ? $item->tanggal_akad->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600">
                                {{ $item->harga_jual ? 'Rp ' . number_format($item->harga_jual, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $badgeVariant = match($item->status) {
                                        'booking' => 'info',
                                        'akad' => 'warning',
                                        'lunas' => 'success',
                                        'batal' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <x-badge variant="{{ $badgeVariant }}">
                                    {{ ucfirst($item->status) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-1">
                                    <x-button variant="info" href="{{ route('master.kavling-pembeli.show', $item) }}" class="!py-1 !px-2">
                                        Detail
                                    </x-button>
                                    <x-button variant="warning" href="{{ route('master.kavling-pembeli.edit', $item) }}" class="!py-1 !px-2">
                                        Edit
                                    </x-button>
                                    <form action="{{ route('master.kavling-pembeli.destroy', $item) }}" method="POST" 
                                        onsubmit="return confirm('Yakin ingin menghapus pengaitan ini?')" class="inline">
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
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data pengaitan kavling & pembeli
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $kavlingPembeli->links() }}
        </div>
    </x-card>
</div>
@endsection