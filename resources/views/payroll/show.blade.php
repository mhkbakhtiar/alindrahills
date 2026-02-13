@extends('layouts.app')

@section('title', 'Detail Pengajuan Penggajian')
@section('breadcrumb', 'Payroll / Pengajuan Penggajian / Detail')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Detail Pengajuan Penggajian</h2>
            <p class="text-sm text-gray-600">{{ $payrollRequest->request_number }}</p>
        </div>
        <div class="flex gap-2">
            @if($payrollRequest->status === 'pending' && auth()->user()->isAdmin())
                <form action="{{ route('payroll-requests.approve', $payrollRequest) }}" method="POST" 
                    onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')">
                    @csrf
                    @method('PATCH')
                    <x-button variant="success" type="submit">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approve
                    </x-button>
                </form>
            @endif
            <a href="{{ route('payroll-requests.print-invoice', $payrollRequest) }}" 
               target="_blank"
               class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak PDF
            </a>
            <x-button variant="secondary" href="{{ route('payroll-requests.index') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </x-button>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-4">
        @php
            $statusVariants = [
                'pending' => 'warning', 
                'approved' => 'success', 
                'rejected' => 'danger', 
                'paid' => 'info'
            ];
            $statusLabels = [
                'pending' => 'Menunggu Persetujuan',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'paid' => 'Sudah Dibayar'
            ];
        @endphp
        <x-badge :variant="$statusVariants[$payrollRequest->status]" class="text-sm px-4 py-2">
            {{ $statusLabels[$payrollRequest->status] }}
        </x-badge>
    </div>

    <!-- Activity Information (if exists) -->
    @if($payrollRequest->activity)
        <x-card class="mb-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-semibold text-blue-900 mb-1">📋 Kegiatan Terkait</h3>
                    <p class="text-base font-bold text-blue-900">{{ $payrollRequest->activity->activity_code }}</p>
                    <p class="text-sm text-blue-800 mb-2">{{ $payrollRequest->activity->activity_name }}</p>
                    <div class="flex items-center text-xs text-blue-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ $payrollRequest->activity->location->location_name ?? 'Lokasi tidak tersedia' }}</span>
                    </div>
                    @if($payrollRequest->activity->start_date && $payrollRequest->activity->end_date)
                        <div class="flex items-center text-xs text-blue-700 mt-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $payrollRequest->activity->start_date->format('d M Y') }} - {{ $payrollRequest->activity->end_date->format('d M Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>
    @endif

    <!-- Request Information -->
    <x-card class="mb-4">
        <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Pengajuan</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Pengajuan</label>
                <p class="text-sm text-gray-900 font-medium">{{ $payrollRequest->request_number }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Pengajuan</label>
                <p class="text-sm text-gray-900">{{ $payrollRequest->request_date->format('d F Y') }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Periode Penggajian</label>
                <p class="text-sm text-gray-900">
                    {{ $payrollRequest->period_start->format('d F Y') }} - {{ $payrollRequest->period_end->format('d F Y') }}
                </p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Surat</label>
                <p class="text-sm text-gray-900">{{ $payrollRequest->letter_number }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Surat</label>
                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($payrollRequest->letter_date)->format('d F Y') }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Diajukan Oleh</label>
                <p class="text-sm text-gray-900">{{ $payrollRequest->requester->full_name }}</p>
            </div>

            @if($payrollRequest->status !== 'pending')
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">
                        @if($payrollRequest->status === 'approved')
                            Disetujui Oleh
                        @else
                            Diproses Oleh
                        @endif
                    </label>
                    <p class="text-sm text-gray-900">
                        {{ $payrollRequest->approver->full_name ?? '-' }}
                    </p>
                </div>

                @if($payrollRequest->approved_date)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Persetujuan</label>
                        <p class="text-sm text-gray-900">{{ $payrollRequest->approved_date->format('d F Y H:i') }}</p>
                    </div>
                @endif
            @endif

            @if($payrollRequest->notes)
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                    <p class="text-sm text-gray-900">{{ $payrollRequest->notes }}</p>
                </div>
            @endif
        </div>
    </x-card>

    <!-- Worker Details -->
    <x-card class="mb-4">
        <h3 class="text-md font-semibold text-gray-900 mb-4">Daftar Tukang</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Tukang</th>
                        <th class="px-3 py-2 text-center font-semibold">Hari Kerja</th>
                        <th class="px-3 py-2 text-right font-semibold">Upah/Hari</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Upah</th>
                        <th class="px-3 py-2 text-right font-semibold">Bonus</th>
                        <th class="px-3 py-2 text-right font-semibold">Potongan</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Bersih</th>
                        <th class="px-3 py-2 text-right font-semibold">SLIP GAJI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payrollRequest->details as $index => $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $index + 1 }}</td>
                            <td class="px-3 py-2">
                                <div class="font-medium text-gray-900">{{ $detail->worker->full_name }}</div>
                                @if($detail->notes)
                                    <div class="text-gray-500 text-xs mt-0.5">{{ $detail->notes }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">{{ number_format($detail->days_worked, 1) }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($detail->daily_rate, 0) }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($detail->total_wage, 0) }}</td>
                            <td class="px-3 py-2 text-right">
                                @if($detail->bonus > 0)
                                    <span class="text-green-600">+Rp {{ number_format($detail->bonus, 0) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                @if($detail->deduction > 0)
                                    <span class="text-red-600">-Rp {{ number_format($detail->deduction, 0) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($detail->net_payment, 0) }}</td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('payroll-requests.print-slip', [
                                    'payrollRequest' => $payrollRequest,
                                    'workerId' => $detail->worker->worker_id
                                ]) }}"
                                target="_blank"
                                class="text-blue-600 hover:text-blue-800 text-xs">
                                    Cetak Slip
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-gray-500">Tidak ada data tukang</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="7" class="px-3 py-3 text-right font-bold text-gray-900">Grand Total:</td>
                        <td class="px-3 py-3 text-right font-bold text-gray-900">
                            Rp {{ number_format($payrollRequest->total_amount, 0) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-card>

    <!-- Summary Card -->
    <x-card>
        <h3 class="text-md font-semibold text-gray-900 mb-4">Ringkasan</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-blue-50 rounded-lg">
                <p class="text-xs text-blue-600 font-medium mb-1">Total Tukang</p>
                <p class="text-2xl font-bold text-blue-900">{{ $payrollRequest->details->count() }}</p>
            </div>
            
            <div class="p-4 bg-green-50 rounded-lg">
                <p class="text-xs text-green-600 font-medium mb-1">Total Hari Kerja</p>
                <p class="text-2xl font-bold text-green-900">{{ number_format($payrollRequest->details->sum('days_worked'), 1) }}</p>
            </div>
            
            <div class="p-4 bg-purple-50 rounded-lg">
                <p class="text-xs text-purple-600 font-medium mb-1">Total Pembayaran</p>
                <p class="text-2xl font-bold text-purple-900">Rp {{ number_format($payrollRequest->total_amount, 0) }}</p>
            </div>
        </div>
    </x-card>
</div>
@endsection