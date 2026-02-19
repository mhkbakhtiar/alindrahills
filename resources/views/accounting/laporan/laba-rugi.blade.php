{{-- resources/views/accounting/laporan/laba-rugi.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')
@section('breadcrumb', 'Accounting / Laporan / Laba Rugi')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Laporan Laba Rugi (Income Statement)</h2>
        <div class="flex gap-2">
            <a href="{{ route('accounting.laporan.laba-rugi.print', request()->query()) }}" target="_blank"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print PDF
            </a>
            <a href="{{ route('accounting.laporan.laba-rugi.excel', request()->query()) }}"
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
                <input type="date" name="dari" value="{{ $dari }}" class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <x-button type="submit" variant="primary">Tampilkan</x-button>
        </form>
    </x-card>

    {{-- Summary --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-xs text-green-600 font-medium">Total Pendapatan</p>
            <p class="text-xl font-bold text-green-800 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-xs text-red-600 font-medium">Total Biaya</p>
            <p class="text-xl font-bold text-red-800 mt-1">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg p-4 border {{ $labaRugi >= 0 ? 'bg-blue-50 border-blue-200' : 'bg-orange-50 border-orange-200' }}">
            <p class="text-xs font-medium {{ $labaRugi >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                {{ $labaRugi >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}
            </p>
            <p class="text-xl font-bold mt-1 {{ $labaRugi >= 0 ? 'text-blue-800' : 'text-orange-800' }}">
                Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- PENDAPATAN --}}
        <x-card>
            <h3 class="text-sm font-semibold mb-3 border-b pb-2 text-green-700">PENDAPATAN</h3>
            <table class="min-w-full text-xs">
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendapatan as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 text-gray-500 w-20">{{ $item->kode_perkiraan }}</td>
                            <td class="py-2">{{ $item->nama_perkiraan }}</td>
                            <td class="py-2 text-right font-medium text-green-700">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">Tidak ada data pendapatan</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 font-bold bg-green-50">
                        <td colspan="2" class="py-2 text-green-800">TOTAL PENDAPATAN</td>
                        <td class="py-2 text-right text-green-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </x-card>

        {{-- BIAYA --}}
        <x-card>
            <h3 class="text-sm font-semibold mb-3 border-b pb-2 text-red-700">BIAYA</h3>
            <table class="min-w-full text-xs">
                <tbody class="divide-y divide-gray-100">
                    @forelse($biaya as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 text-gray-500 w-20">{{ $item->kode_perkiraan }}</td>
                            <td class="py-2">{{ $item->nama_perkiraan }}</td>
                            <td class="py-2 text-right font-medium text-red-700">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">Tidak ada data biaya</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 font-bold bg-red-50">
                        <td colspan="2" class="py-2 text-red-800">TOTAL BIAYA</td>
                        <td class="py-2 text-right text-red-800">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </x-card>
    </div>

    {{-- Bottom Line --}}
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Periode: {{ \Carbon\Carbon::parse($dari)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d F Y') }}</p>
                <p class="text-xs text-gray-400 mt-1">Total Pendapatan - Total Biaya</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 mb-1">{{ $labaRugi >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</p>
                <p class="text-2xl font-bold {{ $labaRugi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
                </p>
            </div>
        </div>
    </x-card>
</div>
@endsection