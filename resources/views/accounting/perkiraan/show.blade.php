{{-- resources/views/accounting/perkiraan/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Perkiraan')
@section('breadcrumb', 'Accounting / Perkiraan / Detail')

@section('content')
<div class="max-w-5xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Detail Perkiraan</h2>
            <p class="text-xs text-gray-600 mt-1">{{ $perkiraan->kode_perkiraan }} - {{ $perkiraan->nama_perkiraan }}</p>
        </div>
        <div class="flex gap-2">
            <x-button variant="secondary" href="{{ route('accounting.perkiraan.index') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </x-button>
            <x-button variant="info" href="{{ route('accounting.perkiraan.ledger', $perkiraan) }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Buku Besar
            </x-button>
            <x-button variant="warning" href="{{ route('accounting.perkiraan.edit', $perkiraan) }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </x-button>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-4 gap-3">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg p-4">
            <div class="text-xs opacity-90 mb-1">Saldo Akhir</div>
            <div class="text-lg font-bold">Rp {{ number_format(abs($perkiraan->saldo), 0) }}</div>
            <div class="text-xs mt-1 opacity-75">
                {{ $perkiraan->saldo >= 0 ? 'Normal Balance' : 'Abnormal' }}
            </div>
        </div>

        <div class="bg-white border rounded-lg p-4">
            <div class="text-xs text-gray-600 mb-1">Saldo Debet</div>
            <div class="text-lg font-bold text-green-600">Rp {{ number_format($perkiraan->saldo_debet, 0) }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Debet</div>
        </div>

        <div class="bg-white border rounded-lg p-4">
            <div class="text-xs text-gray-600 mb-1">Saldo Kredit</div>
            <div class="text-lg font-bold text-red-600">Rp {{ number_format($perkiraan->saldo_kredit, 0) }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Kredit</div>
        </div>

        @if($perkiraan->anggaran)
        <div class="bg-white border rounded-lg p-4">
            <div class="text-xs text-gray-600 mb-1">Budget Utilization</div>
            <div class="text-lg font-bold {{ $perkiraan->budget_status == 'over-budget' ? 'text-red-600' : 'text-blue-600' }}">
                {{ number_format($perkiraan->budget_utilization, 1) }}%
            </div>
            <div class="text-xs mt-1">
                <x-badge :variant="match($perkiraan->budget_status) {
                    'safe' => 'success',
                    'warning' => 'warning',
                    'near-limit' => 'warning',
                    'over-budget' => 'danger',
                    default => 'secondary'
                }">
                    {{ ucfirst(str_replace('-', ' ', $perkiraan->budget_status)) }}
                </x-badge>
            </div>
        </div>
        @else
        <div class="bg-white border rounded-lg p-4">
            <div class="text-xs text-gray-600 mb-1">Transaksi</div>
            <div class="text-lg font-bold text-gray-900">{{ $perkiraan->itemJurnal->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Items</div>
        </div>
        @endif
    </div>

    {{-- Info Perkiraan --}}
    <x-card>
        <h3 class="text-sm font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Informasi Perkiraan
        </h3>
        
        <div class="grid grid-cols-3 gap-6 text-xs">
            <div>
                <label class="text-gray-600 block mb-1">Kode Perkiraan</label>
                <p class="font-medium text-lg text-blue-600">{{ $perkiraan->kode_perkiraan }}</p>
            </div>
            <div class="col-span-2">
                <label class="text-gray-600 block mb-1">Nama Perkiraan</label>
                <p class="font-medium text-base">{{ $perkiraan->nama_perkiraan }}</p>
            </div>

            <div>
                <label class="text-gray-600 block mb-1">Jenis Akun</label>
                <p>
                    <x-badge :variant="match($perkiraan->jenis_akun) {
                        'Aset' => 'success',
                        'Kewajiban' => 'danger',
                        'Modal' => 'info',
                        'Pendapatan' => 'success',
                        'Biaya' => 'warning',
                        default => 'secondary'
                    }">
                        {{ $perkiraan->jenis_akun }}
                    </x-badge>
                </p>
            </div>
            <div>
                <label class="text-gray-600 block mb-1">Kategori</label>
                <p class="font-medium">{{ $perkiraan->kategori ?? '-' }}</p>
            </div>
            <div>
                <label class="text-gray-600 block mb-1">Departemen</label>
                <p class="font-medium">{{ $perkiraan->departemen ?? '-' }}</p>
            </div>

            @if($perkiraan->parent)
            <div class="col-span-2">
                <label class="text-gray-600 block mb-1">Parent Account</label>
                <p class="font-medium">
                    <a href="{{ route('accounting.perkiraan.show', $perkiraan->parent) }}" class="text-blue-600 hover:underline">
                        {{ $perkiraan->parent->kode_perkiraan }} - {{ $perkiraan->parent->nama_perkiraan }}
                    </a>
                </p>
            </div>
            @endif

            <div>
                <label class="text-gray-600 block mb-1">Status</label>
                <p>
                    <x-badge :variant="$perkiraan->is_active ? 'success' : 'danger'">
                        {{ $perkiraan->is_active ? 'Aktif' : 'Nonaktif' }}
                    </x-badge>
                </p>
            </div>

            @if($perkiraan->anggaran)
            <div>
                <label class="text-gray-600 block mb-1">Anggaran</label>
                <p class="font-semibold text-purple-600">Rp {{ number_format($perkiraan->anggaran, 0) }}</p>
            </div>
            @endif

            <div>
                <label class="text-gray-600 block mb-1">Flags</label>
                <div class="flex gap-2">
                    @if($perkiraan->is_header)
                        <x-badge variant="info">Header</x-badge>
                    @endif
                    @if($perkiraan->is_cash_bank)
                        <x-badge variant="success">Kas/Bank</x-badge>
                    @endif
                    @if(!$perkiraan->is_header && !$perkiraan->is_cash_bank)
                        <span class="text-gray-400">-</span>
                    @endif
                </div>
            </div>

            @if($perkiraan->keterangan)
            <div class="col-span-3">
                <label class="text-gray-600 block mb-1">Keterangan</label>
                <p class="text-gray-700">{{ $perkiraan->keterangan }}</p>
            </div>
            @endif
        </div>
    </x-card>

    {{-- Child Accounts --}}
    @if($perkiraan->children->count() > 0)
    <x-card>
        <h3 class="text-sm font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            Sub-Perkiraan ({{ $perkiraan->children->count() }} akun)
        </h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama</th>
                        <th class="px-3 py-2 text-right font-semibold">Saldo</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($perkiraan->children as $child)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium">{{ $child->kode_perkiraan }}</td>
                        <td class="px-3 py-2">{{ $child->nama_perkiraan }}</td>
                        <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format(abs($child->saldo), 0) }}</td>
                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('accounting.perkiraan.show', $child) }}" class="text-blue-600 hover:underline">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
    @endif

    {{-- Transaksi Terakhir --}}
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                20 Transaksi Terakhir
            </h3>
            <a href="{{ route('accounting.perkiraan.ledger', $perkiraan) }}" class="text-xs text-blue-600 hover:underline">
                Lihat Semua di Buku Besar →
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">No. Bukti</th>
                        <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                        <th class="px-3 py-2 text-right font-semibold">Debet</th>
                        <th class="px-3 py-2 text-right font-semibold">Kredit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($perkiraan->itemJurnal as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap">
                                {{ $item->jurnal->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-2 font-medium text-blue-600">
                                <a href="#" class="hover:underline">{{ $item->jurnal->nomor_bukti }}</a>
                            </td>
                            <td class="px-3 py-2">{{ $item->keterangan }}</td>
                            <td class="px-3 py-2 text-right {{ $item->debet > 0 ? 'text-green-600 font-semibold' : '' }}">
                                {{ $item->debet > 0 ? 'Rp ' . number_format($item->debet, 0) : '-' }}
                            </td>
                            <td class="px-3 py-2 text-right {{ $item->kredit > 0 ? 'text-red-600 font-semibold' : '' }}">
                                {{ $item->kredit > 0 ? 'Rp ' . number_format($item->kredit, 0) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-sm">Belum ada transaksi</p>
                                    <p class="text-xs text-gray-400 mt-1">Transaksi akan muncul di sini setelah jurnal diposting</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Metadata --}}
    <div class="grid grid-cols-2 gap-4">
        <x-card>
            <h3 class="text-xs font-semibold text-gray-600 mb-2">Informasi Sistem</h3>
            <div class="space-y-1 text-xs text-gray-600">
                <div class="flex justify-between">
                    <span>Dibuat:</span>
                    <span class="font-medium">{{ $perkiraan->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Diupdate:</span>
                    <span class="font-medium">{{ $perkiraan->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>ID:</span>
                    <span class="font-medium">#{{ $perkiraan->id }}</span>
                </div>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-xs font-semibold text-gray-600 mb-2">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('accounting.perkiraan.ledger', $perkiraan) }}" class="block text-xs text-blue-600 hover:underline">
                    📊 Lihat Buku Besar
                </a>
                <a href="{{ route('accounting.perkiraan.edit', $perkiraan) }}" class="block text-xs text-yellow-600 hover:underline">
                    ✏️ Edit Perkiraan
                </a>
                @if(!$perkiraan->hasTransactions() && $perkiraan->children->count() == 0)
                <form action="{{ route('accounting.perkiraan.destroy', $perkiraan) }}" method="POST" class="inline" 
                    onsubmit="return confirm('Yakin ingin menghapus perkiraan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:underline">
                        🗑️ Hapus Perkiraan
                    </button>
                </form>
                @endif
            </div>
        </x-card>
    </div>
</div>
@endsection