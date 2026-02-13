@extends('layouts.app')

@section('title', 'Detail Pembelian')
@section('breadcrumb', 'Materials / Purchases / Detail')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Detail Pembelian Material</h2>
            <p class="text-sm text-gray-600">{{ $purchase->purchase_number }}</p>
        </div>
        <div class="flex gap-2">
            @if((auth()->user()->isAdmin() || auth()->user()->isSuperadmin()) && !$purchase->goodsReceipt)
                <a href="{{ route('purchases.edit', $purchase) }}" class="px-4 py-2 bg-yellow-600 text-white text-sm rounded-md hover:bg-yellow-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            @endif
            
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Purchase Request Info (if exists) -->
    @if($purchase->purchaseRequest)
        <x-card class="mb-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-purple-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-semibold text-purple-900 mb-1">📋 Purchase Request Terkait</h3>
                    <p class="text-base font-bold text-purple-900">{{ $purchase->purchaseRequest->request_number }}</p>
                    <p class="text-sm text-purple-800 mb-2">{{ $purchase->purchaseRequest->purpose }}</p>
                    <a href="{{ route('purchase-requests.show', $purchase->purchaseRequest) }}" 
                       class="text-xs text-purple-700 hover:underline">
                        Lihat Detail Request →
                    </a>
                </div>
            </div>
        </x-card>
    @endif

    <!-- Purchase Information -->
    <x-card class="mb-4">
        <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Pembelian</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nomor PO</label>
                <p class="text-sm text-gray-900 font-medium">{{ $purchase->purchase_number }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Pembelian</label>
                <p class="text-sm text-gray-900">{{ $purchase->purchase_date->format('d F Y') }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Supplier</label>
                <p class="text-sm text-gray-900 font-medium">{{ $purchase->supplier_name }}</p>
                @if($purchase->supplier_contact)
                    <p class="text-xs text-gray-600">{{ $purchase->supplier_contact }}</p>
                @endif
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Dibeli Oleh</label>
                <p class="text-sm text-gray-900">{{ $purchase->purchaser->full_name ?? '-' }}</p>
            </div>

            @if($purchase->notes)
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                    <p class="text-sm text-gray-900">{{ $purchase->notes }}</p>
                </div>
            @endif
        </div>
    </x-card>

    <!-- Material Details -->
    <x-card class="mb-4">
        <h3 class="text-md font-semibold text-gray-900 mb-4">Daftar Material yang Dibeli</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No</th>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Material</th>
                        <th class="px-3 py-2 text-center font-semibold">Satuan</th>
                        <th class="px-3 py-2 text-right font-semibold">Qty</th>
                        <th class="px-3 py-2 text-right font-semibold">Harga/Unit</th>
                        <th class="px-3 py-2 text-right font-semibold">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($purchase->details as $index => $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-medium">{{ $detail->material->material_code }}</td>
                            <td class="px-3 py-2">{{ $detail->material->material_name }}</td>
                            <td class="px-3 py-2 text-center">{{ $detail->material->unit }}</td>
                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($detail->qty_ordered, 2) }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right font-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="6" class="px-3 py-3 text-right font-bold text-gray-900">TOTAL:</td>
                        <td class="px-3 py-3 text-right font-bold text-gray-900">
                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-card>

    <!-- Goods Receipt Info (if exists) -->
    @if($purchase->goodsReceipt)
        <x-card>
            <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-md">
                <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-green-900">Barang Sudah Diterima</h4>
                    <p class="text-xs text-green-700">
                        Receipt Number: <strong>{{ $purchase->goodsReceipt->receipt_number }}</strong> | 
                        Tanggal: {{ $purchase->goodsReceipt->receipt_date->format('d M Y') }}
                    </p>
                </div>
            </div>
        </x-card>
    @else
        <x-card>
            <div class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-yellow-900">Menunggu Penerimaan Barang</h4>
                    <p class="text-xs text-yellow-700">
                        Barang belum diterima. Silakan buat Goods Receipt setelah barang sampai.
                    </p>
                </div>
            </div>
        </x-card>
    @endif

    {{-- Informasi Jurnal --}}
    @if($purchase->jurnal)
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold">Jurnal Akuntansi</h3>
            @if($purchase->jurnal->status === 'draft')
                <form action="{{ route('purchases.post-jurnal', $purchase) }}" method="POST" 
                    onsubmit="return confirm('Yakin ingin posting jurnal ini?')">
                    @csrf
                    <x-button type="submit" variant="success" class="!py-1 !px-3">
                        ✓ Post Jurnal
                    </x-button>
                </form>
            @else
                <x-badge variant="success">Posted</x-badge>
            @endif
        </div>
        
        <div class="space-y-2 text-xs">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <span class="text-gray-500">Nomor Bukti:</span>
                    <span class="font-medium">{{ $purchase->jurnal->nomor_bukti }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Tanggal:</span>
                    <span class="font-medium">{{ $purchase->jurnal->tanggal->format('d/m/Y') }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-gray-500">Keterangan:</span>
                    <span class="font-medium">{{ $purchase->jurnal->keterangan }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Status:</span>
                    <x-badge variant="{{ $purchase->jurnal->status === 'posted' ? 'success' : 'warning' }}">
                        {{ ucfirst($purchase->jurnal->status) }}
                    </x-badge>
                </div>
            </div>

            <table class="min-w-full mt-3">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-semibold">Perkiraan</th>
                        <th class="px-2 py-2 text-right text-xs font-semibold">Debet</th>
                        <th class="px-2 py-2 text-right text-xs font-semibold">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->jurnal->items as $item)
                        <tr class="border-b">
                            <td class="px-2 py-2">
                                {{ $item->perkiraan->kode_perkiraan ?? '' }} - {{ $item->perkiraan->nama_perkiraan ?? '' }}
                            </td>
                            <td class="px-2 py-2 text-right">
                                {{ $item->debet > 0 ? 'Rp ' . number_format($item->debet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-2 py-2 text-right">
                                {{ $item->kredit > 0 ? 'Rp ' . number_format($item->kredit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                <x-button variant="info" href="{{ route('accounting.jurnal.show', $purchase->jurnal) }}" class="!py-1 !px-3">
                    Lihat Detail Jurnal
                </x-button>
            </div>
        </div>
    </x-card>
    @endif
</div>
@endsection