{{-- resources/views/accounting/laporan/buku-besar.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Buku Besar')
@section('breadcrumb', 'Accounting / Laporan / Buku Besar')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Laporan Buku Besar</h2>
        @if(!empty($data))
        <div class="flex gap-2">
            <a href="{{ route('accounting.laporan.buku-besar.print', request()->query()) }}" target="_blank"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print PDF
            </a>
            <a href="{{ route('accounting.laporan.buku-besar.excel', request()->query()) }}"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export CSV
            </a>
        </div>
        @endif
    </div>

    {{-- Filter --}}
    <x-card>
        <form method="GET" class="flex gap-3 items-end flex-wrap">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Perkiraan *</label>
                <select name="kode_perkiraan" class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" style="min-width:250px" required>
                    <option value="">-- Pilih Perkiraan --</option>
                    @foreach($perkiraan as $p)
                        <option value="{{ $p->kode_perkiraan }}" {{ $kodePerkiraan == $p->kode_perkiraan ? 'selected' : '' }}>
                            {{ $p->kode_perkiraan }} - {{ $p->nama_perkiraan }}
                        </option>
                    @endforeach
                </select>
            </div>
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
            <x-button type="submit" variant="primary">Tampilkan</x-button>
        </form>
    </x-card>

    @if(!empty($data))
    {{-- Account Info --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white border rounded-lg p-4 col-span-1">
            <p class="text-xs text-gray-500">Kode Perkiraan</p>
            <p class="text-lg font-bold text-blue-600 mt-1">{{ $data['akun']->kode_perkiraan }}</p>
        </div>
        <div class="bg-white border rounded-lg p-4 col-span-1">
            <p class="text-xs text-gray-500">Nama Perkiraan</p>
            <p class="text-sm font-bold text-gray-900 mt-1">{{ $data['akun']->nama_perkiraan }}</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Saldo Awal</p>
            <p class="text-lg font-bold text-gray-700 mt-1">Rp {{ number_format($data['saldo_awal'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Saldo Akhir</p>
            @php
                $saldoAkhir = $data['saldo_awal'];
                foreach($data['mutasi'] as $m) { $saldoAkhir += ($m->debet - $m->kredit); }
            @endphp
            <p class="text-lg font-bold {{ $saldoAkhir >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Ledger Table --}}
    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">No. Bukti</th>
                        <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                        <th class="px-3 py-2 text-right font-semibold">Debet (Rp)</th>
                        <th class="px-3 py-2 text-right font-semibold">Kredit (Rp)</th>
                        <th class="px-3 py-2 text-right font-semibold">Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="bg-blue-50 font-semibold">
                        <td colspan="5" class="px-3 py-2 text-gray-700">Saldo Awal</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($data['saldo_awal'], 0, ',', '.') }}</td>
                    </tr>
                    @php $saldo = $data['saldo_awal']; $totalDebet = 0; $totalKredit = 0; @endphp
                    @forelse($data['mutasi'] as $item)
                        @php
                            $saldo += ($item->debet - $item->kredit);
                            $totalDebet += $item->debet;
                            $totalKredit += $item->kredit;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $item->jurnal->tanggal->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">
                                <a href="{{ route('accounting.jurnal.show', $item->jurnal) }}" class="text-blue-600 hover:underline font-medium">
                                    {{ $item->jurnal->nomor_bukti }}
                                </a>
                            </td>
                            <td class="px-3 py-2 text-gray-600">{{ $item->keterangan ?? $item->jurnal->keterangan ?? '-' }}</td>
                            <td class="px-3 py-2 text-right {{ $item->debet > 0 ? 'text-green-600 font-semibold' : 'text-gray-300' }}">
                                {{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-3 py-2 text-right {{ $item->kredit > 0 ? 'text-red-600 font-semibold' : 'text-gray-300' }}">
                                {{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-3 py-2 text-right font-semibold {{ $saldo >= 0 ? 'text-gray-800' : 'text-red-600' }}">
                                Rp {{ number_format($saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-8 text-center text-gray-500">Tidak ada mutasi pada periode ini</td></tr>
                    @endforelse
                </tbody>
                @if($data['mutasi']->count() > 0)
                <tfoot class="bg-gray-100 font-bold border-t-2">
                    <tr>
                        <td colspan="3" class="px-3 py-2 text-right">TOTAL MUTASI:</td>
                        <td class="px-3 py-2 text-right text-green-700">Rp {{ number_format($totalDebet, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-red-700">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </x-card>
    @else
        <x-card>
            <div class="py-12 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-sm">Pilih perkiraan dan periode untuk menampilkan buku besar</p>
            </div>
        </x-card>
    @endif
</div>
@endsection