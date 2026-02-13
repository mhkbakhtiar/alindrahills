@extends('layouts.app')

@section('title', 'Detail Batch')
@section('breadcrumb', 'Warehouse / Batch Tracking / Detail')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Detail Batch</h2>
        <x-button variant="secondary" href="{{ route('batches.index') }}">Kembali</x-button>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <x-card title="Informasi Batch" class="col-span-2">
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="font-semibold text-gray-700">Batch Number</dt>
                    <dd class="text-gray-900 font-medium">{{ $batch->batch_number }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Status</dt>
                    <dd>
                        <x-badge :variant="$batch->status === 'active' ? 'success' : 'default'">
                            {{ ucfirst($batch->status) }}
                        </x-badge>
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Material</dt>
                    <dd class="text-gray-900">{{ $batch->material->material_name }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Kode Material</dt>
                    <dd class="text-gray-900">{{ $batch->material->material_code }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Gudang</dt>
                    <dd class="text-gray-900">{{ $batch->warehouse->warehouse_name }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Tanggal Pembelian</dt>
                    <dd class="text-gray-900">{{ $batch->purchase_date->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Harga per Unit</dt>
                    <dd class="text-gray-900 font-medium">Rp {{ number_format($batch->unit_price, 0) }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Umur Batch</dt>
                    <dd class="text-gray-900">{{ $batch->age_days }} hari</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Qty Masuk</dt>
                    <dd class="text-gray-900">{{ number_format($batch->qty_in, 2) }} {{ $batch->material->unit }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Qty Sisa</dt>
                    <dd class="text-gray-900 font-bold">{{ number_format($batch->qty_remaining, 2) }} {{ $batch->material->unit }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Qty Terpakai</dt>
                    <dd class="text-red-600 font-medium">{{ number_format($batch->qty_in - $batch->qty_remaining, 2) }} {{ $batch->material->unit }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">% Terpakai</dt>
                    <dd class="text-gray-900">{{ $batch->qty_in > 0 ? number_format((($batch->qty_in - $batch->qty_remaining) / $batch->qty_in) * 100, 1) : 0 }}%</dd>
                </div>
            </dl>
        </x-card>

        <x-card title="Nilai Batch">
            <div class="space-y-4">
                <div class="text-center py-4 border-b">
                    <p class="text-xs text-gray-600">Total Nilai Sisa</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($batch->total_value, 0) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-600">Nilai Awal</p>
                    <p class="text-base font-medium text-gray-700">Rp {{ number_format($batch->qty_in * $batch->unit_price, 0) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-600">Nilai Terpakai</p>
                    <p class="text-base font-medium text-red-600">Rp {{ number_format(($batch->qty_in - $batch->qty_remaining) * $batch->unit_price, 0) }}</p>
                </div>
            </div>
        </x-card>
    </div>

    @if($batch->goodsReceipt)
    <x-card title="Informasi Penerimaan">
        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="font-semibold text-gray-700">No. Penerimaan</dt>
                <dd>
                    <a href="{{ route('goods-receipts.show', $batch->goodsReceipt) }}" class="text-blue-600 hover:text-blue-800">
                        {{ $batch->goodsReceipt->receipt_number }}
                    </a>
                </dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-700">Tanggal Penerimaan</dt>
                <dd class="text-gray-900">{{ $batch->goodsReceipt->receipt_date->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-700">Diterima Oleh</dt>
                <dd class="text-gray-900">{{ $batch->goodsReceipt->receiver->full_name }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-700">Status Penerimaan</dt>
                <dd>
                    @php
                        $statusVariants = ['received' => 'success', 'partial' => 'warning', 'corrected' => 'info'];
                    @endphp
                    <x-badge :variant="$statusVariants[$batch->goodsReceipt->status]">
                        {{ ucfirst($batch->goodsReceipt->status) }}
                    </x-badge>
                </dd>
            </div>
        </dl>
    </x-card>
    @endif
</div>
@endsection