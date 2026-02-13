@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-4">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg p-6 text-white">
        <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->full_name }}!</h1>
        <p class="text-blue-100">Sistem Logistik & Manajemen Material Konstruksi</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4">
        <x-card padding="false">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Total Material</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_materials'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>
        </x-card>

        <x-card padding="false">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Pending Request</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_requests'] }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </x-card>

        <x-card padding="false">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Kegiatan Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active_activities'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>
        </x-card>

        <x-card padding="false">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Low Stock Alert</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['low_stock_items'] }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Workflow Section -->
    <x-card title="🔄 Alur Kerja Sistem Logistik" padding="false">
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-6">Ikuti alur kerja berikut untuk menggunakan sistem secara optimal</p>
            
            <!-- Step 1: Setup Master Data -->
            <div class="mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">1</div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-bold text-gray-900 mb-2">📋 Setup Master Data</h3>
                        <p class="text-sm text-gray-600 mb-3">Langkah pertama: Input semua data master yang diperlukan</p>
                        <div class="grid grid-cols-4 gap-3">
                            <a href="{{ route('materials.index') }}" class="p-3 border border-blue-200 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <div>
                                        <p class="text-xs font-semibold text-blue-900">Master Material</p>
                                        <p class="text-xs text-blue-700">{{ $stats['total_materials'] }} item</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('workers.index') }}" class="p-3 border border-blue-200 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-xs font-semibold text-blue-900">Data Tukang</p>
                                        <p class="text-xs text-blue-700">Manage</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('locations.index') }}" class="p-3 border border-blue-200 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-xs font-semibold text-blue-900">Lokasi Proyek</p>
                                        <p class="text-xs text-blue-700">Kavling & Blok</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('stocks.index') }}" class="p-3 border border-blue-200 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <div>
                                        <p class="text-xs font-semibold text-blue-900">Cek Gudang</p>
                                        <p class="text-xs text-blue-700">Stock Status</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="flex items-center justify-center my-4">
                <div class="border-l-2 border-dashed border-gray-300 h-8"></div>
            </div>

            <!-- Step 2: Pengajuan Pembelian -->
            <div class="mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-yellow-600 text-white rounded-full flex items-center justify-center font-bold">2</div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-bold text-gray-900 mb-2">📝 Pengajuan Pembelian Material</h3>
                        <p class="text-sm text-gray-600 mb-3">Bagian Teknik mengajukan pembelian material dengan surat pengantar</p>
                        <div class="grid grid-cols-3 gap-3">
                            @if(auth()->user()->isTeknik() || (auth()->user()->isSuperadmin()))
                                <a href="{{ route('purchase-requests.create') }}" class="p-4 border-2 border-yellow-300 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <x-badge variant="warning">Action</x-badge>
                                    </div>
                                    <p class="text-sm font-bold text-yellow-900">Buat Pengajuan</p>
                                    <p class="text-xs text-yellow-700">Ajukan pembelian material</p>
                                </a>
                            @endif
                            <a href="{{ route('purchase-requests.index') }}" class="p-4 border border-yellow-200 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-bold text-yellow-900">Lihat Pengajuan</p>
                                        <p class="text-xs text-yellow-700">{{ $stats['pending_requests'] }} pending</p>
                                    </div>
                                </div>
                            </a>
                            @if(auth()->user()->isAdmin())
                                <div class="p-4 border border-green-200 bg-green-50 rounded-lg">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-bold text-green-900">Approve Admin</p>
                                            <p class="text-xs text-green-700">Review & setujui</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="flex items-center justify-center my-4">
                <div class="border-l-2 border-dashed border-gray-300 h-8"></div>
            </div>

            <!-- Step 3: Pembelian -->
            <div class="mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold">3</div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-bold text-gray-900 mb-2">🛒 Proses Pembelian</h3>
                        <p class="text-sm text-gray-600 mb-3">Admin melakukan pembelian ke supplier setelah pengajuan disetujui</p>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Buat Purchase Order
                            </a>
                        @else
                            <p class="text-sm text-gray-500 italic">* Hanya Admin yang dapat melakukan pembelian</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="flex items-center justify-center my-4">
                <div class="border-l-2 border-dashed border-gray-300 h-8"></div>
            </div>

            <!-- Step 4: Penerimaan Barang -->
            <div class="mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center font-bold">4</div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-bold text-gray-900 mb-2">📦 Penerimaan Barang</h3>
                        <p class="text-sm text-gray-600 mb-3">Bagian Teknik menerima barang dan melakukan pengecekan</p>
                        <div class="grid grid-cols-2 gap-3">
                            @if(auth()->user()->isTeknik() || (auth()->user()->isSuperadmin()))
                                <a href="{{ route('goods-receipts.create') }}" class="p-4 border-2 border-green-300 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                        <x-badge variant="success">Action</x-badge>
                                    </div>
                                    <p class="text-sm font-bold text-green-900">Terima Barang</p>
                                    <p class="text-xs text-green-700">Cek & input penerimaan</p>
                                </a>
                            @endif
                            <div class="p-4 border border-orange-200 bg-orange-50 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-bold text-orange-900">Koreksi Barang</p>
                                        <p class="text-xs text-orange-700">Jika qty tidak sesuai</p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 mt-2">Sistem otomatis catat selisih dan update stok sesuai qty diterima</p>
                            </div>
                        </div>
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded text-xs">
                            <p class="font-semibold text-blue-900">💡 Info: Sistem FIFO Batch Tracking</p>
                            <p class="text-blue-700">Setiap penerimaan otomatis membuat batch baru dengan harga pembelian. Material keluar menggunakan metode FIFO (First In First Out)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="flex items-center justify-center my-4">
                <div class="border-l-2 border-dashed border-gray-300 h-8"></div>
            </div>

            <!-- Step 5: Kegiatan & Material Usage -->
            <div class="mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold">5</div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-bold text-gray-900 mb-2">🏗️ Kegiatan & Penggunaan Material</h3>
                        <p class="text-sm text-gray-600 mb-3">Buat kegiatan, assign tukang, dan catat penggunaan material</p>
                        <div class="grid grid-cols-3 gap-3">
                            <a href="{{ route('activities.create') }}" class="p-4 border-2 border-indigo-300 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                                <div class="flex items-center justify-between mb-2">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <x-badge variant="info">Step 1</x-badge>
                                </div>
                                <p class="text-sm font-bold text-indigo-900">Buat Kegiatan</p>
                                <p class="text-xs text-indigo-700">Tambah kegiatan baru</p>
                            </a>
                            <a href="{{ route('activities.index') }}" class="p-4 border border-indigo-200 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-bold text-indigo-900">Assign Tukang</p>
                                        <p class="text-xs text-indigo-700">{{ $stats['active_activities'] }} kegiatan</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('material-usages.index') }}" class="p-4 border border-indigo-200 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-bold text-indigo-900">Catat Material</p>
                                        <p class="text-xs text-indigo-700">Material digunakan</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="flex items-center justify-center my-4">
                <div class="border-l-2 border-dashed border-gray-300 h-8"></div>
            </div>

            <!-- Step 6: Penggajian -->
            <div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-pink-600 text-white rounded-full flex items-center justify-center font-bold">6</div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-bold text-gray-900 mb-2">💰 Penggajian Tukang</h3>
                        <p class="text-sm text-gray-600 mb-3">Catat kehadiran dan ajukan penggajian tukang</p>
                        <div class="grid grid-cols-2 gap-3">
                            @if(auth()->user()->isTeknik() || (auth()->user()->isSuperadmin()))
                                <a href="{{ route('payroll-requests.create') }}" class="p-4 border-2 border-pink-300 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <x-badge variant="warning">Action</x-badge>
                                    </div>
                                    <p class="text-sm font-bold text-pink-900">Ajukan Penggajian</p>
                                    <p class="text-xs text-pink-700">Buat pengajuan gaji</p>
                                </a>
                            @endif
                            <a href="{{ route('payroll-requests.index') }}" class="p-4 border border-pink-200 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-pink-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-bold text-pink-900">Lihat Pengajuan</p>
                                        <p class="text-xs text-pink-700">Status penggajian</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-card>
</div>
@endsection