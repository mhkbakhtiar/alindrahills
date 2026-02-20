{{-- resources/views/settings/prefix/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Format Nomor')
@section('breadcrumb', 'Settings / Master Format Nomor / Detail')

@section('content')
<div class="space-y-4 max-w-4xl flex flex-col mx-auto">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Detail Format Nomor</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $prefix->nama_jenis }}</p>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperadmin())
                <a href="{{ route('settings.prefix.edit', $prefix) }}"
                    class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-yellow-500 text-white hover:bg-yellow-600">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            @endif
            <a href="{{ route('settings.prefix.index') }}"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{!! session('success') !!}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            {!! session('error') !!}
        </div>
    @endif

    {{-- ── Preview Besar ────────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl p-6 text-center shadow-lg">
        <p class="text-blue-200 text-xs font-medium mb-2 uppercase tracking-wider">Contoh Nomor Berikutnya</p>
        <code class="text-3xl font-bold font-mono text-white tracking-widest block">
            {{ $prefix->contoh_hasil ?? $prefix->buildContoh() }}
        </code>
        <div class="flex items-center justify-center gap-4 mt-3">
            <span class="text-blue-200 text-xs">
                Nomor terakhir: <strong class="text-white">{{ number_format($prefix->nomor_terakhir) }}</strong>
            </span>
            <span class="text-blue-400">•</span>
            <span class="text-blue-200 text-xs">
                Berikutnya: <strong class="text-white">{{ $prefix->nomor_terakhir + 1 }}</strong>
            </span>
        </div>
        <div class="mt-3">
            @if($prefix->is_active)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-400 bg-opacity-30 text-green-100">
                    ✓ Aktif — Siap digunakan
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-400 bg-opacity-30 text-red-100">
                    ✗ Non-Aktif
                </span>
            @endif
        </div>
    </div>

    {{-- ── Informasi Dasar ──────────────────────────────────────────────────── --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b">
            <h3 class="text-sm font-semibold text-gray-800">Informasi Dasar</h3>
        </div>
        <div class="p-4">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <dt class="text-gray-500 mb-0.5">Kode Jenis</dt>
                    <dd>
                        <code class="px-2 py-1 bg-blue-50 text-blue-700 rounded font-mono font-bold text-sm">
                            {{ $prefix->kode_jenis }}
                        </code>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 mb-0.5">Prefix / Awalan</dt>
                    <dd>
                        <code class="px-2 py-1 bg-gray-100 text-gray-700 rounded font-mono font-semibold text-sm">
                            {{ $prefix->prefix }}
                        </code>
                    </dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-gray-500 mb-0.5">Nama Lengkap Dokumen</dt>
                    <dd class="font-semibold text-gray-800 text-sm">{{ $prefix->nama_jenis }}</dd>
                </div>
                @if($prefix->keterangan)
                    <div class="md:col-span-2">
                        <dt class="text-gray-500 mb-0.5">Keterangan</dt>
                        <dd class="text-gray-700">{{ $prefix->keterangan }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- ── Konfigurasi Format ───────────────────────────────────────────────── --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b">
            <h3 class="text-sm font-semibold text-gray-800">Konfigurasi Format</h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs mb-4">
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-gray-500">Format Tahun</p>
                    <code class="font-mono font-bold text-gray-800 text-sm mt-1 block">
                        {{ $prefix->format_tahun === 'none' ? '—' : $prefix->format_tahun }}
                    </code>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-gray-500">Format Bulan</p>
                    <code class="font-mono font-bold text-gray-800 text-sm mt-1 block">
                        {{ $prefix->format_bulan === 'none' ? '—' : $prefix->format_bulan }}
                    </code>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-gray-500">Pemisah</p>
                    <code class="font-mono font-bold text-gray-800 text-sm mt-1 block">
                        {{ $prefix->separator === '' ? '(kosong)' : '"' . $prefix->separator . '"' }}
                    </code>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-gray-500">Panjang Urutan</p>
                    <code class="font-mono font-bold text-gray-800 text-sm mt-1 block">
                        {{ $prefix->panjang_urutan }} digit
                    </code>
                </div>
            </div>

            {{-- Visualisasi struktur nomor --}}
            <div class="border border-dashed border-gray-300 rounded-lg p-4">
                <p class="text-xs text-gray-500 mb-3 text-center">Struktur Nomor</p>
                <div class="flex items-center justify-center gap-1 flex-wrap">
                    @php
                        $now = \Carbon\Carbon::now();
                        $sep = $prefix->separator;
                    @endphp

                    <div class="text-center">
                        <div class="px-3 py-2 bg-blue-600 text-white rounded font-mono text-xs font-bold">
                            {{ $prefix->prefix }}
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Prefix</p>
                    </div>

                    @if($prefix->format_tahun !== 'none')
                        <div class="text-gray-400 font-mono font-bold">{{ $sep ?: '' }}</div>
                        <div class="text-center">
                            <div class="px-3 py-2 bg-green-600 text-white rounded font-mono text-xs font-bold">
                                {{ $prefix->format_tahun === 'YY' ? $now->format('y') : $now->format('Y') }}
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Tahun</p>
                        </div>
                    @endif

                    @if($prefix->format_bulan !== 'none')
                        <div class="text-gray-400 font-mono font-bold">{{ $sep ?: '' }}</div>
                        <div class="text-center">
                            <div class="px-3 py-2 bg-orange-500 text-white rounded font-mono text-xs font-bold">
                                {{ $now->format('m') }}
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Bulan</p>
                        </div>
                    @endif

                    <div class="text-gray-400 font-mono font-bold">{{ $sep ?: '' }}</div>
                    <div class="text-center">
                        <div class="px-3 py-2 bg-purple-600 text-white rounded font-mono text-xs font-bold">
                            {{ str_pad('1', $prefix->panjang_urutan, '0', STR_PAD_LEFT) }}
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Urutan ({{ $prefix->panjang_urutan }} digit)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Aturan Reset & Statistik ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white border rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">Aturan Reset</h3>
            </div>
            <div class="p-4 text-xs">
                @if($prefix->reset_per === 'bulan')
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">📅</span>
                        <div>
                            <p class="font-semibold text-gray-800">Reset per Bulan</p>
                            <p class="text-gray-500 mt-0.5">Nomor urut kembali ke 0001 setiap awal bulan baru</p>
                        </div>
                    </div>
                @elseif($prefix->reset_per === 'tahun')
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">📆</span>
                        <div>
                            <p class="font-semibold text-gray-800">Reset per Tahun</p>
                            <p class="text-gray-500 mt-0.5">Nomor urut kembali ke 0001 setiap awal tahun baru</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">♾️</span>
                        <div>
                            <p class="font-semibold text-gray-800">Tidak Pernah Reset</p>
                            <p class="text-gray-500 mt-0.5">Nomor urut terus bertambah, tidak pernah diulang</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white border rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">Statistik Penggunaan</h3>
            </div>
            <div class="p-4 text-xs space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Total nomor diterbitkan</span>
                    <span class="font-bold text-blue-600 text-sm">{{ number_format($prefix->nomor_terakhir) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Nomor berikutnya</span>
                    <code class="font-mono font-bold text-green-600">
                        {{ str_pad($prefix->nomor_terakhir + 1, $prefix->panjang_urutan, '0', STR_PAD_LEFT) }}
                    </code>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Terakhir diperbarui</span>
                    <span class="text-gray-600">{{ $prefix->updated_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Dibuat pada</span>
                    <span class="text-gray-600">{{ $prefix->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Aksi Reset (Admin only) ──────────────────────────────────────────── --}}
    @if(auth()->user()->isSuperadmin())
        <div class="bg-white border border-red-200 rounded-lg overflow-hidden">
            <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                <h3 class="text-sm font-semibold text-red-800">⚠️ Zona Berbahaya</h3>
            </div>
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-800">Reset Nomor Urut</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Kembalikan nomor urut ke 0. Nomor berikutnya akan menjadi
                        <code class="font-mono">{{ str_pad(1, $prefix->panjang_urutan, '0', STR_PAD_LEFT) }}</code>.
                        Gunakan hanya untuk testing atau koreksi data.
                    </p>
                </div>
                <form action="{{ route('settings.prefix.reset', $prefix) }}" method="POST"
                    onsubmit="return confirm('PERHATIAN: Reset nomor urut {{ $prefix->kode_jenis }} ke 0?\n\nIni dapat menyebabkan duplikasi nomor jika ada dokumen yang sudah diterbitkan!')">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 whitespace-nowrap">
                        Reset ke 0
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Cara Pakai ───────────────────────────────────────────────────────── --}}
        <div class="bg-white border rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">📖 Cara Menggunakan di Controller</h3>
            </div>
            <div class="p-4">
                <div class="bg-gray-900 rounded-lg p-4 text-xs font-mono text-gray-100 overflow-x-auto">
    <pre>// Generate dan langsung simpan nomor (increment otomatis)
    $nomor = MasterPrefixNomor::generateFor('{{ $prefix->kode_jenis }}');
    // Hasil: {{ $prefix->contoh_hasil ?? $prefix->buildContoh() }}

    // Atau manual (jika perlu kontrol penuh):
    $prefix = MasterPrefixNomor::where('kode_jenis', '{{ $prefix->kode_jenis }}')
        ->active()->firstOrFail();
    $nomor = $prefix->useNomor(); // auto increment

    // Preview tanpa increment:
    $preview = MasterPrefixNomor::previewFor('{{ $prefix->kode_jenis }}');</pre>
                </div>
            </div>
        </div>
    @endif

    

</div>
@endsection