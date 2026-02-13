{{-- resources/views/master/kavling-pembeli/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Pengaitan Kavling & Pembeli')
@section('breadcrumb', 'Master / Kavling & Pembeli / Edit')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Edit Pengaitan Kavling & Pembeli</h2>
        <x-button variant="secondary" href="{{ route('master.kavling-pembeli.index') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </x-button>
    </div>

    <x-card>
        <form action="{{ route('master.kavling-pembeli.update', $kavlingPembeli) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kavling *</label>
                    <select name="location_id" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('location_id') border-red-500 @enderror" required>
                        <option value="">-- Pilih Kavling --</option>
                        @foreach ($kavlings as $kavling)
                            <option value="{{ $kavling->location_id }}" 
                                {{ old('location_id', $kavlingPembeli->location_id) == $kavling->location_id ? 'selected' : '' }}>
                                {{ $kavling->kavling }} - {{ $kavling->blok }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pembeli *</label>
                    <select name="user_id" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('user_id') border-red-500 @enderror" required>
                        <option value="">-- Pilih Pembeli --</option>
                        @foreach ($pembeli as $p)
                            <option value="{{ $p->user_id }}" 
                                {{ old('user_id', $kavlingPembeli->user_id) == $p->user_id ? 'selected' : '' }}>
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Booking</label>
                    <input type="date" name="tanggal_booking" 
                        value="{{ old('tanggal_booking', $kavlingPembeli->tanggal_booking?->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tanggal_booking') border-red-500 @enderror">
                    @error('tanggal_booking')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Akad</label>
                    <input type="date" name="tanggal_akad" 
                        value="{{ old('tanggal_akad', $kavlingPembeli->tanggal_akad?->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tanggal_akad') border-red-500 @enderror">
                    @error('tanggal_akad')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga Jual</label>
                    <input type="number" name="harga_jual" 
                        value="{{ old('harga_jual', $kavlingPembeli->harga_jual) }}" 
                        step="0.01" min="0"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('harga_jual') border-red-500 @enderror">
                    @error('harga_jual')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror" required>
                        <option value="booking" {{ old('status', $kavlingPembeli->status) == 'booking' ? 'selected' : '' }}>Booking</option>
                        <option value="akad" {{ old('status', $kavlingPembeli->status) == 'akad' ? 'selected' : '' }}>Akad</option>
                        <option value="lunas" {{ old('status', $kavlingPembeli->status) == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="batal" {{ old('status', $kavlingPembeli->status) == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="3" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $kavlingPembeli->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <x-button type="button" variant="secondary" href="{{ route('master.kavling-pembeli.index') }}">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection