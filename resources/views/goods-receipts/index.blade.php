@extends('layouts.app')

@section('title', 'Penerimaan Barang')
@section('breadcrumb', 'Materials / Goods Receipts')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Penerimaan Barang (Goods Receipt)</h2>
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperadmin())
            <a href="{{ route('goods-receipts.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Terima Barang
            </a>
        @endif
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No. Receipt</th>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">No. PO</th>
                        <th class="px-3 py-2 text-left font-semibold">Supplier</th>
                        <th class="px-3 py-2 text-left font-semibold">Warehouse</th>
                        <th class="px-3 py-2 text-left font-semibold">Diterima Oleh</th>
                        <th class="px-3 py-2 text-center font-semibold">Item</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($receipts as $receipt)
                        @php
                            $purchase = $receipt->purchase;
                            $isFullyReceived = $purchase ? $purchase->isFullyReceived() : true;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $receipt->receipt_number }}</td>
                            <td class="px-3 py-2">{{ $receipt->receipt_date->format('d M Y') }}</td>
                            <td class="px-3 py-2">
                                @if($receipt->purchase)
                                    <a href="{{ route('purchases.show', $receipt->purchase) }}" 
                                       class="text-blue-600 hover:underline">
                                        {{ $receipt->purchase->purchase_number }}
                                    </a>
                                    @if(!$isFullyReceived)
                                        <span class="block text-xs text-orange-600 mt-1">
                                            🔄 Belum lengkap
                                        </span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($receipt->purchase)
                                    <div class="font-medium">{{ $receipt->purchase->supplier_name }}</div>
                                    @if($receipt->purchase->supplier_contact)
                                        <div class="text-gray-500">{{ $receipt->purchase->supplier_contact }}</div>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-2">{{ $receipt->warehouse->warehouse_name ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $receipt->receiver->full_name ?? '-' }}</td>
                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold">
                                    {{ $receipt->details->count() }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($receipt->status === 'received')
                                    <x-badge variant="success">Received</x-badge>
                                @elseif($receipt->status === 'corrected')
                                    <x-badge variant="warning">Corrected</x-badge>
                                @else
                                    <x-badge variant="info">{{ ucfirst($receipt->status) }}</x-badge>
                                @endif
                                
                                @if($receipt->is_corrected)
                                    <span class="block text-xs text-yellow-600 mt-1">
                                        ⚠️ Ada Koreksi
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('goods-receipts.show', $receipt) }}" 
                                       class="text-blue-600 hover:text-blue-800"
                                       title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-8 text-center text-gray-500">Belum ada penerimaan barang</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $receipts->links() }}
        </div>
    </x-card>
</div>
@endsection