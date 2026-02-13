@extends('layouts.app')

@section('title', 'Laporan Bulanan Absensi')
@section('breadcrumb', 'Payroll / Absensi Tukang / Laporan Bulanan')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Laporan Bulanan Absensi</h2>
        <x-button variant="secondary" href="{{ route('attendances.index') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </x-button>
    </div>

    <!-- Filter Section -->
    <x-card>
        <form method="GET" action="{{ route('attendances.monthly-report') }}" class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
                <select name="month" class="w-full px-3 py-2 text-sm border rounded">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun</label>
                <select name="year" class="w-full px-3 py-2 text-sm border rounded">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="flex items-end col-span-2">
                <x-button type="submit" variant="primary" class="w-auto">Tampilkan Laporan</x-button>
            </div>
        </form>
    </x-card>

    <!-- Summary Cards -->
    <div class="grid grid-cols-4 gap-4">
        <x-card>
            <div class="text-center">
                <p class="text-xs text-gray-500 mb-1">Total Tukang</p>
                <p class="text-2xl font-bold text-gray-900">{{ count($report) }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="text-center">
                <p class="text-xs text-gray-500 mb-1">Total Kehadiran</p>
                <p class="text-2xl font-bold text-green-600">{{ collect($report)->sum('hadir') }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="text-center">
                <p class="text-xs text-gray-500 mb-1">Total Izin/Sakit</p>
                <p class="text-2xl font-bold text-yellow-600">{{ collect($report)->sum('izin') + collect($report)->sum('sakit') }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="text-center">
                <p class="text-xs text-gray-500 mb-1">Total Alpha</p>
                <p class="text-2xl font-bold text-red-600">{{ collect($report)->sum('alpha') }}</p>
            </div>
        </x-card>
    </div>

    <!-- Report Table -->
    <x-card>
        <div class="mb-4 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-900">
                Periode: {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
            </h3>
            <form method="GET" action="{{ route('attendances.export-pdf') }}" class="inline">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <button type="submit" class="px-4 py-2 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export PDF
                </button>
            </form>
        </div>


        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No</th>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Tukang</th>
                        <th class="px-3 py-2 text-left font-semibold">Jenis</th>
                        <th class="px-3 py-2 text-center font-semibold">Total Hari</th>
                        <th class="px-3 py-2 text-center font-semibold bg-green-50">Hadir</th>
                        <th class="px-3 py-2 text-center font-semibold bg-yellow-50">Izin</th>
                        <th class="px-3 py-2 text-center font-semibold bg-blue-50">Sakit</th>
                        <th class="px-3 py-2 text-center font-semibold bg-red-50">Alpha</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Jam</th>
                        <th class="px-3 py-2 text-right font-semibold">Upah Harian</th>
                        <th class="px-3 py-2 text-right font-semibold">Total Upah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php $no = 1; @endphp
                    @forelse($report as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $no++ }}</td>
                            <td class="px-3 py-2 font-medium">{{ $item['worker']->worker_code }}</td>
                            <td class="px-3 py-2">{{ $item['worker']->full_name }}</td>
                            <td class="px-3 py-2">{{ $item['worker']->worker_type }}</td>
                            <td class="px-3 py-2 text-center font-medium">{{ $item['total_days'] }}</td>
                            <td class="px-3 py-2 text-center bg-green-50">{{ $item['hadir'] }}</td>
                            <td class="px-3 py-2 text-center bg-yellow-50">{{ $item['izin'] }}</td>
                            <td class="px-3 py-2 text-center bg-blue-50">{{ $item['sakit'] }}</td>
                            <td class="px-3 py-2 text-center bg-red-50">{{ $item['alpha'] }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($item['total_hours'], 1) }} jam</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($item['worker']->daily_rate, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right font-medium text-green-600">
                                Rp {{ number_format($item['hadir'] * $item['worker']->daily_rate, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-3 py-8 text-center text-gray-500">Tidak ada data untuk periode ini</td>
                        </tr>
                    @endforelse
                    
                    @if(count($report) > 0)
                    <tr class="bg-gray-100 font-semibold">
                        <td colspan="4" class="px-3 py-2 text-right">TOTAL:</td>
                        <td class="px-3 py-2 text-center">{{ collect($report)->sum('total_days') }}</td>
                        <td class="px-3 py-2 text-center bg-green-100">{{ collect($report)->sum('hadir') }}</td>
                        <td class="px-3 py-2 text-center bg-yellow-100">{{ collect($report)->sum('izin') }}</td>
                        <td class="px-3 py-2 text-center bg-blue-100">{{ collect($report)->sum('sakit') }}</td>
                        <td class="px-3 py-2 text-center bg-red-100">{{ collect($report)->sum('alpha') }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format(collect($report)->sum('total_hours'), 1) }} jam</td>
                        <td class="px-3 py-2 text-right">-</td>
                        <td class="px-3 py-2 text-right text-green-600">
                            Rp {{ number_format(collect($report)->sum(function($item) {
                                return $item['hadir'] * $item['worker']->daily_rate;
                            }), 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </x-card>
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection