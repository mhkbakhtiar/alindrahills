{{-- resources/views/accounting/laporan/jurnal-umum.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Jurnal Umum')
@section('breadcrumb', 'Accounting / Laporan / Jurnal Umum')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Laporan Jurnal Umum</h2>
        <div class="flex gap-2">
            <a href="{{ route('accounting.laporan.jurnal-umum.print', request()->query()) }}" target="_blank"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print PDF
            </a>
            <a href="{{ route('accounting.laporan.jurnal-umum.excel', request()->query()) }}"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export CSV
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <x-card>
        <form method="GET" class="flex gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ $dari }}"
                    class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ $sampai }}"
                    class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <x-button type="submit" variant="primary">Filter</x-button>
        </form>
    </x-card>

    {{-- Summary --}}
    @php
        $grandDebet = 0;
        $grandKredit = 0;
        foreach($jurnal as $j) {
            $grandDebet += $j->items->sum('debet');
            $grandKredit += $j->items->sum('kredit');
        }
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Total Jurnal</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $jurnal->count() }}</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Total Debet</p>
            <p class="text-xl font-bold text-green-600 mt-1">Rp {{ number_format($grandDebet, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Total Kredit</p>
            <p class="text-xl font-bold text-red-600 mt-1">Rp {{ number_format($grandKredit, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Table --}}
    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">No. Bukti</th>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Perkiraan</th>
                        <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                        <th class="px-3 py-2 text-right font-semibold">Debet (Rp)</th>
                        <th class="px-3 py-2 text-right font-semibold">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($jurnal as $j)
                        @foreach($j->items as $index => $item)
                            <tr class="hover:bg-gray-50">
                                @if($index === 0)
                                    <td class="px-3 py-2 align-top font-medium" rowspan="{{ $j->items->count() }}">
                                        {{ \Carbon\Carbon::parse($j->tanggal)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-3 py-2 align-top" rowspan="{{ $j->items->count() }}">
                                        <a href="{{ route('accounting.jurnal.show', $j) }}" class="text-blue-600 hover:underline font-medium">
                                            {{ $j->nomor_bukti }}
                                        </a>
                                    </td>
                                @endif
                                <td class="px-3 py-2">{{ $item->kode_perkiraan }}</td>
                                <td class="px-3 py-2">{{ $item->perkiraan->nama_perkiraan ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $item->keterangan ?? $j->keterangan ?? '-' }}</td>
                                <td class="px-3 py-2 text-right {{ $item->debet > 0 ? 'text-green-600 font-semibold' : 'text-gray-300' }}">
                                    {{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-3 py-2 text-right {{ $item->kredit > 0 ? 'text-red-600 font-semibold' : 'text-gray-300' }}">
                                    {{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-50">
                            <td colspan="5" class="px-3 py-1 text-right text-gray-500 italic text-xs">{{ $j->keterangan }}</td>
                            <td class="px-3 py-1 text-right font-semibold text-green-700 text-xs">{{ number_format($j->items->sum('debet'), 0, ',', '.') }}</td>
                            <td class="px-3 py-1 text-right font-semibold text-red-700 text-xs">{{ number_format($j->items->sum('kredit'), 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-gray-500">Tidak ada data jurnal pada periode ini</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($jurnal->count() > 0)
                <tfoot class="bg-gray-100 font-bold border-t-2">
                    <tr>
                        <td colspan="5" class="px-3 py-2 text-right">GRAND TOTAL:</td>
                        <td class="px-3 py-2 text-right text-green-700">Rp {{ number_format($grandDebet, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-red-700">Rp {{ number_format($grandKredit, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </x-card>
</div>
@endsection