@extends('layouts.app')

@section('title', 'Detail Pengeluaran Material')
@section('breadcrumb', 'Project / Pengeluaran Material / Detail')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Detail Pengeluaran Material</h2>
        <x-button variant="secondary" href="{{ route('material-usages.index') }}">Kembali</x-button>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <x-card title="Informasi Pengeluaran" class="col-span-2">
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="font-semibold text-gray-700">No. Pengeluaran</dt>
                    <dd class="text-gray-900">{{ $materialUsage->usage_number }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Tanggal</dt>
                    <dd class="text-gray-900">{{ $materialUsage->usage_date->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Kegiatan</dt>
                    <dd class="text-gray-900">{{ $materialUsage->activity->activity_code }} - {{ $materialUsage->activity->activity_name }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Lokasi</dt>
                    <dd class="text-gray-900">{{ $materialUsage->activity->location->kavling }} - {{ $materialUsage->activity->location->blok }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Gudang</dt>
                    <dd class="text-gray-900">{{ $materialUsage->warehouse->warehouse_name }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Dikeluarkan Oleh</dt>
                    <dd class="text-gray-900">{{ $materialUsage->issuer->full_name }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="font-semibold text-gray-700">Catatan</dt>
                    <dd class="text-gray-900">{{ $materialUsage->notes ?: '-' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card title="Total Nilai">
            <div class="text-center py-4">
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($materialUsage->total_value, 0) }}</p>
                <p class="text-xs text-gray-600 mt-1">Total nilai material</p>
            </div>
            <div class="mt-4 pt-4 border-t text-xs">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-700">Total Item:</span>
                    <span class="font-medium">{{ $materialUsage->details->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Metode:</span>
                    <span class="font-medium">FIFO</span>
                </div>
            </div>
        </x-card>
    </div>

    <x-card title="Detail Material yang Digunakan">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold">Kode</th>
                    <th class="px-3 py-2 text-left font-semibold">Nama Material</th>
                    <th class="px-3 py-2 text-center font-semibold">Satuan</th>
                    <th class="px-3 py-2 text-right font-semibold">Qty Digunakan</th>
                    <th class="px-3 py-2 text-right font-semibold">Harga Rata-rata</th>
                    <th class="px-3 py-2 text-right font-semibold">Subtotal</th>
                    <th class="px-3 py-2 text-left font-semibold">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($materialUsage->details as $detail)
                    <tr>
                        <td class="px-3 py-2 font-medium">{{ $detail->material->material_code }}</td>
                        <td class="px-3 py-2">{{ $detail->material->material_name }}</td>
                        <td class="px-3 py-2 text-center">{{ $detail->material->unit }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($detail->qty_used, 2) }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($detail->average_unit_price, 0) }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($detail->subtotal, 0) }}</td>
                        <td class="px-3 py-2">{{ $detail->notes ?: '-' }}</td>
                    </tr>
                @endforeach
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="5" class="px-3 py-2 text-right">TOTAL</td>
                    <td class="px-3 py-2 text-right">Rp {{ number_format($materialUsage->total_value, 0) }}</td>
                    <td class="px-3 py-2"></td>
                </tr>
            </tbody>
        </table>
    </x-card>

    <!-- Batch Details (if available) -->
    @if($materialUsage->details->first() && method_exists($materialUsage->details->first(), 'batchDetails'))
        <x-card title="Detail Batch yang Digunakan (FIFO)">
            <div class="space-y-4">
                @foreach($materialUsage->details as $detail)
                    @if($detail->batchDetails && $detail->batchDetails->count() > 0)
                        <div class="border rounded p-3">
                            <p class="text-sm font-semibold text-gray-900 mb-2">{{ $detail->material->material_name }}</p>
                            <table class="min-w-full text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-2 py-1 text-left font-semibold">Batch Number</th>
                                        <th class="px-2 py-1 text-left font-semibold">Tanggal Beli</th>
                                        <th class="px-2 py-1 text-right font-semibold">Qty Diambil</th>
                                        <th class="px-2 py-1 text-right font-semibold">Harga/Unit</th>
                                        <th class="px-2 py-1 text-right font-semibold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($detail->batchDetails as $batchDetail)
                                        <tr>
                                            <td class="px-2 py-1">{{ $batchDetail->batch->batch_number }}</td>
                                            <td class="px-2 py-1">{{ $batchDetail->batch->purchase_date->format('d M Y') }}</td>
                                            <td class="px-2 py-1 text-right">{{ number_format($batchDetail->qty_used, 2) }}</td>
                                            <td class="px-2 py-1 text-right">Rp {{ number_format($batchDetail->unit_price, 0) }}</td>
                                            <td class="px-2 py-1 text-right">Rp {{ number_format($batchDetail->subtotal, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endforeach
            </div>
        </x-card>
    @endif

    <!-- Stock Mutations -->
    <x-card title="Mutasi Stok">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold">Material</th>
                    <th class="px-3 py-2 text-right font-semibold">Stok Sebelum</th>
                    <th class="px-3 py-2 text-right font-semibold">Qty Keluar</th>
                    <th class="px-3 py-2 text-right font-semibold">Stok Sesudah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($mutations ?? [] as $mutation)
                    <tr>
                        <td class="px-3 py-2">{{ $mutation->material->material_name }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($mutation->stock_before, 2) }}</td>
                        <td class="px-3 py-2 text-right text-red-600 font-medium">{{ number_format($mutation->qty, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($mutation->stock_after, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</div>
@endsection