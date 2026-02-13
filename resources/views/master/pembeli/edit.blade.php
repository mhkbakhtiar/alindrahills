{{-- resources/views/master/pembeli/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Pembeli')
@section('breadcrumb', 'Master / Pembeli / Edit')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Edit Pembeli</h2>
        <x-button variant="secondary" href="{{ route('master.pembeli.index') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </x-button>
    </div>

    <x-card>
        <form action="{{ route('master.pembeli.update', $pembeli) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="nama" value="{{ old('nama', $pembeli->nama) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama') border-red-500 @enderror" 
                        required autofocus>
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $pembeli->email) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Telepon</label>
                    <input type="text" name="telepon" value="{{ old('telepon', $pembeli->telepon) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('telepon') border-red-500 @enderror">
                    @error('telepon')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Identitas (KTP/SIM/Passport)</label>
                    <input type="text" name="no_identitas" value="{{ old('no_identitas', $pembeli->no_identitas) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('no_identitas') border-red-500 @enderror">
                    @error('no_identitas')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="3" 
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('alamat') border-red-500 @enderror">{{ old('alamat', $pembeli->alamat) }}</textarea>
                    @error('alamat')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $pembeli->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-xs text-gray-700">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <x-button type="button" variant="secondary" href="{{ route('master.pembeli.index') }}">
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