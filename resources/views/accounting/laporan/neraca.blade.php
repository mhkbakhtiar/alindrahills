{{-- resources/views/accounting/laporan/neraca.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Neraca')
@section('breadcrumb', 'Accounting / Laporan / Neraca')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Laporan Neraca (Balance Sheet)</h2>
        <div class="flex gap-2">
            <a href="{{ route('accounting.laporan.neraca.print', request()->query()) }}" target="_blank"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print PDF
            </a>
            <a href="{{ route('accounting.laporan.neraca.excel', request()->query()) }}"
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
                <label class="block text-xs font-medium text-gray-700 mb-1">Per Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}"
                    class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <x-button type="submit" variant="primary">Tampilkan</x-button>
        </form>
    </x-card>

    {{-- Balance Check --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-xs text-blue-600 font-medium">Total Aset</p>
            <p class="text-xl font-bold text-blue-800 mt-1">Rp {{ number_format($totalAset, 0, ',', '.') }}</p>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <p class="text-xs text-orange-600 font-medium">Total Kewajiban + Modal</p>
            <p class="text-xl font-bold text-orange-800 mt-1">Rp {{ number_format($totalKewajiban + $totalModal, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg p-4 {{ abs($totalAset - ($totalKewajiban + $totalModal)) < 1 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
            <p class="text-xs font-medium {{ abs($totalAset - ($totalKewajiban + $totalModal)) < 1 ? 'text-green-600' : 'text-red-600' }}">Status</p>
            <p class="text-xl font-bold mt-1 {{ abs($totalAset - ($totalKewajiban + $totalModal)) < 1 ? 'text-green-800' : 'text-red-800' }}">
                {{ abs($totalAset - ($totalKewajiban + $totalModal)) < 1 ? '✓ BALANCE' : '✗ TIDAK BALANCE' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- ASET --}}
        <x-card>
            <h3 class="text-sm font-semibold mb-3 border-b pb-2 text-blue-700">ASET</h3>
            <table class="min-w-full text-xs">
                <tbody class="divide-y divide-gray-100">
                    @foreach($aset as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 text-gray-600">{{ $item->kode_perkiraan }}</td>
                            <td class="py-2">{{ $item->nama_perkiraan }}</td>
                            <td class="py-2 text-right font-medium">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 font-bold bg-blue-50">
                        <td colspan="2" class="py-2 text-blue-800">TOTAL ASET</td>
                        <td class="py-2 text-right text-blue-800">Rp {{ number_format($totalAset, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </x-card>

        {{-- KEWAJIBAN + MODAL --}}
        <div class="space-y-4">
            <x-card>
                <h3 class="text-sm font-semibold mb-3 border-b pb-2 text-red-700">KEWAJIBAN</h3>
                <table class="min-w-full text-xs">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($kewajiban as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 text-gray-600">{{ $item->kode_perkiraan }}</td>
                                <td class="py-2">{{ $item->nama_perkiraan }}</td>
                                <td class="py-2 text-right font-medium">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 font-bold bg-red-50">
                            <td colspan="2" class="py-2 text-red-800">TOTAL KEWAJIBAN</td>
                            <td class="py-2 text-right text-red-800">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-card>

            <x-card>
                <h3 class="text-sm font-semibold mb-3 border-b pb-2 text-green-700">MODAL</h3>
                <table class="min-w-full text-xs">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($modal as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 text-gray-600">{{ $item->kode_perkiraan }}</td>
                                <td class="py-2">{{ $item->nama_perkiraan }}</td>
                                <td class="py-2 text-right font-medium">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 text-gray-600">-</td>
                            <td class="py-2 italic text-gray-600">Laba/Rugi Berjalan</td>
                            <td class="py-2 text-right font-medium {{ $labaRugi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($labaRugi, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 font-bold bg-green-50">
                            <td colspan="2" class="py-2 text-green-800">TOTAL MODAL</td>
                            <td class="py-2 text-right text-green-800">Rp {{ number_format($totalModal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-card>
        </div>
    </div>
</div>
@endsection