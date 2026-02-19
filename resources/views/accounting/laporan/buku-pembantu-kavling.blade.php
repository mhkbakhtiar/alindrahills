{{-- resources/views/accounting/laporan/buku-pembantu-kavling.blade.php --}}
@extends('layouts.app')

@section('title', 'Buku Pembantu Kavling')
@section('breadcrumb', 'Accounting / Laporan / Buku Pembantu Kavling')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Buku Pembantu per Kavling</h2>
        @if(!empty($data))
        <div class="flex gap-2">
            <a href="{{ route('accounting.laporan.buku-pembantu-kavling.print', request()->query()) }}" target="_blank"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print PDF
            </a>
        </div>
        @endif
    </div>

    {{-- Filter --}}
    <x-card>
        <form method="GET" class="flex gap-3 items-end flex-wrap">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Kavling *</label>
                <select name="kavling_pembeli_id" class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" style="min-width:280px">
                    <option value="">-- Pilih Kavling & Pembeli --</option>
                    @foreach($kavlingList as $kp)
                        <option value="{{ $kp->id }}" {{ $kavlingPembeliId == $kp->id ? 'selected' : '' }}>
                            {{ $kp->kavling->kavling ?? '' }} - {{ $kp->kavling->blok ?? '' }} | {{ $kp->pembeli->nama ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>
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

    @if(!empty($data))
    {{-- Kavling Info --}}

    @if(!empty($data))
    <x-card>
        <h3 class="text-sm font-semibold mb-3 border-b pb-2">Informasi Kavling & Pembeli</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
                <p class="text-gray-500">Kavling</p>
                <p class="font-bold text-blue-600 text-base">{{ $data['kavlingPembeli']->kavling->kavling ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Blok</p>
                <p class="font-semibold">{{ $data['kavlingPembeli']->kavling->blok ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Pembeli</p>
                <p class="font-semibold">{{ $data['kavlingPembeli']->pembeli->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Status</p>
                <p><x-badge variant="info">{{ ucfirst($data['kavlingPembeli']->status) }}</x-badge></p>
            </div>
            <div>
                <p class="text-gray-500">Tanggal Booking</p>
                <p class="font-semibold">{{ $data['kavlingPembeli']->tanggal_booking?->format('d/m/Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Tanggal Akad</p>
                <p class="font-semibold">{{ $data['kavlingPembeli']->tanggal_akad?->format('d/m/Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Harga Jual</p>
                <p class="font-semibold text-green-600">Rp {{ number_format($data['kavlingPembeli']->harga_jual ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Sisa Pembayaran</p>
                <p class="font-semibold {{ $data['sisa_pembayaran'] > 0 ? 'text-orange-600' : 'text-green-600' }}">
                    Rp {{ number_format($data['sisa_pembayaran'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </x-card>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Total Debet</p>
            <p class="text-lg font-bold text-green-600 mt-1">Rp {{ number_format($data['total_debet'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Total Kredit</p>
            <p class="text-lg font-bold text-red-600 mt-1">Rp {{ number_format($data['total_kredit'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Total Pembayaran</p>
            <p class="text-lg font-bold text-blue-600 mt-1">Rp {{ number_format($data['pembayaran'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Sisa Pembayaran</p>
            <p class="text-lg font-bold {{ $data['sisa_pembayaran'] > 0 ? 'text-orange-600' : 'text-green-600' }} mt-1">
                Rp {{ number_format($data['sisa_pembayaran'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Transactions Table --}}
    <x-card>
        <h3 class="text-sm font-semibold mb-3 border-b pb-2">Riwayat Transaksi</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">No. Bukti</th>
                        <th class="px-3 py-2 text-left font-semibold">Perkiraan</th>
                        <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                        <th class="px-3 py-2 text-right font-semibold">Debet (Rp)</th>
                        <th class="px-3 py-2 text-right font-semibold">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($data['transaksi'] as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $item->jurnal->tanggal->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">
                                <a href="{{ route('accounting.jurnal.show', $item->jurnal) }}" class="text-blue-600 hover:underline font-medium">
                                    {{ $item->jurnal->nomor_bukti }}
                                </a>
                            </td>
                            <td class="px-3 py-2">{{ $item->kode_perkiraan }} - {{ $item->perkiraan->nama_perkiraan ?? '-' }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                            <td class="px-3 py-2 text-right {{ $item->debet > 0 ? 'text-green-600 font-semibold' : 'text-gray-300' }}">
                                {{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-3 py-2 text-right {{ $item->kredit > 0 ? 'text-red-600 font-semibold' : 'text-gray-300' }}">
                                {{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-8 text-center text-gray-500">Tidak ada transaksi untuk kavling ini</td></tr>
                    @endforelse
                </tbody>
                @if($data['transaksi']->count() > 0)
                <tfoot class="bg-gray-100 font-bold border-t-2">
                    <tr>
                        <td colspan="4" class="px-3 py-2 text-right">TOTAL:</td>
                        <td class="px-3 py-2 text-right text-green-700">Rp {{ number_format($data['total_debet'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-red-700">Rp {{ number_format($data['total_kredit'], 0, ',', '.') }}</td>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <p class="text-sm">Pilih kavling dan periode untuk menampilkan data</p>
            </div>
        </x-card>
    @endif
</div>
@endsection