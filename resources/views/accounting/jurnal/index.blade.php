{{-- resources/views/accounting/jurnal/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Jurnal Umum')
@section('breadcrumb', 'Accounting / Jurnal')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Jurnal Umum</h2>
        <x-button variant="primary" href="{{ route('accounting.jurnal.create') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Jurnal Baru
        </x-button>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter --}}
    <x-card>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="search" placeholder="Cari nomor bukti..." 
                value="{{ request('search') }}"
                class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            
            <input type="date" name="dari" placeholder="Dari Tanggal" 
                value="{{ request('dari') }}"
                class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            
            <input type="date" name="sampai" placeholder="Sampai Tanggal" 
                value="{{ request('sampai') }}"
                class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

            <select name="status" class="px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                <option value="void" {{ request('status') == 'void' ? 'selected' : '' }}>Void</option>
            </select>

            <div class="flex gap-2">
                <x-button type="submit" variant="secondary" class="flex-1">Filter</x-button>
                <x-button href="{{ route('accounting.jurnal.index') }}" variant="secondary">Reset</x-button>
            </div>
        </form>
    </x-card>

    {{-- Jurnal Table --}}
    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Nomor Bukti</th>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">Jenis</th>
                        <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                        <th class="px-3 py-2 text-right font-semibold">Total</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                        <th class="px-3 py-2 text-left font-semibold">Dibuat Oleh</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($jurnal as $j)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">
                                <a href="{{ route('accounting.jurnal.show', $j) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $j->nomor_bukti }}
                                </a>
                            </td>
                            <td class="px-3 py-2">{{ \Carbon\Carbon::parse($j->tanggal)->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">
                                <x-badge :variant="match($j->jenis_jurnal) {
                                    'umum' => 'info',
                                    'penyesuaian' => 'warning',
                                    'penutup' => 'danger',
                                    'pembalik' => 'secondary',
                                    default => 'secondary'
                                }">
                                    {{ ucfirst($j->jenis_jurnal) }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-gray-600">
                                {{ Str::limit($j->keterangan, 40) ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-right font-semibold">
                                Rp {{ number_format($j->items->sum('debet'), 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                <x-badge :variant="match($j->status) {
                                    'draft' => 'secondary',
                                    'posted' => 'success',
                                    'void' => 'danger',
                                    default => 'secondary'
                                }">
                                    {{ ucfirst($j->status) }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-gray-600">
                                {{ $j->creator->full_name ?? '-' }}
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('accounting.jurnal.show', $j) }}" 
                                       class="text-blue-600 hover:text-blue-800" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    @if($j->status === 'draft')
                                        <a href="{{ route('accounting.jurnal.edit', $j) }}" 
                                           class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('accounting.jurnal.destroy', $j) }}" method="POST" 
                                              onsubmit="return confirm('Yakin ingin menghapus jurnal ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('accounting.jurnal.print', $j) }}" 
                                       target="_blank" class="text-gray-600 hover:text-gray-800" title="Print">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
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
                                    <p class="text-sm">Tidak ada data jurnal</p>
                                    <a href="{{ route('accounting.jurnal.create') }}" class="mt-2 text-blue-600 hover:text-blue-800 text-xs">
                                        + Buat Jurnal Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jurnal->hasPages())
            <div class="mt-4">
                {{ $jurnal->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection