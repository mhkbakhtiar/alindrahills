{{-- resources/views/warehouses/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Gudang')
@section('breadcrumb', 'Material / Master Gudang / Edit')

@section('content')
<div class="space-y-4 max-w-2xl">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Edit Gudang</h2>
            <p class="text-xs text-gray-500 mt-0.5">
                <code class="px-1.5 py-0.5 bg-blue-50 text-blue-700 rounded font-mono font-bold">{{ $warehouse->warehouse_code }}</code>
                — {{ $warehouse->warehouse_name }}
            </p>
        </div>
        <a href="{{ route('warehouses.show', $warehouse) }}"
            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <p class="font-semibold mb-1">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('warehouses.update', $warehouse) }}" method="POST">
        @csrf
        @method('PATCH')

        {{-- ── Kode (readonly) ─────────────────────────────────────────────── --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Kode Gudang</h3>
                <span class="text-xs text-gray-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Tidak dapat diubah
                </span>
            </div>
            <div class="p-4 flex items-center gap-4">
                <code class="text-2xl font-bold font-mono text-blue-700 bg-blue-50 px-4 py-2 rounded-lg">
                    {{ $warehouse->warehouse_code }}
                </code>
                <div class="text-xs text-gray-500">
                    <p>Kode digenerate otomatis dari</p>
                    <a href="{{ route('settings.prefix.index') }}" class="text-blue-600 hover:underline font-medium">
                        Master Format Nomor (WHS)
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Form Data ────────────────────────────────────────────────────── --}}
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
                        value="{{ old('warehouse_name', $warehouse->warehouse_name) }}"
                        placeholder="cth: Gudang Utama, Gudang Barat"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('warehouse_name') border-red-400 @enderror"
                        required>
                    @error('warehouse_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Lokasi / Alamat</label>
                    <textarea name="location" rows="2"
                        placeholder="cth: Blok A, Kavling No. 12, Area Proyek Alindra Hills"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('location', $warehouse->location) }}</textarea>
                    @error('location')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="flex items-center justify-between py-2 border-t">
                    <div>
                        <p class="text-xs font-medium text-gray-800">Status Aktif</p>
                        <p class="text-xs text-gray-500 mt-0.5">Gudang non-aktif tidak dapat menerima/mengirim stok</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                            {{ old('is_active', $warehouse->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
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
            <a href="{{ route('warehouses.show', $warehouse) }}"
                class="inline-flex items-center px-4 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection