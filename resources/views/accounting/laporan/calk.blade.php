{{-- resources/views/accounting/laporan/calk.blade.php --}}
@extends('layouts.app')

@section('title', 'CALK')
@section('breadcrumb', 'Accounting / Laporan / CALK')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Catatan Atas Laporan Keuangan (CALK)</h2>
        <div class="flex gap-2">
            <a href="{{ route('accounting.laporan.calk.print', request()->query()) }}" target="_blank"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print PDF
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($data as $key => $section)
            <x-card>
                <h3 class="text-sm font-semibold mb-3 border-b pb-2 text-gray-700">
                    {{ $section['title'] }}
                </h3>
                <table class="min-w-full text-xs">
                    <tbody class="divide-y divide-gray-100">
                        @forelse($section['items'] as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 text-gray-500 w-20">{{ $item->kode_perkiraan }}</td>
                                <td class="py-2">{{ $item->nama_perkiraan }}</td>
                                <td class="py-2 text-right font-medium">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-400 italic">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 font-bold bg-gray-50">
                            <td colspan="2" class="py-2">TOTAL {{ strtoupper($section['title']) }}</td>
                            <td class="py-2 text-right">Rp {{ number_format($section['total'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-card>
        @endforeach
    </div>
</div>
@endsection