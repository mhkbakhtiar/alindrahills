{{-- resources/views/warehouses/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Master Gudang')
@section('breadcrumb', 'Material / Master Gudang')

@section('content')
<div class="space-y-4">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Master Gudang</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kelola data gudang penyimpanan material</p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperadmin())
            <a href="{{ route('warehouses.create') }}"
                class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Gudang
            </a>
        @endif
    </div>

    {{-- ── Alerts ───────────────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{!! session('success') !!}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{!! session('error') !!}</span>
        </div>
    @endif

    {{-- ── Summary Cards ────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white border rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Total Gudang</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalAll }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Aktif</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $totalActive }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Non-Aktif</p>
                    <p class="text-2xl font-bold text-gray-400 mt-1">{{ $totalInactive }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filter ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white border rounded-lg p-3">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Kode, nama, atau lokasi..."
                    class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-52">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="is_active" class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-3 py-2 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'is_active']))
                    <a href="{{ route('warehouses.index') }}"
                        class="px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Table ────────────────────────────────────────────────────────────── --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Kode Gudang</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Nama Gudang</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Lokasi</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Jenis Material</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($warehouses as $warehouse)
                        <tr class="hover:bg-gray-50 {{ !$warehouse->is_active ? 'opacity-60' : '' }}">
                            <td class="px-4 py-3">
                                <code class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded font-mono font-bold">
                                    {{ $warehouse->warehouse_code }}
                                </code>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-800">{{ $warehouse->warehouse_name }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($warehouse->location)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $warehouse->location }}
                                    </div>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($warehouse->stocks_count > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        {{ $warehouse->stocks_count }} jenis
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">Kosong</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($warehouse->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        ✓ Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                        Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('warehouses.show', $warehouse) }}"
                                        class="px-2.5 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700 hover:bg-gray-200">
                                        Detail
                                    </a>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperadmin())
                                        <a href="{{ route('warehouses.edit', $warehouse) }}"
                                            class="px-2.5 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                            Edit
                                        </a>
                                        @if($warehouse->stocks_count === 0)
                                            <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST"
                                                onsubmit="return confirm('Hapus gudang {{ $warehouse->warehouse_code }} - {{ $warehouse->warehouse_name }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="px-2.5 py-1 text-xs font-medium rounded bg-red-100 text-red-700 hover:bg-red-200">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500">Belum ada data gudang</p>
                                @if(auth()->user()->isAdmin() || auth()->user()->isSuperadmin())
                                    <a href="{{ route('warehouses.create') }}" class="text-blue-600 hover:underline text-xs mt-1 inline-block">
                                        Tambah gudang pertama →
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($warehouses->hasPages())
            <div class="px-4 py-3 border-t bg-gray-50">
                {{ $warehouses->links() }}
            </div>
        @endif
    </div>

</div>
@endsection