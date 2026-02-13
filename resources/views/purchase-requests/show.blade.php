@extends('layouts.app')

@section('title', 'Detail Pengajuan Pembelian')
@section('breadcrumb', 'Purchase / Pengajuan Pembelian / Detail')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Detail Pengajuan Pembelian</h2>
            <p class="text-sm text-gray-600">{{ $purchaseRequest->request_number }}</p>
        </div>
        <div class="flex gap-2">
            <!-- Print PDF Button -->
            <a href="{{ route('purchase-requests.print-invoice', $purchaseRequest) }}" 
               target="_blank"
               class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak PDF
            </a>

            @if($purchaseRequest->status === 'pending' && (auth()->user()->isAdmin() || auth()->user()->isSuperadmin()))
                <form action="{{ route('purchase-requests.approve', $purchaseRequest) }}" method="POST" 
                    onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approve
                    </button>
                </form>
            @endif
            
            <a href="{{ route('purchase-requests.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-4">
        @php
            $statusVariants = [
                'pending' => 'warning', 
                'approved' => 'success', 
                'rejected' => 'danger', 
                'purchased' => 'info'
            ];
            $statusLabels = [
                'pending' => 'Menunggu Persetujuan',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'purchased' => 'Sudah Dibeli'
            ];
        @endphp
        <x-badge :variant="$statusVariants[$purchaseRequest->status]" class="text-sm px-4 py-2">
            {{ $statusLabels[$purchaseRequest->status] }}
        </x-badge>
    </div>

    <!-- Request Information -->
    <x-card class="mb-4">
        <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Pengajuan</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Pengajuan</label>
                <p class="text-sm text-gray-900 font-medium">{{ $purchaseRequest->request_number }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Pengajuan</label>
                <p class="text-sm text-gray-900">{{ $purchaseRequest->request_date->format('d F Y') }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Surat</label>
                <p class="text-sm text-gray-900">{{ $purchaseRequest->letter_number }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Surat</label>
                <p class="text-sm text-gray-900">{{ $purchaseRequest->letter_date->format('d F Y') }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Diajukan Oleh</label>
                <p class="text-sm text-gray-900">{{ $purchaseRequest->requester->full_name }}</p>
            </div>

            @if($purchaseRequest->status !== 'pending')
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">
                        @if($purchaseRequest->status === 'approved')
                            Disetujui Oleh
                        @else
                            Diproses Oleh
                        @endif
                    </label>
                    <p class="text-sm text-gray-900">
                        {{ $purchaseRequest->approver->full_name ?? '-' }}
                    </p>
                </div>
            @endif

            @if($purchaseRequest->approved_date)
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Persetujuan</label>
                    <p class="text-sm text-gray-900">{{ $purchaseRequest->approved_date->format('d F Y H:i') }}</p>
                </div>
            @endif

            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Tujuan Pembelian</label>
                <p class="text-sm text-gray-900">{{ $purchaseRequest->purpose }}</p>
            </div>

            @if($purchaseRequest->notes)
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                    <p class="text-sm text-gray-900">{{ $purchaseRequest->notes }}</p>
                </div>
            @endif
        </div>
    </x-card>

    <!-- Material Details -->
    <x-card class="mb-4">
        <h3 class="text-md font-semibold text-gray-900 mb-4">Daftar Material yang Diajukan</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No</th>
                        <th class="px-3 py-2 text-left font-semibold">Kode Material</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Material</th>
                        <th class="px-3 py-2 text-center font-semibold">Satuan</th>
                        <th class="px-3 py-2 text-right font-semibold">Qty Diajukan</th>
                        <th class="px-3 py-2 text-left font-semibold">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($purchaseRequest->details as $index => $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-medium">{{ $detail->material->material_code }}</td>
                            <td class="px-3 py-2">{{ $detail->material->material_name }}</td>
                            <td class="px-3 py-2 text-center">{{ $detail->material->unit }}</td>
                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($detail->qty_requested, 2) }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $detail->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-gray-500">Tidak ada data material</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-3 py-3 text-right font-bold text-gray-900">Total Item:</td>
                        <td colspan="2" class="px-3 py-3 font-bold text-gray-900">
                            {{ $purchaseRequest->details->count() }} item
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-card>

    <!-- Purchase Info (if exists) -->
    @if($purchaseRequest->purchase)
        <x-card>
            <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-md">
                <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-green-900">Sudah Dibelikan</h4>
                    <p class="text-xs text-green-700">
                        Purchase Order: <strong>{{ $purchaseRequest->purchase->purchase_number }}</strong>
                        {{-- @if($purchaseRequest->purchase->supplier)
                            | Supplier: {{ $purchaseRequest->purchase->supplier->supplier_name }}
                        @endif --}}
                    </p>
                </div>
            </div>
        </x-card>
    @endif
</div>
@endsection