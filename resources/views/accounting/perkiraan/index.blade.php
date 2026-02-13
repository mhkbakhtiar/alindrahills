{{-- resources/views/accounting/perkiraan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Chart of Accounts')
@section('breadcrumb', 'Accounting / Perkiraan')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Chart of Accounts (Perkiraan)</h2>
        <div class="flex gap-2">
            <x-button variant="secondary" href="{{ route('accounting.perkiraan.export') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </x-button>
            <x-button variant="primary" href="{{ route('accounting.perkiraan.create') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Perkiraan
            </x-button>
        </div>
    </div>

    {{-- Filter --}}
    <x-card>
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" placeholder="Cari kode atau nama..." 
                value="{{ request('search') }}"
                class="flex-1 px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            
            <select name="jenis" class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Jenis</option>
                <option value="Aset" {{ request('jenis') == 'Aset' ? 'selected' : '' }}>Aset</option>
                <option value="Kewajiban" {{ request('jenis') == 'Kewajiban' ? 'selected' : '' }}>Kewajiban</option>
                <option value="Modal" {{ request('jenis') == 'Modal' ? 'selected' : '' }}>Modal</option>
                <option value="Pendapatan" {{ request('jenis') == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                <option value="Biaya" {{ request('jenis') == 'Biaya' ? 'selected' : '' }}>Biaya</option>
            </select>

            <x-button type="submit" variant="secondary">Filter</x-button>
            <x-button href="{{ route('accounting.perkiraan.index') }}" variant="secondary">Reset</x-button>
        </form>
    </x-card>

    {{-- Quick Links to Reports --}}
    <div class="grid grid-cols-4 gap-3">
        <a href="{{ route('accounting.reports.trial-balance') }}" class="bg-white border rounded-lg p-4 hover:bg-gray-50 transition">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-gray-600">Neraca Saldo</div>
                    <div class="text-sm font-semibold">Trial Balance</div>
                </div>
            </div>
        </a>

        <a href="{{ route('accounting.reports.balance-sheet') }}" class="bg-white border rounded-lg p-4 hover:bg-gray-50 transition">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-gray-600">Neraca</div>
                    <div class="text-sm font-semibold">Balance Sheet</div>
                </div>
            </div>
        </a>

        <a href="{{ route('accounting.reports.income-statement') }}" class="bg-white border rounded-lg p-4 hover:bg-gray-50 transition">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-gray-600">Laba Rugi</div>
                    <div class="text-sm font-semibold">Income Statement</div>
                </div>
            </div>
        </a>

        <form action="{{ route('accounting.perkiraan.recalculate') }}" method="POST" class="bg-white border rounded-lg p-4 hover:bg-gray-50 transition cursor-pointer" onclick="if(confirm('Recalculate saldo semua perkiraan?')) this.submit();">
            @csrf
            <div class="flex items-center gap-3">
                <div class="bg-yellow-100 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-gray-600">Recalculate</div>
                    <div class="text-sm font-semibold">Update Saldo</div>
                </div>
            </div>
        </form>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Perkiraan</th>
                        <th class="px-3 py-2 text-left font-semibold">Jenis</th>
                        <th class="px-3 py-2 text-left font-semibold">Departemen</th>
                        <th class="px-3 py-2 text-right font-semibold">Saldo Debet</th>
                        <th class="px-3 py-2 text-right font-semibold">Saldo Kredit</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($perkiraan as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $p->kode_perkiraan }}</td>
                            <td class="px-3 py-2">
                                @if($p->is_header)
                                    <strong>{{ $p->nama_perkiraan }}</strong>
                                @else
                                    {{ $p->nama_perkiraan }}
                                @endif
                                @if($p->is_cash_bank)
                                    <span class="ml-1 text-xs text-blue-600">💰</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <x-badge :variant="match($p->jenis_akun) {
                                    'Aset' => 'success',
                                    'Kewajiban' => 'danger',
                                    'Modal' => 'info',
                                    'Pendapatan' => 'success',
                                    'Biaya' => 'warning',
                                    default => 'secondary'
                                }">
                                    {{ $p->jenis_akun }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-gray-600">{{ $p->departemen ?? '-' }}</td>
                            <td class="px-3 py-2 text-right">
                                @if($p->saldo_debet > 0)
                                    <span class="text-green-600 font-semibold">Rp {{ number_format($p->saldo_debet, 0) }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                @if($p->saldo_kredit > 0)
                                    <span class="text-red-600 font-semibold">Rp {{ number_format($p->saldo_kredit, 0) }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <x-badge :variant="$p->is_active ? 'success' : 'secondary'">
                                    {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('accounting.perkiraan.show', $p) }}" class="text-blue-600 hover:text-blue-800" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('accounting.perkiraan.edit', $p) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-sm">Tidak ada data perkiraan</p>
                                    <a href="{{ route('accounting.perkiraan.create') }}" class="mt-2 text-blue-600 hover:text-blue-800 text-xs">
                                        + Tambah Perkiraan Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($perkiraan->hasPages())
            <div class="mt-4">
                {{ $perkiraan->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection