{{-- resources/views/warehouses/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Gudang')
@section('breadcrumb', 'Material / Master Gudang / Tambah')

@section('content')
<div class="space-y-4 max-w-3xl">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Tambah Gudang Baru</h2>
            <p class="text-xs text-gray-500 mt-0.5">Form isian untuk menambah data gudang</p>
        </div>
        <a href="{{ route('warehouses.index') }}"
            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('warehouses.store') }}" method="POST">
        @csrf

        {{-- ── Form Data Gudang ─────────────────────────────────────────────── --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">Data Gudang</h3>
            </div>
            <div class="p-4 space-y-4">

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Nama Gudang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="warehouse_name"
                        value="{{ old('warehouse_name') }}"
                        placeholder="cth: Gudang Utama, Gudang Barat, Gudang Material A"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('warehouse_name') border-red-400 @enderror"
                        autofocus required>
                    @error('warehouse_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Lokasi / Alamat</label>
                    <textarea name="location" rows="2"
                        placeholder="cth: Blok A, Kavling No. 12, Area Proyek Alindra Hills"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('location') border-red-400 @enderror">{{ old('location') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Opsional — lokasi atau deskripsi posisi gudang</p>
                    @error('location')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status toggle --}}
                <div class="flex items-center justify-between py-2 border-t">
                    <div>
                        <p class="text-xs font-medium text-gray-800">Status Aktif</p>
                        <p class="text-xs text-gray-500 mt-0.5">Gudang non-aktif tidak dapat menerima/mengirim stok</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                            {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300
                            rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white
                            after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white
                            after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ── Actions ──────────────────────────────────────────────────────── --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('warehouses.index') }}"
                class="inline-flex items-center px-4 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Gudang
            </button>
        </div>

    </form>
</div>
@endsection