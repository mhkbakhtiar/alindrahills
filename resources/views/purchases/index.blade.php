{{-- resources/views/purchases/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Purchase Orders')
@section('breadcrumb', 'Material / Purchase Orders')

@section('content')
<div class="space-y-4">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Purchase Orders</h2>
            <p class="text-xs text-gray-500 mt-0.5">Daftar semua pembelian material</p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperadmin())
            <a href="{{ route('purchases.create') }}"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Purchase Order
            </a>
        @endif
    </div>

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

    {{-- ── Summary Cards ────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Total PO</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $purchases->total() }}</p>
        </div>
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Cash / Lunas</p>
            <p class="text-xl font-bold text-green-600 mt-1">
                {{ $purchases->getCollection()->where('payment_status', 'lunas')->count() }}
            </p>
        </div>
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Tempo / Belum Bayar</p>
            <p class="text-xl font-bold text-orange-500 mt-1">
                {{ $purchases->getCollection()->where('payment_status', 'belum_bayar')->count() }}
            </p>
        </div>
        <div class="bg-white border rounded-lg p-3">
            <p class="text-xs text-gray-500">Jurnal Belum Posting</p>
            <p class="text-xl font-bold text-yellow-600 mt-1">
                {{ $purchases->getCollection()->filter(fn($p) => $p->jurnal && $p->jurnal->status === 'draft')->count() }}
            </p>
        </div>
    </div>

    {{-- ── Filter ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white border rounded-lg p-3">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="No. PO atau supplier..."
                    class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-52">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Metode Bayar</label>
                <select name="payment_type" class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="cash" {{ request('payment_type') === 'cash' ? 'selected' : '' }}>Cash / Tunai</option>
                    <option value="tempo" {{ request('payment_type') === 'tempo' ? 'selected' : '' }}>Tempo</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status Bayar</label>
                <select name="payment_status" class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="lunas" {{ request('payment_status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum_bayar" {{ request('payment_status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-3 py-2 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'payment_type', 'payment_status']))
                    <a href="{{ route('purchases.index') }}"
                        class="px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Table ────────────────────────────────────────────────────────────── --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left font-semibold text-gray-700">No. PO</th>
                        <th class="px-3 py-3 text-left font-semibold text-gray-700">Tanggal</th>
                        <th class="px-3 py-3 text-left font-semibold text-gray-700">Supplier</th>
                        <th class="px-3 py-3 text-right font-semibold text-gray-700">Total (Rp)</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-700">Metode Bayar</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-700">Jatuh Tempo</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-700">Status Bayar</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-700">Jurnal</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-700">Barang</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($purchases as $purchase)
                        @php
                            $isOverdue = $purchase->payment_type === 'tempo'
                                && $purchase->payment_status === 'belum_bayar'
                                && $purchase->tempo_date
                                && $purchase->tempo_date->isPast();
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $isOverdue ? 'bg-red-50' : '' }}">
                            <td class="px-3 py-3">
                                <a href="{{ route('purchases.show', $purchase) }}"
                                    class="font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $purchase->purchase_number }}
                                </a>
                            </td>
                            <td class="px-3 py-3 text-gray-600">
                                {{ $purchase->purchase_date->format('d M Y') }}
                            </td>
                            <td class="px-3 py-3">
                                <p class="font-medium text-gray-800">{{ $purchase->supplier_name }}</p>
                                @if($purchase->supplier_contact)
                                    <p class="text-gray-400 text-xs">{{ $purchase->supplier_contact }}</p>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-right font-semibold text-gray-800">
                                {{ number_format($purchase->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if($purchase->payment_type === 'cash')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        💵 Cash
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        📅 Tempo
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if($purchase->tempo_date)
                                    <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                        {{ $purchase->tempo_date->format('d M Y') }}
                                        @if($isOverdue)
                                            <span class="block text-red-500 text-xs">⚠ Jatuh Tempo</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if($purchase->payment_status === 'lunas')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✓ Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $isOverdue ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $isOverdue ? '⚠ Overdue' : 'Belum Bayar' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if($purchase->jurnal)
                                    @if($purchase->jurnal->status === 'posted')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Posted
                                        </span>
                                    @elseif($purchase->jurnal->status === 'draft')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Draft
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Void
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if($purchase->goodsReceipt)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        ✓ Diterima
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                <a href="{{ route('purchases.show', $purchase) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-3 py-12 text-center text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm">Belum ada Purchase Order</p>
                                @if(auth()->user()->isAdmin() || auth()->user()->isSuperadmin())
                                    <a href="{{ route('purchases.create') }}" class="text-blue-600 hover:underline text-xs mt-1 inline-block">
                                        Buat Purchase Order pertama →
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="px-4 py-3 border-t bg-gray-50">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

</div>
@endsection