@extends('layouts.app')

@section('title', 'Daftar Pembelian Material')
@section('breadcrumb', 'Materials / Purchases')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Daftar Pembelian Material</h2>
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperadmin())
            <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Pembelian
            </a>
        @endif
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No. PO</th>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">No. Request</th>
                        <th class="px-3 py-2 text-left font-semibold">Supplier</th>
                        <th class="px-3 py-2 text-left font-semibold">Dibeli Oleh</th>
                        <th class="px-3 py-2 text-right font-semibold">Total</th>
                        <th class="px-3 py-2 text-center font-semibold">Status Terima</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $purchase->purchase_number }}</td>
                            <td class="px-3 py-2">{{ $purchase->purchase_date->format('d M Y') }}</td>
                            <td class="px-3 py-2">
                                @if($purchase->purchaseRequest)
                                    <a href="{{ route('purchase-requests.show', $purchase->purchaseRequest) }}" 
                                       class="text-blue-600 hover:underline">
                                        {{ $purchase->purchaseRequest->request_number }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <div class="font-medium">{{ $purchase->supplier_name }}</div>
                                @if($purchase->supplier_contact)
                                    <div class="text-gray-500">{{ $purchase->supplier_contact }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-2">{{ $purchase->purchaser->full_name ?? '-' }}</td>
                            <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-center">
                                @if($purchase->goodsReceipt)
                                    <x-badge variant="success">Sudah Diterima</x-badge>
                                @else
                                    <x-badge variant="warning">Belum Diterima</x-badge>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('purchases.show', $purchase) }}" 
                                       class="text-blue-600 hover:text-blue-800"
                                       title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    @if((auth()->user()->isAdmin() || auth()->user()->isSuperadmin()) && !$purchase->goodsReceipt)
                                        <a href="{{ route('purchases.edit', $purchase) }}" 
                                           class="text-yellow-600 hover:text-yellow-800"
                                           title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @endif

                                    @if((auth()->user()->isAdmin() || auth()->user()->isSuperadmin()) && !$purchase->goodsReceipt)
                                        <form action="{{ route('purchases.destroy', $purchase) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Yakin ingin menghapus pembelian ini?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800"
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-gray-500">Belum ada pembelian</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </x-card>
</div>
@endsection