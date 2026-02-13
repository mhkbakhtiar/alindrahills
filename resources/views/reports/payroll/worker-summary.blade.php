@extends('layouts.app')

@section('title', 'Ringkasan Tukang')
@section('breadcrumb', 'Laporan / Penggajian / Ringkasan per Tukang')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Ringkasan Penggajian per Tukang</h2>
        <x-button variant="secondary" href="{{ route('reports.payroll.index') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </x-button>
    </div>

    <!-- Filter Form -->
    <x-card>
        <form method="GET" action="{{ route('reports.payroll.worker-summary') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tukang</label>
                <select name="worker_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Tukang</option>
                    @foreach($allWorkers as $w)
                        <option value="{{ $w->worker_id }}" {{ request('worker_id') == $w->worker_id ? 'selected' : '' }}>
                            {{ $w->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <x-button variant="primary" type="submit" class="w-full">Filter</x-button>
            </div>
        </form>
    </x-card>

    <!-- Worker Summary Cards -->
    @foreach($workers as $data)
        <x-card>
            <div class="border-b pb-3 mb-3">
                <h3 class="text-lg font-semibold text-gray-900">{{ $data['worker']->full_name }}</h3>
                <p class="text-sm text-gray-600">{{ $data['worker']->phone ?? '-' }}</p>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-4">
                <div>
                    <div class="text-xs text-gray-600">Total Pengajuan</div>
                    <div class="text-lg font-bold text-gray-900">{{ $data['total_payrolls'] }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-600">Total Hari Kerja</div>
                    <div class="text-lg font-bold text-blue-600">{{ $data['total_days'] }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-600">Total Upah</div>
                    <div class="text-lg font-bold text-gray-900">Rp {{ number_format($data['total_wage'], 0) }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-600">Total Bonus</div>
                    <div class="text-lg font-bold text-green-600">Rp {{ number_format($data['total_bonus'], 0) }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-600">Total Potongan</div>
                    <div class="text-lg font-bold text-red-600">Rp {{ number_format($data['total_deduction'], 0) }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-600">Total Diterima</div>
                    <div class="text-lg font-bold text-green-600">Rp {{ number_format($data['total_earned'], 0) }}</div>
                </div>
            </div>

            <!-- Detail List -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">No. Pengajuan</th>
                            <th class="px-3 py-2 text-left font-semibold">Periode</th>
                            <th class="px-3 py-2 text-center font-semibold">Hari Kerja</th>
                            <th class="px-3 py-2 text-right font-semibold">Upah Harian</th>
                            <th class="px-3 py-2 text-right font-semibold">Total Upah</th>
                            <th class="px-3 py-2 text-right font-semibold">Bonus</th>
                            <th class="px-3 py-2 text-right font-semibold">Potongan</th>
                            <th class="px-3 py-2 text-right font-semibold">Diterima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($data['details'] as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium">{{ $detail->payrollRequest->request_number }}</td>
                                <td class="px-3 py-2">{{ $detail->payrollRequest->period_start->format('d M') }} - {{ $detail->payrollRequest->period_end->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-center">{{ $detail->days_worked }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($detail->daily_rate, 0) }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($detail->total_wage, 0) }}</td>
                                <td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($detail->bonus, 0) }}</td>
                                <td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($detail->deduction, 0) }}</td>
                                <td class="px-3 py-2 text-right font-medium">Rp {{ number_format($detail->net_payment, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endforeach

    @if($workers->isEmpty())
        <x-card>
            <div class="text-center py-8 text-gray-500">Tidak ada data</div>
        </x-card>
    @endif
</div>
@endsection