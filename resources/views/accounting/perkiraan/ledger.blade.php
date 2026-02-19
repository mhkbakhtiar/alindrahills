{{-- resources/views/accounting/perkiraan/ledger.blade.php --}}
@extends('layouts.app')

@section('title', 'Buku Besar - ' . $perkiraan->nama_perkiraan)
@section('breadcrumb', 'Accounting / Laporan / Buku Besar')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Buku Besar (Ledger)</h2>
        <div class="flex gap-2">
            <a href="{{ route('accounting.perkiraan.ledger.print', ['perkiraan' => $perkiraan, 'start_date' => request('start_date', $startDate->format('Y-m-d')), 'end_date' => request('end_date', $endDate->format('Y-m-d'))]) }}" 
            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Export PDF
            </a>
            <x-button variant="secondary" href="{{ route('accounting.perkiraan.show', $perkiraan) }}">Kembali</x-button>
        </div>
    </div>

    {{-- Account Info --}}
    <x-card>
        <div class="grid grid-cols-2 gap-4 text-xs mb-4">
            <div>
                <p class="text-gray-600">Kode Perkiraan</p>
                <p class="font-semibold text-lg">{{ $perkiraan->kode_perkiraan }}</p>
            </div>
            <div>
                <p class="text-gray-600">Nama Perkiraan</p>
                <p class="font-semibold text-lg">{{ $perkiraan->nama_perkiraan }}</p>
            </div>
            <div>
                <p class="text-gray-600">Jenis Akun</p>
                <p><x-badge>{{ $perkiraan->jenis_akun }}</x-badge></p>
            </div>
            <div>
                <p class="text-gray-600">Kategori</p>
                <p>{{ $perkiraan->kategori ?? '-' }}</p>
            </div>
        </div>

        {{-- Date Filter --}}
        <form method="GET" class="flex gap-3 items-end border-t pt-4">
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
    </x-card>

    {{-- Ledger Table --}}
    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">No. Bukti</th>
                        <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                        <th class="px-3 py-2 text-right font-semibold">Debet</th>
                        <th class="px-3 py-2 text-right font-semibold">Kredit</th>
                        <th class="px-3 py-2 text-right font-semibold">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $totalDebet = 0;
                        $totalKredit = 0;
                    @endphp
                    
                    @forelse($items as $item)
                        @php
                            $totalDebet += $item->debet;
                            $totalKredit += $item->kredit;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $item->jurnal->tanggal->format('d/m/Y') }}</td>
                            <td class="px-3 py-2 font-medium">{{ $item->jurnal->nomor_bukti }}</td>
                            <td class="px-3 py-2">{{ $item->keterangan }}</td>
                            <td class="px-3 py-2 text-right {{ $item->debet > 0 ? 'text-green-600 font-semibold' : '' }}">
                                {{ $item->debet > 0 ? 'Rp ' . number_format($item->debet, 0) : '-' }}
                            </td>
                            <td class="px-3 py-2 text-right {{ $item->kredit > 0 ? 'text-red-600 font-semibold' : '' }}">
                                {{ $item->kredit > 0 ? 'Rp ' . number_format($item->kredit, 0) : '-' }}
                            </td>
                            <td class="px-3 py-2 text-right font-semibold">
                                Rp {{ number_format($item->running_balance, 0) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-gray-500">Tidak ada transaksi pada periode ini</td>
                        </tr>
                    @endforelse

                    @if($items->count() > 0)
                        <tr class="bg-gray-100 font-bold">
                            <td colspan="3" class="px-3 py-2 text-right">Total:</td>
                            <td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($totalDebet, 0) }}</td>
                            <td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($totalKredit, 0) }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($totalDebet - $totalKredit, 0) }}</td>
                        </tr>
                    @endif
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