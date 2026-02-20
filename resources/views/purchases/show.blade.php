{{-- resources/views/purchases/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Purchase Order')
@section('breadcrumb', 'Material / Purchase Orders / Detail')

@section('content')
<div class="space-y-4">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Detail Purchase Order</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $purchase->purchase_number }}</p>
        </div>
        <div class="flex gap-2">
            @if((auth()->user()->isAdmin() || auth()->user()->isSuperadmin()) && !$purchase->goodsReceipt)
                <a href="{{ route('purchases.edit', $purchase) }}"
                    class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-yellow-500 text-white hover:bg-yellow-600">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            @endif
            <a href="{{ route('purchases.index') }}"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- ── Alerts ───────────────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Status Cards ─────────────────────────────────────────────────────── --}}
    @php
        $isOverdue = $purchase->payment_type === 'tempo'
            && $purchase->payment_status === 'belum_bayar'
            && $purchase->tempo_date
            && $purchase->tempo_date->isPast();
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Nomor PO</p>
            <p class="text-sm font-bold text-blue-600 mt-0.5 truncate">{{ $purchase->purchase_number }}</p>
        </div>
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Tanggal PO</p>
            <p class="text-sm font-semibold mt-0.5">{{ $purchase->purchase_date->format('d M Y') }}</p>
        </div>
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Metode Bayar</p>
            @if($purchase->payment_type === 'cash')
                <span class="inline-flex items-center mt-1 gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                    💵 Cash / Tunai
                </span>
            @else
                <span class="inline-flex items-center mt-1 gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                    📅 Tempo
                </span>
                @if($purchase->tempo_date)
                    <p class="text-xs mt-0.5 {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-orange-600' }}">
                        JT: {{ $purchase->tempo_date->format('d M Y') }}
                        @if($isOverdue) ⚠️ @endif
                    </p>
                @endif
            @endif
        </div>
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Status Bayar</p>
            @if($purchase->payment_status === 'lunas')
                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                    ✓ Lunas
                </span>
            @else
                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $isOverdue ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $isOverdue ? '⚠ Overdue' : 'Belum Bayar' }}
                </span>
            @endif
        </div>
    </div>

    {{-- ── Overdue Alert ────────────────────────────────────────────────────── --}}
    @if($isOverdue)
        <div class="bg-red-50 border border-red-300 rounded-lg px-4 py-3 flex items-center gap-2 text-sm text-red-800">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>
                <strong>Jatuh Tempo Terlewati!</strong>
                Pembayaran ke <strong>{{ $purchase->supplier_name }}</strong> seharusnya dilakukan
                pada {{ $purchase->tempo_date->format('d M Y') }}
                ({{ $purchase->tempo_date->diffForHumans() }}).
            </span>
        </div>
    @endif

    {{-- ── Purchase Request Info ───────────────────────────────────────────── --}}
    @if($purchase->purchaseRequest)
        <div class="bg-white border rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-500">Purchase Request Terkait</p>
                    <p class="text-sm font-semibold text-purple-700">{{ $purchase->purchaseRequest->request_number }}</p>
                    @if($purchase->purchaseRequest->purpose)
                        <p class="text-xs text-gray-500 truncate">{{ $purchase->purchaseRequest->purpose }}</p>
                    @endif
                </div>
                <a href="{{ route('purchase-requests.show', $purchase->purchaseRequest) }}"
                    class="text-xs text-purple-600 hover:underline whitespace-nowrap flex-shrink-0">
                    Lihat Detail →
                </a>
            </div>
        </div>
    @endif

    {{-- ── Informasi Pembelian ─────────────────────────────────────────────── --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b">
            <h3 class="text-sm font-semibold text-gray-800">Informasi Pembelian</h3>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
                <p class="text-gray-500">Supplier</p>
                <p class="font-semibold mt-0.5 text-sm">{{ $purchase->supplier_name }}</p>
                @if($purchase->supplier_contact)
                    <p class="text-gray-500 mt-0.5">{{ $purchase->supplier_contact }}</p>
                @endif
            </div>
            <div>
                <p class="text-gray-500">Dibuat Oleh</p>
                <p class="font-semibold mt-0.5">{{ $purchase->purchaser->full_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Total Pembelian</p>
                <p class="font-bold text-xl text-gray-900 mt-0.5">
                    Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                </p>
            </div>
            @if($purchase->payment_type === 'tempo' && $purchase->tempo_date)
                <div>
                    <p class="text-gray-500">Jatuh Tempo</p>
                    <p class="font-semibold mt-0.5 {{ $isOverdue ? 'text-red-600' : 'text-orange-600' }}">
                        {{ $purchase->tempo_date->format('d M Y') }}
                        <span class="text-gray-400 font-normal ml-1">({{ $purchase->tempo_date->diffForHumans() }})</span>
                    </p>
                </div>
            @endif
            @if($purchase->notes)
                <div class="md:col-span-3">
                    <p class="text-gray-500">Catatan</p>
                    <p class="mt-0.5">{{ $purchase->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Detail Material ─────────────────────────────────────────────────── --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b">
            <h3 class="text-sm font-semibold text-gray-800">Detail Material</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Nama Material</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Satuan</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Qty</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Harga/Unit</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($purchase->details as $index => $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-blue-600">{{ $detail->material->material_code }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $detail->material->material_name }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $detail->material->unit }}</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ number_format($detail->qty_ordered, 2) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">Tidak ada data material</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t-2">
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-right font-bold text-gray-700">TOTAL:</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 text-sm">
                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ── Goods Receipt ────────────────────────────────────────────────────── --}}
    @if($purchase->goodsReceipt)
        <div class="bg-white border rounded-lg p-4">
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-lg p-3">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-green-800">Barang Sudah Diterima</p>
                    <p class="text-xs text-green-700 mt-0.5">
                        {{ $purchase->goodsReceipt->receipt_number }} —
                        {{ $purchase->goodsReceipt->receipt_date->format('d M Y') }}
                    </p>
                </div>
                <a href="{{ route('goods-receipts.show', $purchase->goodsReceipt) }}"
                    class="text-xs text-green-700 hover:underline whitespace-nowrap flex-shrink-0">
                    Detail GR →
                </a>
            </div>
        </div>
    @else
        <div class="bg-white border rounded-lg p-4">
            <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-yellow-800">Menunggu Penerimaan Barang</p>
                    <p class="text-xs text-yellow-700 mt-0.5">Buat Goods Receipt setelah barang tiba di gudang.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Jurnal Akuntansi ─────────────────────────────────────────────────── --}}
    @if($purchase->jurnal)
        <div class="bg-white border rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Jurnal Akuntansi</h3>
                <div class="flex items-center gap-2">
                    @if($purchase->jurnal->status === 'draft')
                        <form action="{{ route('accounting.jurnal.post', $purchase->jurnal) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin posting jurnal ini? Saldo perkiraan akan berubah dan tidak bisa diundo tanpa void.')">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Post Jurnal
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('accounting.jurnal.show', $purchase->jurnal) }}"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100">
                        Lihat Detail →
                    </a>
                </div>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 text-xs">
                    <div>
                        <p class="text-gray-500">Nomor Bukti</p>
                        <p class="font-semibold mt-0.5">{{ $purchase->jurnal->nomor_bukti }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tanggal</p>
                        <p class="font-semibold mt-0.5">{{ $purchase->jurnal->tanggal->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <div class="mt-0.5">
                            @if($purchase->jurnal->status === 'posted')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">✓ Posted</span>
                            @elseif($purchase->jurnal->status === 'draft')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Draft</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">Void</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500">Jenis</p>
                        <p class="font-semibold mt-0.5 capitalize">{{ $purchase->jurnal->jenis_jurnal }}</p>
                    </div>
                </div>

                @if($purchase->jurnal->keterangan)
                    <p class="text-xs text-gray-500 bg-gray-50 rounded px-3 py-2 mb-3 italic">
                        {{ $purchase->jurnal->keterangan }}
                    </p>
                @endif

                <table class="min-w-full text-xs border rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-700 text-white">
                            <th class="px-3 py-2.5 text-left font-semibold">Perkiraan</th>
                            <th class="px-3 py-2.5 text-right font-semibold text-green-300">Debet (Rp)</th>
                            <th class="px-3 py-2.5 text-right font-semibold text-red-300">Kredit (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($purchase->jurnal->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2.5">
                                    <span class="font-semibold text-blue-600">{{ $item->perkiraan->kode_perkiraan ?? '' }}</span>
                                    <span class="text-gray-600 ml-1">— {{ $item->perkiraan->nama_perkiraan ?? '' }}</span>
                                    @if($item->keterangan)
                                        <p class="text-gray-400 text-xs mt-0.5 italic">{{ $item->keterangan }}</p>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-right font-semibold {{ $item->debet > 0 ? 'text-green-600' : 'text-gray-300' }}">
                                    {{ $item->debet > 0 ? 'Rp ' . number_format($item->debet, 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-right font-semibold {{ $item->kredit > 0 ? 'text-red-600' : 'text-gray-300' }}">
                                    {{ $item->kredit > 0 ? 'Rp ' . number_format($item->kredit, 0, ',', '.') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 bg-gray-50">
                        <tr>
                            <td class="px-3 py-2.5 text-right font-bold text-gray-700">TOTAL</td>
                            <td class="px-3 py-2.5 text-right font-bold text-green-700">
                                Rp {{ number_format($purchase->jurnal->items->sum('debet'), 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2.5 text-right font-bold text-red-700">
                                Rp {{ number_format($purchase->jurnal->items->sum('kredit'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>

                @if($purchase->jurnal->status === 'draft')
                    <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2 text-xs text-yellow-800 flex items-start gap-1.5">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>
                            Jurnal masih <strong>Draft</strong> — saldo perkiraan belum berubah.
                            Klik <strong>Post Jurnal</strong> setelah memastikan data sudah benar.
                        </span>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection