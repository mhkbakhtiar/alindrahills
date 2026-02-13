{{-- resources/views/accounting/perkiraan/income-statement.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')
@section('breadcrumb', 'Accounting / Laporan / Laba Rugi')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Laporan Laba Rugi (Income Statement)</h2>
        <div class="flex gap-2">
            <x-button variant="secondary" onclick="window.print()">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </x-button>
        </div>
    </div>

    <x-card>
        <div class="text-center mb-6">
            <h3 class="text-lg font-bold">LAPORAN LABA RUGI</h3>
            <p class="text-sm text-gray-600">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
            </p>
        </div>

        {{-- Date Filter --}}
        <form method="GET" class="flex gap-3 items-end mb-6 pb-4 border-b">
            <div class="flex-1">
                <label class="block text-xs font-medium mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                    class="w-full px-3 py-2 text-xs border rounded-lg">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                    class="w-full px-3 py-2 text-xs border rounded-lg">
            </div>
            <x-button type="submit" variant="primary">Filter</x-button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <tbody>
                    {{-- PENDAPATAN --}}
                    <tr class="bg-green-600 text-white">
                        <td colspan="2" class="px-3 py-2 font-bold">PENDAPATAN</td>
                    </tr>
                    
                    @foreach($pendapatan->groupBy('kategori') as $kategori => $items)
                        <tr class="bg-green-50">
                            <td colspan="2" class="px-3 py-2 font-semibold">{{ $kategori ?? 'Lain-lain' }}</td>
                        </tr>
                        @foreach($items as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 pl-6">{{ $p->nama_perkiraan }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($p->saldo_kredit, 0) }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-green-100 font-semibold">
                            <td class="px-3 py-2 text-right">Subtotal {{ $kategori ?? 'Lain-lain' }}:</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($items->sum('saldo_kredit'), 0) }}</td>
                        </tr>
                    @endforeach
                    
                    <tr class="bg-green-200 font-bold border-t-2 border-green-600">
                        <td class="px-3 py-3 text-right">TOTAL PENDAPATAN:</td>
                        <td class="px-3 py-3 text-right">Rp {{ number_format($totalPendapatan, 0) }}</td>
                    </tr>

                    {{-- BIAYA --}}
                    <tr class="bg-red-600 text-white border-t-4">
                        <td colspan="2" class="px-3 py-2 font-bold">BIAYA</td>
                    </tr>
                    
                    @foreach($biaya->groupBy('kategori') as $kategori => $items)
                        <tr class="bg-red-50">
                            <td colspan="2" class="px-3 py-2 font-semibold">{{ $kategori ?? 'Lain-lain' }}</td>
                        </tr>
                        @foreach($items as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 pl-6">{{ $p->nama_perkiraan }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($p->saldo_debet, 0) }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-red-100 font-semibold">
                            <td class="px-3 py-2 text-right">Subtotal {{ $kategori ?? 'Lain-lain' }}:</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($items->sum('saldo_debet'), 0) }}</td>
                        </tr>
                    @endforeach
                    
                    <tr class="bg-red-200 font-bold border-t-2 border-red-600">
                        <td class="px-3 py-3 text-right">TOTAL BIAYA:</td>
                        <td class="px-3 py-3 text-right">Rp {{ number_format($totalBiaya, 0) }}</td>
                    </tr>

                    {{-- LABA/RUGI --}}
                    <tr class="bg-{{ $labaRugi >= 0 ? 'green' : 'red' }}-600 text-white font-bold text-base border-t-4">
                        <td class="px-4 py-4 text-right">
                            {{ $labaRugi >= 0 ? 'LABA BERSIH:' : 'RUGI BERSIH:' }}
                        </td>
                        <td class="px-4 py-4 text-right">
                            Rp {{ number_format(abs($labaRugi), 0) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t">
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-xs text-gray-600 mb-1">Total Pendapatan</p>
                <p class="text-lg font-bold text-green-600">Rp {{ number_format($totalPendapatan, 0) }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <p class="text-xs text-gray-600 mb-1">Total Biaya</p>
                <p class="text-lg font-bold text-red-600">Rp {{ number_format($totalBiaya, 0) }}</p>
            </div>
            <div class="bg-{{ $labaRugi >= 0 ? 'green' : 'red' }}-100 p-4 rounded-lg">
                <p class="text-xs text-gray-600 mb-1">{{ $labaRugi >= 0 ? 'Laba' : 'Rugi' }} Bersih</p>
                <p class="text-lg font-bold {{ $labaRugi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format(abs($labaRugi), 0) }}
                </p>
                <p class="text-xs mt-1 {{ $labaRugi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $labaRugi >= 0 ? '↑' : '↓' }} 
                    {{ number_format(abs($labaRugi) / max($totalPendapatan, 1) * 100, 2) }}% dari pendapatan
                </p>
            </div>
        </div>
    </x-card>
</div>

<style>
    @media print {
        .no-print {
            display: none;
        }
    }
</style>
@endsection