@extends('layouts.app')

@section('title', 'Detail Jurnal')
@section('breadcrumb', 'Accounting / Jurnal / Detail')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Detail Jurnal: {{ $jurnal->nomor_bukti }}</h2>
        <div class="flex gap-2">
            <x-button variant="secondary" href="{{ route('accounting.jurnal.index') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </x-button>
            
            @if($jurnal->status === 'draft')
                <x-button variant="warning" href="{{ route('accounting.jurnal.edit', $jurnal) }}">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </x-button>

                <form action="{{ route('accounting.jurnal.post', $jurnal) }}" method="POST" 
                      onsubmit="return confirm('Yakin ingin posting jurnal ini?')" class="inline">
                    @csrf
                    <x-button type="submit" variant="success">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Post Jurnal
                    </x-button>
                </form>
            @endif

            @if($jurnal->status === 'posted')
                <form action="{{ route('accounting.jurnal.void', $jurnal) }}" method="POST" 
                      onsubmit="return confirm('Yakin ingin void jurnal ini? Saldo akan dikembalikan.')" class="inline">
                    @csrf
                    <x-button type="submit" variant="danger">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Void
                    </x-button>
                </form>
            @endif

            <x-button variant="secondary" href="{{ route('accounting.jurnal.print', $jurnal) }}" target="_blank">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </x-button>
        </div>
    </div>

    {{-- Header Information --}}
    <x-card>
        <h3 class="text-sm font-semibold mb-4 border-b pb-2">Informasi Jurnal</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <label class="text-xs text-gray-600">Nomor Bukti</label>
                <p class="font-semibold">{{ $jurnal->nomor_bukti }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Tanggal</label>
                <p class="font-semibold">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d F Y') }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Jenis Jurnal</label>
                <p>
                    <x-badge :variant="match($jurnal->jenis_jurnal) {
                        'umum' => 'info',
                        'penyesuaian' => 'warning',
                        'penutup' => 'danger',
                        'pembalik' => 'secondary',
                        default => 'secondary'
                    }">
                        {{ ucfirst($jurnal->jenis_jurnal) }}
                    </x-badge>
                </p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Status</label>
                <p>
                    <x-badge :variant="match($jurnal->status) {
                        'draft' => 'secondary',
                        'posted' => 'success',
                        'void' => 'danger',
                        default => 'secondary'
                    }">
                        {{ ucfirst($jurnal->status) }}
                    </x-badge>
                </p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Departemen</label>
                <p class="font-semibold">{{ $jurnal->departemen ?? '-' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Tahun Anggaran</label>
                <p class="font-semibold">{{ $jurnal->tahunAnggaran->nama ?? '-' }}</p>
            </div>
            <div class="md:col-span-3">
                <label class="text-xs text-gray-600">Keterangan</label>
                <p class="font-semibold">{{ $jurnal->keterangan ?? '-' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Dibuat Oleh</label>
                <p class="font-semibold">{{ $jurnal->creator->full_name ?? '-' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Dibuat Pada</label>
                <p class="font-semibold">{{ $jurnal->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @if($jurnal->updated_at != $jurnal->created_at)
            <div>
                <label class="text-xs text-gray-600">Diupdate Pada</label>
                <p class="font-semibold">{{ $jurnal->updated_at->format('d/m/Y H:i') }}</p>
            </div>
            @endif
        </div>
    </x-card>

    {{-- Journal Items --}}
    <x-card>
        <h3 class="text-sm font-semibold mb-4 border-b pb-2">Detail Item Jurnal</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold w-8">#</th>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Perkiraan</th>
                        <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                        <th class="px-3 py-2 text-right font-semibold">Debet (Rp)</th>
                        <th class="px-3 py-2 text-right font-semibold">Kredit (Rp)</th>
                        <th class="px-3 py-2 text-left font-semibold">Kavling - Pembeli</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $totalDebet = 0;
                        $totalKredit = 0;
                    @endphp
                    @foreach($jurnal->items as $index => $item)
                        @php
                            $totalDebet += $item->debet;
                            $totalKredit += $item->kredit;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-center text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-medium">{{ $item->kode_perkiraan }}</td>
                            <td class="px-3 py-2">{{ $item->perkiraan->nama_perkiraan ?? '-' }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                            <td class="px-3 py-2 text-right">
                                @if($item->debet > 0)
                                    <span class="text-green-600 font-semibold">Rp {{ number_format($item->debet, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                @if($item->kredit > 0)
                                    <span class="text-red-600 font-semibold">Rp {{ number_format($item->kredit, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-gray-600">
                                @if($item->kavlingPembeli)
                                    {{ $item->kavlingPembeli->kavling->kavling ?? '' }}
                                    {{ $item->kavlingPembeli->kavling->blok ? '- ' . $item->kavlingPembeli->kavling->blok : '' }}
                                    {{ $item->kavlingPembeli->pembeli->nama ? '| ' . $item->kavlingPembeli->pembeli->nama : '' }}
                                @else
                                    {{ $item->kode_kavling ?? '-' }}
                                @endif
                            </td>
                            {{-- HAPUS baris <td> user yang lama --}}
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold border-t-2">
                    <tr>
                        <td colspan="4" class="px-3 py-2 text-right">TOTAL:</td>
                        <td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($totalDebet, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                        <td class="px-3 py-2">
                            @if(abs($totalDebet - $totalKredit) < 0.01)
                                <span class="text-green-600">✓ BALANCE</span>
                            @else
                                <span class="text-red-600">✗ TIDAK BALANCE (Selisih: Rp {{ number_format(abs($totalDebet - $totalKredit), 0, ',', '.') }})</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-card>

    {{-- Action History (if needed) --}}
    @if($jurnal->status === 'void' || $jurnal->status === 'posted')
    <x-card>
        <h3 class="text-sm font-semibold mb-3 border-b pb-2">Riwayat Aksi</h3>
        <div class="space-y-2 text-xs">
            @if($jurnal->status === 'posted')
                <div class="flex items-center gap-2 text-green-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Jurnal telah di-posting dan mempengaruhi saldo perkiraan</span>
                </div>
            @endif
            @if($jurnal->status === 'void')
                <div class="flex items-center gap-2 text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Jurnal telah di-void dan saldo perkiraan telah dikembalikan</span>
                </div>
            @endif
        </div>
    </x-card>
    @endif
</div>
@endsection