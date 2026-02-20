{{-- resources/views/settings/prefix/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Master Format Nomor')
@section('breadcrumb', 'Settings / Master Format Nomor')

@section('content')
<div class="space-y-4">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Master Format Nomor</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kelola format dan prefix penomoran dokumen sistem</p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperadmin())
            <a href="{{ route('settings.prefix.create') }}"
                class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Format
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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Total Format</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $prefixes->total() }}</p>
        </div>
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Aktif</p>
            <p class="text-2xl font-bold text-green-600 mt-1">
                {{ $prefixes->getCollection()->where('is_active', true)->count() }}
            </p>
        </div>
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Non-Aktif</p>
            <p class="text-2xl font-bold text-gray-400 mt-1">
                {{ $prefixes->getCollection()->where('is_active', false)->count() }}
            </p>
        </div>
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Sudah Digunakan</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">
                {{ $prefixes->getCollection()->where('nomor_terakhir', '>', 0)->count() }}
            </p>
        </div>
    </div>

    {{-- ── Filter ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white border rounded-lg p-3">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Kode, nama, atau prefix..."
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
                    <a href="{{ route('settings.prefix.index') }}"
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
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Nama Dokumen</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Prefix</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Format</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Contoh Hasil</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">No. Terakhir</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Reset Per</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($prefixes as $prefix)
                        <tr class="hover:bg-gray-50 {{ !$prefix->is_active ? 'opacity-60' : '' }}">
                            <td class="px-4 py-3">
                                <code class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded font-mono font-bold text-xs">
                                    {{ $prefix->kode_jenis }}
                                </code>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $prefix->nama_jenis }}</p>
                                @if($prefix->keterangan)
                                    <p class="text-gray-400 mt-0.5 truncate max-w-xs">{{ $prefix->keterangan }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <code class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded font-mono font-semibold">
                                    {{ $prefix->prefix }}
                                </code>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $parts = array_filter([
                                        $prefix->prefix,
                                        $prefix->format_tahun !== 'none' ? $prefix->format_tahun : null,
                                        $prefix->format_bulan !== 'none' ? $prefix->format_bulan : null,
                                        str_repeat('0', $prefix->panjang_urutan),
                                    ]);
                                @endphp
                                <span class="font-mono text-xs text-gray-500">
                                    {{ implode($prefix->separator, $parts) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <code class="px-2 py-0.5 bg-green-50 text-green-700 rounded font-mono text-xs font-semibold">
                                    {{ $prefix->contoh_hasil ?? $prefix->buildContoh() }}
                                </code>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($prefix->nomor_terakhir > 0)
                                    <span class="font-bold text-blue-600">{{ number_format($prefix->nomor_terakhir) }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $resetColors = [
                                        'bulan' => 'bg-orange-100 text-orange-700',
                                        'tahun' => 'bg-blue-100 text-blue-700',
                                        'never' => 'bg-gray-100 text-gray-600',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $resetColors[$prefix->reset_per] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($prefix->reset_per) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($prefix->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                        Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('settings.prefix.show', $prefix) }}"
                                        class="px-2.5 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700 hover:bg-gray-200">
                                        Detail
                                    </a>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperadmin())
                                        <a href="{{ route('settings.prefix.edit', $prefix) }}"
                                            class="px-2.5 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                            Edit
                                        </a>
                                        @if($prefix->nomor_terakhir === 0)
                                            <form action="{{ route('settings.prefix.destroy', $prefix) }}" method="POST"
                                                onsubmit="return confirm('Hapus prefix {{ $prefix->kode_jenis }}?')">
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
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm">Belum ada master format nomor</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($prefixes->hasPages())
            <div class="px-4 py-3 border-t bg-gray-50">
                {{ $prefixes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection