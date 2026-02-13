{{-- resources/views/accounting/perkiraan/trial-balance.blade.php --}}
@extends('layouts.app')

@section('title', 'Neraca Saldo')
@section('breadcrumb', 'Accounting / Laporan / Neraca Saldo')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Neraca Saldo (Trial Balance)</h2>
        <div class="flex gap-2">
            <x-button variant="secondary" onclick="window.print()">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </x-button>
            <x-button variant="primary" href="{{ route('accounting.perkiraan.export') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
            </x-button>
        </div>
    </div>

    <x-card>
        <div class="text-center mb-6">
            <h3 class="text-lg font-bold">NERACA SALDO</h3>
            <p class="text-sm text-gray-600">Per Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Perkiraan</th>
                        <th class="px-3 py-2 text-center font-semibold">Jenis</th>
                        <th class="px-3 py-2 text-right font-semibold">Saldo Debet</th>
                        <th class="px-3 py-2 text-right font-semibold">Saldo Kredit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach(['Aset', 'Kewajiban', 'Modal', 'Pendapatan', 'Biaya'] as $jenis)
                        @php
                            $items = $perkiraan->where('jenis_akun', $jenis);
                            $subtotalDebet = $items->sum('saldo_debet');
                            $subtotalKredit = $items->sum('saldo_kredit');
                        @endphp
                        
                        @if($items->count() > 0)
                            {{-- Header Jenis --}}
                            <tr class="bg-blue-50">
                                <td colspan="5" class="px-3 py-2 font-bold">{{ $jenis }}</td>
                            </tr>
                            
                            {{-- Items --}}
                            @foreach($items as $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 font-medium">{{ $p->kode_perkiraan }}</td>
                                    <td class="px-3 py-2">{{ $p->nama_perkiraan }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <x-badge variant="secondary" size="sm">{{ $p->kategori ?? '-' }}</x-badge>
                                    </td>
                                    <td class="px-3 py-2 text-right {{ $p->saldo_debet > 0 ? 'text-green-600 font-semibold' : '' }}">
                                        {{ $p->saldo_debet > 0 ? 'Rp ' . number_format($p->saldo_debet, 0) : '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-right {{ $p->saldo_kredit > 0 ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $p->saldo_kredit > 0 ? 'Rp ' . number_format($p->saldo_kredit, 0) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                            
                            {{-- Subtotal --}}
                            <tr class="bg-gray-100 font-semibold">
                                <td colspan="3" class="px-3 py-2 text-right">Subtotal {{ $jenis }}:</td>
                                <td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($subtotalDebet, 0) }}</td>
                                <td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($subtotalKredit, 0) }}</td>
                            </tr>
                        @endif
                    @endforeach

                    {{-- Grand Total --}}
                    <tr class="bg-gray-800 text-white font-bold text-sm">
                        <td colspan="3" class="px-3 py-3 text-right">TOTAL:</td>
                        <td class="px-3 py-3 text-right">Rp {{ number_format($totalDebet, 0) }}</td>
                        <td class="px-3 py-3 text-right">Rp {{ number_format($totalKredit, 0) }}</td>
                    </tr>
                    
                    {{-- Balance Check --}}
                    <tr class="bg-{{ $totalDebet == $totalKredit ? 'green' : 'red' }}-100">
                        <td colspan="3" class="px-3 py-2 text-right font-semibold">Selisih:</td>
                        <td colspan="2" class="px-3 py-2 text-center font-bold {{ $totalDebet == $totalKredit ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format(abs($totalDebet - $totalKredit), 0) }}
                            @if($totalDebet == $totalKredit)
                                <span class="text-xs">(Balanced ✓)</span>
                            @else
                                <span class="text-xs">(Not Balanced ✗)</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
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