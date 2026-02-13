@extends('layouts.app')

@section('title', 'Batch Stock Material')
@section('breadcrumb', 'Stock / Batch Material')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Batch Stock Material</h2>
            <p class="text-sm text-gray-600">Daftar batch material berdasarkan tanggal pembelian (FIFO)</p>
        </div>
        <button onclick="window.history.back()" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </button>
    </div>

    @if($batches->isNotEmpty())
        <!-- Material & Warehouse Info -->
        <x-card class="mb-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-semibold text-blue-900 mb-1">📦 Informasi Material</h3>
                    <p class="text-base font-bold text-blue-900">{{ $batches->first()->material->material_code }} - {{ $batches->first()->material->material_name }}</p>
                    <div class="flex items-center text-xs text-blue-700 mt-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="font-medium">{{ $batches->first()->warehouse->warehouse_name }}</span>
                    </div>
                    <div class="flex items-center text-xs text-blue-700 mt-1">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>{{ $batches->count() }} Batch Tersedia</span>
                        <span class="mx-2">•</span>
                        <span class="font-semibold">Total Stock: {{ number_format($batches->sum('quantity'), 2) }} {{ $batches->first()->material->unit }}</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Batches Table -->
        <x-card>
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-md font-semibold text-gray-900">Daftar Batch (Urut FIFO)</h3>
                <div class="flex items-center text-xs text-gray-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Batch diurutkan berdasarkan tanggal pembelian (yang lama akan digunakan terlebih dahulu)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">No</th>
                            <th class="px-3 py-2 text-left font-semibold">Batch Number</th>
                            <th class="px-3 py-2 text-left font-semibold">Supplier</th>
                            <th class="px-3 py-2 text-center font-semibold">Tanggal Pembelian</th>
                            <th class="px-3 py-2 text-right font-semibold">Quantity Awal</th>
                            <th class="px-3 py-2 text-right font-semibold">Quantity Sisa</th>
                            <th class="px-3 py-2 text-right font-semibold">Harga/Unit</th>
                            <th class="px-3 py-2 text-right font-semibold">Total Nilai</th>
                            <th class="px-3 py-2 text-center font-semibold">Status</th>
                            <th class="px-3 py-2 text-center font-semibold">Umur Batch</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($batches as $index => $batch)
                            @php
                                $percentageRemaining = $batch->initial_quantity > 0 
                                    ? ($batch->quantity / $batch->initial_quantity) * 100 
                                    : 0;
                                
                                $daysSincePurchase = $batch->purchase_date->diffInDays(now());
                                
                                // Color coding based on remaining percentage
                                $stockColorClass = '';
                                if ($percentageRemaining > 70) {
                                    $stockColorClass = 'text-green-600 bg-green-50';
                                } elseif ($percentageRemaining > 30) {
                                    $stockColorClass = 'text-yellow-600 bg-yellow-50';
                                } else {
                                    $stockColorClass = 'text-red-600 bg-red-50';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $index === 0 ? 'bg-blue-50' : '' }}">
                                <td class="px-3 py-2">
                                    <div class="flex items-center">
                                        {{ $index + 1 }}
                                        @if($index === 0)
                                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-blue-500 text-white rounded">
                                                NEXT
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="font-medium text-gray-900">{{ $batch->batch_number }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    {{ $batch->supplier->supplier_name ?? '-' }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <div>{{ $batch->purchase_date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $batch->purchase_date->format('H:i') }}</div>
                                </td>
                                <td class="px-3 py-2 text-right text-gray-500">
                                    {{ number_format($batch->initial_quantity, 2) }} {{ $batch->material->unit }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <div class="font-semibold {{ $stockColorClass }} px-2 py-1 rounded">
                                        {{ number_format($batch->quantity, 2) }} {{ $batch->material->unit }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ number_format($percentageRemaining, 1) }}% tersisa
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    Rp {{ number_format($batch->unit_price, 0) }}
                                </td>
                                <td class="px-3 py-2 text-right font-medium">
                                    Rp {{ number_format($batch->quantity * $batch->unit_price, 0) }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if($batch->status === 'active')
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">
                                            Active
                                        </span>
                                    @elseif($batch->status === 'depleted')
                                        <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">
                                            Depleted
                                        </span>
                                    @elseif($batch->status === 'expired')
                                        <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded">
                                            Expired
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <div class="text-sm">{{ $daysSincePurchase }} hari</div>
                                    @if($daysSincePurchase > 90)
                                        <div class="text-xs text-red-600 font-semibold">⚠️ Lama</div>
                                    @elseif($daysSincePurchase > 60)
                                        <div class="text-xs text-yellow-600">⚠️ Cukup lama</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="px-3 py-3 text-right font-bold text-gray-900">Total:</td>
                            <td class="px-3 py-3 text-right font-bold text-gray-900">
                                {{ number_format($batches->sum('quantity'), 2) }} {{ $batches->first()->material->unit }}
                            </td>
                            <td class="px-3 py-3"></td>
                            <td class="px-3 py-3 text-right font-bold text-gray-900">
                                Rp {{ number_format($batches->sum(function($batch) { return $batch->quantity * $batch->unit_price; }), 0) }}
                            </td>
                            <td colspan="2" class="px-3 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- FIFO Explanation -->
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-blue-900">
                        <p class="font-semibold mb-1">Sistem FIFO (First In First Out)</p>
                        <p class="text-xs text-blue-800">
                            Material dari batch dengan tanggal pembelian paling lama akan digunakan terlebih dahulu. 
                            Batch yang ditandai <span class="px-2 py-0.5 bg-blue-500 text-white rounded text-xs font-semibold">NEXT</span> 
                            akan digunakan pada penggunaan material berikutnya.
                        </p>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
            <x-card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500">Total Batch</p>
                        <p class="text-xl font-bold text-gray-900">{{ $batches->count() }}</p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500">Total Stock</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($batches->sum('quantity'), 2) }}</p>
                        <p class="text-xs text-gray-500">{{ $batches->first()->material->unit }}</p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500">Total Nilai</p>
                        <p class="text-lg font-bold text-gray-900">
                            Rp {{ number_format($batches->sum(function($batch) { return $batch->quantity * $batch->unit_price; }) / 1000000, 2) }}M
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500">Rata-rata Umur</p>
                        <p class="text-xl font-bold text-gray-900">
                            {{ round($batches->avg(function($batch) { return $batch->purchase_date->diffInDays(now()); })) }}
                        </p>
                        <p class="text-xs text-gray-500">hari</p>
                    </div>
                </div>
            </x-card>
        </div>

    @else
        <!-- Empty State -->
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada batch</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ada batch material yang tersedia untuk material dan warehouse yang dipilih.</p>
                <div class="mt-6">
                    <button onclick="window.history.back()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                        Kembali
                    </button>
                </div>
            </div>
        </x-card>
    @endif
</div>
@endsection