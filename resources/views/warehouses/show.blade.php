{{-- resources/views/warehouses/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Gudang')
@section('breadcrumb', 'Material / Master Gudang / Detail')

@section('content')
<div class="space-y-4">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Detail Gudang</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $warehouse->warehouse_name }}</p>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperadmin())
                <a href="{{ route('warehouses.edit', $warehouse) }}"
                    class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-yellow-500 text-white hover:bg-yellow-600">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            @endif
            <a href="{{ route('warehouses.index') }}"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>


    {{-- ── Hero Card ────────────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-slate-700 to-slate-900 rounded-xl p-6 shadow-sm">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-white bg-opacity-10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <code class="text-blue-300 font-mono font-bold text-sm">{{ $warehouse->warehouse_code }}</code>
                    <h3 class="text-white text-xl font-bold mt-0.5">{{ $warehouse->warehouse_name }}</h3>
                    @if($warehouse->location)
                        <div class="flex items-center gap-1.5 mt-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-slate-300 text-xs">{{ $warehouse->location }}</span>
                        </div>
                    @endif
                </div>
            </div>
            <div>
                @if($warehouse->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-400 bg-opacity-20 text-green-300">
                        ✓ Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-400 bg-opacity-20 text-red-300">
                        ✗ Non-Aktif
                    </span>
                @endif
            </div>
        </div>

        {{-- Stats row --}}
        <div class="grid grid-cols-3 gap-3 mt-5">
            <div class="bg-white bg-opacity-5 rounded-lg p-3 text-center">
                <p class="text-slate-400 text-xs">Jenis Material</p>
                <p class="text-white text-xl font-bold mt-0.5">{{ $warehouse->stocks->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-5 rounded-lg p-3 text-center">
                <p class="text-slate-400 text-xs">Total Stok (Unit)</p>
                <p class="text-white text-xl font-bold mt-0.5">
                    {{ number_format($warehouse->stocks->sum('qty_on_hand'), 2) }}
                </p>
            </div>
            <div class="bg-white bg-opacity-5 rounded-lg p-3 text-center">
                <p class="text-slate-400 text-xs">Stok Kritis</p>
                @php
                    $critical = $warehouse->stocks->filter(fn($s) => $s->qty_on_hand <= ($s->minimum_stock ?? 0));
                @endphp
                <p class="text-xl font-bold mt-0.5 {{ $critical->count() > 0 ? 'text-red-400' : 'text-white' }}">
                    {{ $critical->count() }}
                </p>
            </div>
        </div>
    </div>

    {{-- ── Info Dasar ───────────────────────────────────────────────────────── --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b">
            <h3 class="text-sm font-semibold text-gray-800">Informasi Gudang</h3>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
                <p class="text-gray-500">Kode Gudang</p>
                <code class="font-mono font-bold text-blue-700 text-sm mt-0.5 block">{{ $warehouse->warehouse_code }}</code>
            </div>
            <div>
                <p class="text-gray-500">Nama Gudang</p>
                <p class="font-semibold text-gray-800 mt-0.5">{{ $warehouse->warehouse_name }}</p>
            </div>
            <div>
                <p class="text-gray-500">Status</p>
                <div class="mt-0.5">
                    @if($warehouse->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">✓ Aktif</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Non-Aktif</span>
                    @endif
                </div>
            </div>
            @if($warehouse->location)
                <div class="md:col-span-3">
                    <p class="text-gray-500">Lokasi</p>
                    <div class="flex items-start gap-1.5 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="font-medium text-gray-800">{{ $warehouse->location }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Daftar Stok Material ─────────────────────────────────────────────── --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Stok Material di Gudang Ini</h3>
            @if($warehouse->stocks->count() > 0)
                <span class="text-xs text-gray-500">{{ $warehouse->stocks->count() }} jenis material</span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Nama Material</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Satuan</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Qty di Tangan</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Stok Minimum</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Status Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($warehouse->stocks as $index => $stock)
                        @php
                            $isCritical = $stock->qty_on_hand <= ($stock->minimum_stock ?? 0);
                            $isEmpty    = $stock->qty_on_hand <= 0;
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $isEmpty ? 'bg-red-50' : ($isCritical ? 'bg-yellow-50' : '') }}">
                            <td class="px-4 py-3 text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <code class="font-mono font-medium text-blue-600">
                                    {{ $stock->material->material_code ?? '—' }}
                                </code>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $stock->material->material_name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">
                                {{ $stock->material->unit ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold {{ $isEmpty ? 'text-red-600' : ($isCritical ? 'text-yellow-600' : 'text-gray-800') }}">
                                {{ number_format($stock->qty_on_hand, 2) }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-500">
                                {{ $stock->minimum_stock ? number_format($stock->minimum_stock, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($isEmpty)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        ✗ Habis
                                    </span>
                                @elseif($isCritical)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        ⚠ Kritis
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        ✓ Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">Gudang ini belum memiliki stok material</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($warehouse->stocks->count() > 0)
                    <tfoot class="bg-gray-50 border-t-2">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-700">TOTAL STOK:</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">
                                {{ number_format($warehouse->stocks->sum('qty_on_hand'), 2) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ── Zona Hapus (Admin & stok kosong) ───────────────────────────────── --}}
    @if((auth()->user()->isAdmin() || auth()->user()->isSuperadmin()) && $warehouse->stocks->count() === 0)
        <div class="bg-white border border-red-200 rounded-lg overflow-hidden">
            <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                <h3 class="text-sm font-semibold text-red-800">⚠️ Zona Berbahaya</h3>
            </div>
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-800">Hapus Gudang</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Gudang dapat dihapus karena belum memiliki stok material.
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST"
                    onsubmit="return confirm('Hapus gudang {{ $warehouse->warehouse_code }} - {{ $warehouse->warehouse_name }}?\n\nTindakan ini tidak dapat dibatalkan!')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Gudang
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection