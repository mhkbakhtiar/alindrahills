@extends('layouts.app')

@section('title', 'Detail Kegiatan')
@section('breadcrumb', 'Project / Kegiatan / Detail')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Detail Kegiatan</h2>
        <div class="flex gap-2">
            <x-button variant="primary" href="{{ route('material-usages.create', ['activity_id' => $activity->activity_id]) }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Catat Penggunaan Material
            </x-button>
            <x-button variant="secondary" href="{{ route('activities.edit', $activity) }}">Edit</x-button>
            <x-button variant="secondary" href="{{ route('activities.index') }}">Kembali</x-button>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <x-card title="Informasi Kegiatan" class="col-span-2">
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="font-semibold text-gray-700">Kode Kegiatan</dt>
                    <dd class="text-gray-900">{{ $activity->activity_code }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Nama Kegiatan</dt>
                    <dd class="text-gray-900">{{ $activity->activity_name }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Lokasi</dt>
                    <dd class="text-gray-900">{{ $activity->location->kavling }} - {{ $activity->location->blok }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Jenis Kegiatan</dt>
                    <dd class="text-gray-900">{{ $activity->activity_type }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Tanggal Mulai</dt>
                    <dd class="text-gray-900">{{ $activity->start_date->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Tanggal Selesai</dt>
                    <dd class="text-gray-900">{{ $activity->end_date ? $activity->end_date->format('d M Y') : '-' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Status</dt>
                    <dd>
                        @php
                            $statusVariants = ['planned' => 'default', 'ongoing' => 'warning', 'completed' => 'success', 'cancelled' => 'danger'];
                        @endphp
                        <x-badge :variant="$statusVariants[$activity->status]">{{ ucfirst($activity->status) }}</x-badge>
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Dibuat oleh</dt>
                    <dd class="text-gray-900">{{ $activity->creator->full_name ?? '-' }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="font-semibold text-gray-700">Deskripsi</dt>
                    <dd class="text-gray-900">{{ $activity->description ?: '-' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card title="Statistik">
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-700">Total Tukang:</span>
                    <span class="font-bold text-gray-900">{{ $activity->total_workers }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Penggunaan Material:</span>
                    <span class="font-bold text-blue-600">{{ $activity->materialUsages->count() }}x</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Total Biaya Material:</span>
                    <span class="font-bold text-green-600">Rp {{ number_format($activity->total_material_cost, 0) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Durasi:</span>
                    <span class="font-medium">
                        @if($activity->end_date)
                            {{ $activity->start_date->diffInDays($activity->end_date) }} hari
                        @else
                            {{ $activity->start_date->diffInDays(now()) }} hari (ongoing)
                        @endif
                    </span>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Tukang yang Ditugaskan -->
    <x-card title="Tukang yang Ditugaskan">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold">Kode</th>
                    <th class="px-3 py-2 text-left font-semibold">Nama Tukang</th>
                    <th class="px-3 py-2 text-left font-semibold">Jenis Tukang</th>
                    <th class="px-3 py-2 text-right font-semibold">Upah Harian</th>
                    <th class="px-3 py-2 text-left font-semibold">Tanggal Ditugaskan</th>
                    <th class="px-3 py-2 text-left font-semibold">Deskripsi Pekerjaan</th>
                    <th class="px-3 py-2 text-center font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($activity->activityWorkers as $assignment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium">{{ $assignment->worker->worker_code }}</td>
                        <td class="px-3 py-2">{{ $assignment->worker->full_name }}</td>
                        <td class="px-3 py-2">{{ $assignment->worker->worker_type }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($assignment->worker->daily_rate, 0) }}</td>
                        <td class="px-3 py-2">{{ $assignment->assigned_date->format('d M Y') }}</td>
                        <td class="px-3 py-2">{{ $assignment->work_description ?: '-' }}</td>
                        <td class="px-3 py-2 text-center">
                            <x-badge :variant="$assignment->is_active ? 'success' : 'danger'">
                                {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                            </x-badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-8 text-center text-gray-500">
                            Belum ada tukang yang ditugaskan
                            <div class="mt-2">
                                <a href="{{ route('activities.edit', $activity) }}" class="text-blue-600 hover:text-blue-800">Tambah Tukang</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <!-- Contractor Detail Section -->
    <x-card title="Contractor Terlibat">
        @if($activity->contractors->count())
            <div class="grid grid-cols-2 gap-3 text-sm">
                @foreach($activity->contractors as $c)
                    <div class="border rounded p-3 bg-gray-50">
                        <div class="text-xs text-gray-500 mb-1">
                            {{ $c->contractor_code }}
                        </div>

                        <div class="font-semibold text-gray-800">
                            {{ $c->contractor_name }}
                        </div>

                        <div class="text-xs text-gray-600 mt-1 space-y-0.5">
                            <div>
                                <span class="font-medium">PIC:</span>
                                {{ $c->pic_name ?? '-' }}
                            </div>
                            <div>
                                <span class="font-medium">Telepon:</span>
                                {{ $c->phone ?? '-' }}
                            </div>
                            <div>
                                <span class="font-medium">Alamat:</span>
                                {{ $c->address ?? '-' }}
                            </div>
                        </div>

                        <div class="mt-2">
                            <x-badge :variant="$c->status === 'active' ? 'success' : 'danger'">
                                {{ ucfirst($c->status) }}
                            </x-badge>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-sm text-gray-500 py-6">
                Belum ada contractor yang ditugaskan
            </div>
        @endif
    </x-card>


    <!-- Rincian Penggunaan Material -->
    <x-card title="Rincian Penggunaan Material">
        <div class="mb-3 flex justify-between items-center">
            <p class="text-sm text-gray-700">Total Material yang Digunakan pada Kegiatan Ini</p>
            <x-button variant="primary" size="sm" href="{{ route('material-usages.create', ['activity_id' => $activity->activity_id]) }}">
                + Tambah Penggunaan
            </x-button>
        </div>

        @if($activity->materialUsages && $activity->materialUsages->count() > 0)
            <!-- Summary per Material -->
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                <p class="text-xs font-semibold text-blue-800 mb-2">Ringkasan Material:</p>
                <div class="grid grid-cols-4 gap-3">
                    @php
                        $materialSummary = [];
                        foreach($activity->materialUsages as $usage) {
                            foreach($usage->details as $detail) {
                                $matId = $detail->material_id;
                                if (!isset($materialSummary[$matId])) {
                                    $materialSummary[$matId] = [
                                        'name' => $detail->material->material_name,
                                        'code' => $detail->material->material_code,
                                        'unit' => $detail->material->unit,
                                        'qty' => 0,
                                        'value' => 0
                                    ];
                                }
                                $materialSummary[$matId]['qty'] += $detail->qty_used;
                                $materialSummary[$matId]['value'] += $detail->subtotal;
                            }
                        }
                    @endphp
                    @foreach($materialSummary as $summary)
                        <div class="text-xs">
                            <p class="font-semibold text-blue-900">{{ $summary['code'] }}</p>
                            <p class="text-blue-700">{{ $summary['name'] }}</p>
                            <p class="font-medium text-blue-800">{{ number_format($summary['qty'], 2) }} {{ $summary['unit'] }}</p>
                            <p class="text-blue-600">Rp {{ number_format($summary['value'], 0) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Detail per Transaksi -->
            <div class="space-y-3">
                @foreach($activity->materialUsages as $usage)
                    <div class="border rounded p-3 hover:bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-semibold text-sm text-gray-900">{{ $usage->usage_number }}</p>
                                <p class="text-xs text-gray-600">{{ $usage->usage_date->format('d M Y') }} • {{ $usage->warehouse->warehouse_name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-sm text-green-600">Rp {{ number_format($usage->total_value, 0) }}</p>
                                <a href="{{ route('material-usages.show', $usage) }}" class="text-xs text-blue-600 hover:text-blue-800">Detail →</a>
                            </div>
                        </div>

                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-left font-semibold">Material</th>
                                    <th class="px-2 py-1 text-right font-semibold">Qty</th>
                                    <th class="px-2 py-1 text-right font-semibold">Harga Rata-rata</th>
                                    <th class="px-2 py-1 text-right font-semibold">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($usage->details as $detail)
                                    <tr>
                                        <td class="px-2 py-1">
                                            <span class="font-medium">{{ $detail->material->material_code }}</span> - {{ $detail->material->material_name }}
                                        </td>
                                        <td class="px-2 py-1 text-right">{{ number_format($detail->qty_used, 2) }} {{ $detail->material->unit }}</td>
                                        <td class="px-2 py-1 text-right">Rp {{ number_format($detail->average_unit_price, 0) }}</td>
                                        <td class="px-2 py-1 text-right">Rp {{ number_format($detail->subtotal, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if($usage->notes)
                            <p class="text-xs text-gray-600 mt-2 italic">Catatan: {{ $usage->notes }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Total Keseluruhan -->
            <div class="mt-4 pt-4 border-t">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-sm">Total Biaya Material Kegiatan Ini:</span>
                    <span class="text-xl font-bold text-green-600">Rp {{ number_format($activity->total_material_cost, 0) }}</span>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p class="text-gray-500 text-sm mb-3">Belum ada material yang digunakan untuk kegiatan ini</p>
                <x-button variant="primary" size="sm" href="{{ route('material-usages.create', ['activity_id' => $activity->activity_id]) }}">
                    Catat Penggunaan Material Pertama
                </x-button>
            </div>
        @endif
    </x-card>
</div>
@endsection