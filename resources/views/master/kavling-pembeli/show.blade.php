{{-- resources/views/master/kavling-pembeli/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Pengaitan Kavling & Pembeli')
@section('breadcrumb', 'Master / Kavling & Pembeli / Detail')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Detail Pengaitan Kavling & Pembeli</h2>
        <div class="flex gap-2">
            <x-button variant="warning" href="{{ route('master.kavling-pembeli.edit', $kavlingPembeli) }}">
                Edit
            </x-button>
            <x-button variant="secondary" href="{{ route('master.kavling-pembeli.index') }}">
                Kembali
            </x-button>
        </div>
    </div>

    {{-- Informasi Kavling --}}
    <x-card>
        <h3 class="text-sm font-semibold mb-4">Informasi Kavling</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
                <label class="block font-medium text-gray-500 mb-1">Kode Kavling</label>
                <p class="text-gray-900 font-medium">{{ $kavlingPembeli->kavling->kavling ?? '-' }}</p>
            </div>

            <div>
                <label class="block font-medium text-gray-500 mb-1">Blok</label>
                <p class="text-gray-900">{{ $kavlingPembeli->kavling->blok ?? '-' }}</p>
            </div>

            <div>
                <label class="block font-medium text-gray-500 mb-1">Alamat</label>
                <p class="text-gray-900">{{ $kavlingPembeli->kavling->address ?? '-' }}</p>
            </div>
        </div>
    </x-card>

    {{-- Informasi Pembeli --}}
    <x-card>
        <h3 class="text-sm font-semibold mb-4">Informasi Pembeli</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
                <label class="block font-medium text-gray-500 mb-1">Nama Pembeli</label>
                <p class="text-gray-900 font-medium">{{ $kavlingPembeli->pembeli->nama ?? '-' }}</p>
            </div>

            <div>
                <label class="block font-medium text-gray-500 mb-1">Telepon</label>
                <p class="text-gray-900">{{ $kavlingPembeli->pembeli->telepon ?? '-' }}</p>
            </div>

            <div>
                <label class="block font-medium text-gray-500 mb-1">Email</label>
                <p class="text-gray-900">{{ $kavlingPembeli->pembeli->email ?? '-' }}</p>
            </div>

            <div>
                <label class="block font-medium text-gray-500 mb-1">No. Identitas</label>
                <p class="text-gray-900">{{ $kavlingPembeli->pembeli->no_identitas ?? '-' }}</p>
            </div>

            <div class="md:col-span-2">
                <label class="block font-medium text-gray-500 mb-1">Alamat</label>
                <p class="text-gray-900">{{ $kavlingPembeli->pembeli->alamat ?? '-' }}</p>
            </div>
        </div>
    </x-card>

    {{-- Detail Transaksi --}}
    <x-card>
        <h3 class="text-sm font-semibold mb-4">Detail Transaksi</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
                <label class="block font-medium text-gray-500 mb-1">Tanggal Booking</label>
                <p class="text-gray-900">
                    {{ $kavlingPembeli->tanggal_booking ? $kavlingPembeli->tanggal_booking->format('d F Y') : '-' }}
                </p>
            </div>

            <div>
                <label class="block font-medium text-gray-500 mb-1">Tanggal Akad</label>
                <p class="text-gray-900">
                    {{ $kavlingPembeli->tanggal_akad ? $kavlingPembeli->tanggal_akad->format('d F Y') : '-' }}
                </p>
            </div>

            <div>
                <label class="block font-medium text-gray-500 mb-1">Harga Jual</label>
                <p class="text-gray-900 font-semibold text-lg">
                    {{ $kavlingPembeli->harga_jual ? 'Rp ' . number_format($kavlingPembeli->harga_jual, 0, ',', '.') : '-' }}
                </p>
            </div>

            <div>
                <label class="block font-medium text-gray-500 mb-1">Status</label>
                <div>
                    @php
                        $badgeVariant = match($kavlingPembeli->status) {
                            'booking' => 'info',
                            'akad' => 'warning',
                            'lunas' => 'success',
                            'batal' => 'danger',
                            default => 'secondary'
                        };
                    @endphp
                    <x-badge variant="{{ $badgeVariant }}">
                        {{ ucfirst($kavlingPembeli->status) }}
                    </x-badge>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block font-medium text-gray-500 mb-1">Keterangan</label>
                <p class="text-gray-900">{{ $kavlingPembeli->keterangan ?? '-' }}</p>
            </div>
        </div>
    </x-card>

    {{-- Timeline Status --}}
    <x-card>
        <h3 class="text-sm font-semibold mb-4">Timeline</h3>
        
        <div class="space-y-4 text-xs">
            @if($kavlingPembeli->tanggal_booking)
            <div class="flex items-start">
                <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-blue-500"></div>
                <div class="ml-3">
                    <p class="font-medium text-gray-900">Booking</p>
                    <p class="text-gray-500">{{ $kavlingPembeli->tanggal_booking->format('d F Y') }}</p>
                </div>
            </div>
            @endif

            @if($kavlingPembeli->tanggal_akad)
            <div class="flex items-start">
                <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-yellow-500"></div>
                <div class="ml-3">
                    <p class="font-medium text-gray-900">Akad</p>
                    <p class="text-gray-500">{{ $kavlingPembeli->tanggal_akad->format('d F Y') }}</p>
                </div>
            </div>
            @endif

            @if($kavlingPembeli->status == 'lunas')
            <div class="flex items-start">
                <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-green-500"></div>
                <div class="ml-3">
                    <p class="font-medium text-gray-900">Lunas</p>
                    <p class="text-gray-500">{{ $kavlingPembeli->updated_at->format('d F Y') }}</p>
                </div>
            </div>
            @endif

            @if($kavlingPembeli->status == 'batal')
            <div class="flex items-start">
                <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-red-500"></div>
                <div class="ml-3">
                    <p class="font-medium text-gray-900">Batal</p>
                    <p class="text-gray-500">{{ $kavlingPembeli->updated_at->format('d F Y') }}</p>
                </div>
            </div>
            @endif
        </div>
    </x-card>

    {{-- Metadata --}}
    <x-card>
        <h3 class="text-sm font-semibold mb-4">Informasi Sistem</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-medium text-gray-500 mb-1">Dibuat pada</label>
                <p class="text-gray-900">{{ $kavlingPembeli->created_at->format('d F Y H:i') }}</p>
            </div>

            <div>
                <label class="block font-medium text-gray-500 mb-1">Terakhir diupdate</label>
                <p class="text-gray-900">{{ $kavlingPembeli->updated_at->format('d F Y H:i') }}</p>
            </div>
        </div>
    </x-card>
</div>
@endsection