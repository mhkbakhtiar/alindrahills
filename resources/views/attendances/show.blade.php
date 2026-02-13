@extends('layouts.app')

@section('title', 'Detail Absensi')
@section('breadcrumb', 'Payroll / Absensi Tukang / Detail')

@section('content')
<div class="max-w-3xl mx-auto">
    <x-card title="Detail Absensi">
        <div class="grid grid-cols-2 gap-6">
            <!-- Worker Information -->
            <div class="col-span-2 border-b pb-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Tukang</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Kode Tukang</p>
                        <p class="text-sm font-medium text-gray-900">{{ $attendance->worker->worker_code }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Nama Lengkap</p>
                        <p class="text-sm font-medium text-gray-900">{{ $attendance->worker->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jenis Tukang</p>
                        <p class="text-sm font-medium text-gray-900">{{ $attendance->worker->worker_type }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Upah Harian</p>
                        <p class="text-sm font-medium text-gray-900">Rp {{ number_format($attendance->worker->daily_rate, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Attendance Details -->
            <div class="col-span-2">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Detail Absensi</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Tanggal</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $attendance->attendance_date->format('d F Y') }}
                            <span class="text-gray-500">({{ $attendance->attendance_date->format('l') }})</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <p class="text-sm">
                            <x-badge :variant="$attendance->status_badge">
                                {{ ucfirst($attendance->status) }}
                            </x-badge>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Check In</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Check Out</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jam Kerja</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $attendance->hours_worked > 0 ? number_format($attendance->hours_worked, 1) . ' jam' : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Upah Hari Ini</p>
                        <p class="text-sm font-medium text-green-600">
                            @if($attendance->status == 'hadir' && $attendance->hours_worked > 0)
                                Rp {{ number_format($attendance->worker->daily_rate, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($attendance->notes)
            <div class="col-span-2">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Catatan</h3>
                <div class="bg-gray-50 rounded p-3">
                    <p class="text-sm text-gray-700">{{ $attendance->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Timestamps -->
            <div class="col-span-2 border-t pt-4">
                <div class="grid grid-cols-2 gap-4 text-xs text-gray-500">
                    <div>
                        <p>Dibuat: {{ $attendance->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p>Diupdate: {{ $attendance->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between pt-4 border-t mt-6">
            <x-button type="button" variant="secondary" href="{{ route('attendances.index') }}">
                Kembali
            </x-button>
            <div class="flex gap-2">
                <x-button variant="primary" href="{{ route('attendances.edit', $attendance) }}">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </x-button>
                <form method="POST" action="{{ route('attendances.destroy', $attendance) }}" 
                      onsubmit="return confirm('Yakin ingin menghapus data absensi ini?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </x-button>
                </form>
            </div>
        </div>
    </x-card>
</div>
@endsection