{{-- resources/views/accounting/tahun-anggaran/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Tahun Anggaran')
@section('breadcrumb', 'Accounting / Tahun Anggaran / Edit')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Edit Tahun Anggaran {{ $tahunAnggaran->tahun }}</h2>
        <x-button variant="secondary" href="{{ route('accounting.tahun-anggaran.index') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </x-button>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('accounting.tahun-anggaran.update', $tahunAnggaran) }}" method="POST">
        @csrf
        @method('PUT')
        <x-card>
            <h3 class="text-sm font-semibold mb-4">Informasi Tahun Anggaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tahun *</label>
                    <input type="number" name="tahun" value="{{ old('tahun', $tahunAnggaran->tahun) }}"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tahun') border-red-400 @enderror"
                        required>
                    @error('tahun')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <div class="px-3 py-2">
                        <x-badge :variant="$tahunAnggaran->status === 'aktif' ? 'success' : 'secondary'">
                            {{ ucfirst($tahunAnggaran->status) }}
                        </x-badge>
                        <p class="text-xs text-gray-400 mt-1">Status diubah melalui tombol Aktifkan / Tutup Buku</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Periode Awal *</label>
                    <input type="date" name="periode_awal" value="{{ old('periode_awal', $tahunAnggaran->periode_awal->format('Y-m-d')) }}"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('periode_awal') border-red-400 @enderror"
                        required>
                    @error('periode_awal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Periode Akhir *</label>
                    <input type="date" name="periode_akhir" value="{{ old('periode_akhir', $tahunAnggaran->periode_akhir->format('Y-m-d')) }}"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('periode_akhir') border-red-400 @enderror"
                        required>
                    @error('periode_akhir')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan', $tahunAnggaran->keterangan) }}</textarea>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex justify-end gap-2">
                <x-button type="button" variant="secondary" href="{{ route('accounting.tahun-anggaran.index') }}">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update
                </x-button>
            </div>
        </x-card>
    </form>
</div>
@endsection