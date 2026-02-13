{{-- resources/views/accounting/perkiraan/balance-sheet.blade.php --}}
@extends('layouts.app')

@section('title', 'Neraca')
@section('breadcrumb', 'Accounting / Laporan / Neraca')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Neraca (Balance Sheet)</h2>
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
            <h3 class="text-lg font-bold">NERACA</h3>
            <p class="text-sm text-gray-600">Per Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-6">
            {{-- AKTIVA (ASET) --}}
            <div>
                <div class="bg-blue-600 text-white px-3 py-2 font-bold text-sm mb-2">AKTIVA</div>
                
                <table class="w-full text-xs">
                    <tbody>
                        @foreach($aset->groupBy('kategori') as $kategori => $items)
                            <tr class="bg-gray-100">
                                <td colspan="2" class="px-3 py-2 font-semibold">{{ $kategori ?? 'Lain-lain' }}</td>
                            </tr>
                            @foreach($items as $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 pl-6">{{ $p->nama_perkiraan }}</td>
                                    <td class="px-3 py-2 text-right">Rp {{ number_format($p->saldo, 0) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50 font-semibold">
                                <td class="px-3 py-2 text-right">Subtotal {{ $kategori ?? 'Lain-lain' }}:</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($items->sum('saldo'), 0) }}</td>
                            </tr>
                        @endforeach
                        
                        <tr class="bg-blue-100 font-bold border-t-2 border-blue-600">
                            <td class="px-3 py-3 text-right">TOTAL AKTIVA:</td>
                            <td class="px-3 py-3 text-right">Rp {{ number_format($totalAset, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- PASIVA (KEWAJIBAN & MODAL) --}}
            <div>
                <div class="bg-red-600 text-white px-3 py-2 font-bold text-sm mb-2">PASIVA</div>
                
                <table class="w-full text-xs">
                    <tbody>
                        {{-- KEWAJIBAN --}}
                        <tr class="bg-gray-100">
                            <td colspan="2" class="px-3 py-2 font-bold">KEWAJIBAN</td>
                        </tr>
                        
                        @foreach($kewajiban->groupBy('kategori') as $kategori => $items)
                            <tr class="bg-gray-50">
                                <td colspan="2" class="px-3 py-2 font-semibold pl-4">{{ $kategori ?? 'Lain-lain' }}</td>
                            </tr>
                            @foreach($items as $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 pl-8">{{ $p->nama_perkiraan }}</td>
                                    <td class="px-3 py-2 text-right">Rp {{ number_format($p->saldo, 0) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        
                        <tr class="bg-gray-100 font-semibold">
                            <td class="px-3 py-2 text-right">Total Kewajiban:</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($totalKewajiban, 0) }}</td>
                        </tr>

                        {{-- MODAL --}}
                        <tr class="bg-gray-100 border-t">
                            <td colspan="2" class="px-3 py-2 font-bold">MODAL</td>
                        </tr>
                        
                        @foreach($modal as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 pl-4">{{ $p->nama_perkiraan }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($p->saldo, 0) }}</td>
                            </tr>
                        @endforeach
                        
                        <tr class="bg-gray-100 font-semibold">
                            <td class="px-3 py-2 text-right">Total Modal:</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($totalModal, 0) }}</td>
                        </tr>

                        <tr class="bg-red-100 font-bold border-t-2 border-red-600">
                            <td class="px-3 py-3 text-right">TOTAL PASIVA:</td>
                            <td class="px-3 py-3 text-right">Rp {{ number_format($totalKewajiban + $totalModal, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Balance Verification --}}
        <div class="mt-6 pt-4 border-t">
            <div class="bg-{{ $totalAset == ($totalKewajiban + $totalModal) ? 'green' : 'red' }}-100 p-4 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="font-bold">Verifikasi Neraca:</span>
                    <span class="font-bold {{ $totalAset == ($totalKewajiban + $totalModal) ? 'text-green-600' : 'text-red-600' }}">
                        @if($totalAset == ($totalKewajiban + $totalModal))
                            ✓ BALANCE (Aktiva = Pasiva)
                        @else
                            ✗ NOT BALANCE (Selisih: Rp {{ number_format(abs($totalAset - ($totalKewajiban + $totalModal)), 0) }})
                        @endif
                    </span>
                </div>
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