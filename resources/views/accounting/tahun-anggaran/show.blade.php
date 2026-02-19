{{-- resources/views/accounting/tahun-anggaran/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Tahun Anggaran')
@section('breadcrumb', 'Accounting / Tahun Anggaran / Detail')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Detail Tahun Anggaran {{ $tahunAnggaran->tahun }}</h2>
        <div class="flex gap-2">
            <x-button variant="secondary" href="{{ route('accounting.tahun-anggaran.index') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </x-button>

            @if($tahunAnggaran->status !== 'tutup_buku')
                <x-button variant="warning" href="{{ route('accounting.tahun-anggaran.edit', $tahunAnggaran) }}">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </x-button>
            @endif

            @if($tahunAnggaran->status === 'aktif')
                <form action="{{ route('accounting.tahun-anggaran.close', $tahunAnggaran->id) }}" method="POST"
                    onsubmit="return confirm('Yakin ingin menutup buku tahun anggaran {{ $tahunAnggaran->tahun }}? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                    @csrf
                    <x-button type="submit" variant="danger">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Tutup Buku
                    </x-button>
                </form>
            @endif

            @if($tahunAnggaran->status !== 'aktif' && $tahunAnggaran->status !== 'tutup_buku')
                <form action="{{ route('accounting.tahun-anggaran.activate', $tahunAnggaran->id) }}" method="POST"
                    onsubmit="return confirm('Yakin ingin mengaktifkan tahun anggaran {{ $tahunAnggaran->tahun }}?')" class="inline">
                    @csrf
                    <x-button type="submit" variant="success">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Aktifkan
                    </x-button>
                </form>
            @endif
        </div>
    </div>

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

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Total Jurnal</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tahunAnggaran->jurnal->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">transaksi tercatat</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Jurnal Posted</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $tahunAnggaran->jurnal->where('status', 'posted')->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">sudah diposting</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-xs text-gray-500">Jurnal Draft</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $tahunAnggaran->jurnal->where('status', 'draft')->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">belum diposting</p>
        </div>
    </div>

    {{-- Detail Info --}}
    <x-card>
        <h3 class="text-sm font-semibold mb-4 border-b pb-2">Informasi Tahun Anggaran</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <label class="text-xs text-gray-600">Tahun</label>
                <p class="font-bold text-xl text-gray-900">{{ $tahunAnggaran->tahun }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Periode Awal</label>
                <p class="font-semibold">{{ $tahunAnggaran->periode_awal->format('d F Y') }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Periode Akhir</label>
                <p class="font-semibold">{{ $tahunAnggaran->periode_akhir->format('d F Y') }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Status</label>
                <p class="mt-1">
                    <x-badge :variant="$tahunAnggaran->status === 'aktif' ? 'success' : ($tahunAnggaran->status === 'tutup_buku' ? 'danger' : 'secondary')">
                        {{ $tahunAnggaran->status === 'aktif' ? 'Aktif' : ($tahunAnggaran->status === 'tutup_buku' ? 'Tutup Buku' : ucfirst($tahunAnggaran->status)) }}
                    </x-badge>
                </p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Durasi</label>
                <p class="font-semibold">{{ $tahunAnggaran->periode_awal->diffInDays($tahunAnggaran->periode_akhir) + 1 }} hari</p>
            </div>
            <div>
                <label class="text-xs text-gray-600">Dibuat Pada</label>
                <p class="font-semibold">{{ $tahunAnggaran->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @if($tahunAnggaran->keterangan)
                <div class="md:col-span-3">
                    <label class="text-xs text-gray-600">Keterangan</label>
                    <p class="font-semibold">{{ $tahunAnggaran->keterangan }}</p>
                </div>
            @endif
        </div>
    </x-card>

    {{-- Jurnal List --}}
    <x-card>
        <div class="flex items-center justify-between mb-4 border-b pb-2">
            <h3 class="text-sm font-semibold">Daftar Jurnal</h3>
            <a href="{{ route('accounting.jurnal.index') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No. Bukti</th>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">Jenis</th>
                        <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                        <th class="px-3 py-2 text-left font-semibold">Status</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($tahunAnggaran->jurnal->take(20) as $jurnal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium text-blue-600">
                                <a href="{{ route('accounting.jurnal.show', $jurnal) }}" class="hover:underline">
                                    {{ $jurnal->nomor_bukti }}
                                </a>
                            </td>
                            <td class="px-3 py-2 text-gray-600">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">
                                <x-badge :variant="match($jurnal->jenis_jurnal) {
                                    'umum' => 'info',
                                    'penyesuaian' => 'warning',
                                    'penutup' => 'danger',
                                    'pembalik' => 'secondary',
                                    default => 'secondary'
                                }">
                                    {{ ucfirst($jurnal->jenis_jurnal) }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-gray-600">{{ Str::limit($jurnal->keterangan, 40) ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <x-badge :variant="match($jurnal->status) {
                                    'draft' => 'secondary',
                                    'posted' => 'success',
                                    'void' => 'danger',
                                    default => 'secondary'
                                }">
                                    {{ ucfirst($jurnal->status) }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('accounting.jurnal.show', $jurnal) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-gray-500">
                                Belum ada jurnal pada tahun anggaran ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tahunAnggaran->jurnal->count() > 20)
            <p class="text-xs text-gray-400 mt-3 text-center">Menampilkan 20 dari {{ $tahunAnggaran->jurnal->count() }} jurnal</p>
        @endif
    </x-card>

    {{-- Warning for tutup buku --}}
    @if($tahunAnggaran->status === 'tutup_buku')
        <x-card>
            <div class="flex items-start gap-3 text-sm">
                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <div>
                    <p class="font-semibold text-red-700">Tahun Anggaran Sudah Ditutup</p>
                    <p class="text-xs text-red-600 mt-1">Tahun anggaran ini sudah tutup buku. Tidak dapat menambah, mengedit, atau menghapus jurnal pada periode ini.</p>
                </div>
            </div>
        </x-card>
    @endif
</div>
@endsection