{{-- resources/views/accounting/tahun-anggaran/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Tahun Anggaran')
@section('breadcrumb', 'Accounting / Tahun Anggaran')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Tahun Anggaran</h2>
        <x-button variant="primary" href="{{ route('accounting.tahun-anggaran.create') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Tahun Anggaran
        </x-button>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tahun</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Periode Awal</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Periode Akhir</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Keterangan</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($tahunAnggaran as $ta)
                        <tr class="hover:bg-gray-50 {{ $ta->status === 'aktif' ? 'bg-green-50' : '' }}">
                            <td class="px-4 py-3 font-bold text-gray-900">{{ $ta->tahun }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $ta->periode_awal->format('d F Y') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $ta->periode_akhir->format('d F Y') }}</td>
                            <td class="px-4 py-3">
                                <x-badge :variant="$ta->status === 'aktif' ? 'success' : ($ta->status === 'tutup_buku' ? 'danger' : 'secondary')">
                                    {{ $ta->status === 'aktif' ? 'Aktif' : ($ta->status === 'tutup_buku' ? 'Tutup Buku' : ucfirst($ta->status)) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $ta->keterangan ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- Detail --}}
                                    <a href="{{ route('accounting.tahun-anggaran.show', $ta) }}"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    {{-- Edit --}}
                                    @if($ta->status !== 'tutup_buku')
                                        <a href="{{ route('accounting.tahun-anggaran.edit', $ta) }}"
                                            class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- Aktivasi --}}
                                    @if($ta->status !== 'aktif' && $ta->status !== 'tutup_buku')
                                        <form action="{{ route('accounting.tahun-anggaran.activate', $ta->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin mengaktifkan tahun anggaran {{ $ta->tahun }}? Tahun anggaran lain akan dinonaktifkan.')" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg" title="Aktifkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Tutup Buku --}}
                                    @if($ta->status === 'aktif')
                                        <form action="{{ route('accounting.tahun-anggaran.close', $ta->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menutup buku tahun anggaran {{ $ta->tahun }}? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-orange-600 hover:bg-orange-50 rounded-lg" title="Tutup Buku">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Hapus --}}
                                    @if($ta->status !== 'tutup_buku')
                                        <form action="{{ route('accounting.tahun-anggaran.destroy', $ta) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus tahun anggaran {{ $ta->tahun }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Belum ada tahun anggaran. 
                                <a href="{{ route('accounting.tahun-anggaran.create') }}" class="text-blue-600 hover:underline">Tambah sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tahunAnggaran->hasPages())
            <div class="mt-4">
                {{ $tahunAnggaran->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection