@extends('layouts.app')

@section('title', 'Laporan Penggajian')
@section('breadcrumb', 'Laporan / Penggajian')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Laporan Penggajian Tukang</h2>
        <div class="flex gap-2">
            <x-button variant="secondary" href="{{ route('reports.payroll.worker-summary') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Ringkasan per Tukang
            </x-button>
            <form action="{{ route('reports.payroll.export') }}" method="GET" class="inline">
                @foreach(request()->all() as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <x-button variant="primary" type="submit">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </x-button>
            </form>
        </div>
    </div>

    <!-- Filter Form -->
    <x-card>
        <form method="GET" action="{{ route('reports.payroll.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kegiatan</label>
                <select name="activity_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kegiatan</option>
                    @foreach($activities as $activity)
                        <option value="{{ $activity->activity_id }}" {{ request('activity_id') == $activity->activity_id ? 'selected' : '' }}>
                            {{ $activity->activity_code }} - {{ $activity->activity_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 flex gap-2">
                <x-button variant="primary" type="submit">Filter</x-button>
                <x-button variant="secondary" href="{{ route('reports.payroll.index') }}">Reset</x-button>
            </div>
        </form>
    </x-card>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-card>
            <div class="text-sm text-gray-600">Total Pengajuan</div>
            <div class="text-2xl font-bold text-gray-900">{{ $summary['total_requests'] }}</div>
        </x-card>
        <x-card>
            <div class="text-sm text-gray-600">Total Nominal</div>
            <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($summary['total_amount'], 0) }}</div>
        </x-card>
        <x-card>
            <div class="text-sm text-gray-600">Sudah Dibayar</div>
            <div class="text-2xl font-bold text-green-600">Rp {{ number_format($summary['paid_amount'], 0) }}</div>
        </x-card>
        <x-card>
            <div class="text-sm text-gray-600">Total Tukang</div>
            <div class="text-2xl font-bold text-purple-600">{{ $summary['total_workers'] }}</div>
        </x-card>
    </div>

    <!-- Status Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600">Pending</div>
                    <div class="text-xl font-bold text-yellow-600">{{ $summary['pending'] }}</div>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600">Approved</div>
                    <div class="text-xl font-bold text-green-600">{{ $summary['approved'] }}</div>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600">Rejected</div>
                    <div class="text-xl font-bold text-red-600">{{ $summary['rejected'] }}</div>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600">Paid</div>
                    <div class="text-xl font-bold text-blue-600">{{ $summary['paid'] }}</div>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Monthly Breakdown -->
    <x-card>
        <h3 class="text-lg font-semibold mb-4">Ringkasan per Bulan</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Bulan</th>
                        <th class="px-3 py-2 text-center font-semibold">Jumlah Pengajuan</th>
                        <th class="px-3 py-2 text-center font-semibold">Total Tukang</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($byMonth as $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $data['month'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $data['count'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $data['workers'] }}</td>
                            <td class="px-3 py-2 text-right font-medium">Rp {{ number_format($data['total_amount'], 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Top Workers -->
    <x-card>
        <h3 class="text-lg font-semibold mb-4">Top 10 Tukang - Total Pendapatan</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Nama Tukang</th>
                        <th class="px-3 py-2 text-center font-semibold">Total Pengajuan</th>
                        <th class="px-3 py-2 text-center font-semibold">Total Hari Kerja</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($topWorkers as $worker)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $worker->full_name }}</td>
                            <td class="px-3 py-2 text-center">{{ $worker->total_payrolls }}</td>
                            <td class="px-3 py-2 text-center">{{ $worker->total_days }}</td>
                            <td class="px-3 py-2 text-right font-medium text-green-600">Rp {{ number_format($worker->total_earned, 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Payroll List -->
    <x-card>
        <h3 class="text-lg font-semibold mb-4">Daftar Pengajuan Penggajian</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No. Pengajuan</th>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">Periode</th>
                        <th class="px-3 py-2 text-left font-semibold">Kegiatan</th>
                        <th class="px-3 py-2 text-center font-semibold">Jumlah Tukang</th>
                        <th class="px-3 py-2 text-right font-semibold">Total</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payrolls as $payroll)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $payroll->request_number }}</td>
                            <td class="px-3 py-2">{{ $payroll->request_date->format('d M Y') }}</td>
                            <td class="px-3 py-2">{{ $payroll->period_start->format('d M') }} - {{ $payroll->period_end->format('d M Y') }}</td>
                            <td class="px-3 py-2">{{ $payroll->activity?->activity_code ?? '-' }}</td>
                            <td class="px-3 py-2 text-center">{{ $payroll->details->count() }}</td>
                            <td class="px-3 py-2 text-right font-medium">Rp {{ number_format($payroll->total_amount, 0) }}</td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $statusVariants = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'paid' => 'info'];
                                @endphp
                                <x-badge :variant="$statusVariants[$payroll->status]">{{ ucfirst($payroll->status) }}</x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection