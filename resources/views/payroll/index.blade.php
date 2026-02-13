@extends('layouts.app')

@section('title', 'Pengajuan Penggajian')
@section('breadcrumb', 'Payroll / Pengajuan Penggajian')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Pengajuan Penggajian Tukang</h2>
        @if(auth()->user()->isTeknik() || (auth()->user()->isSuperadmin()))
            <x-button variant="primary" href="{{ route('payroll-requests.create') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Pengajuan
            </x-button>
        @endif
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No. Pengajuan</th>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">Periode</th>
                        <th class="px-3 py-2 text-left font-semibold">Diajukan Oleh</th>
                        <th class="px-3 py-2 text-right font-semibold">Total</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $request->request_number }}</td>
                            <td class="px-3 py-2">{{ $request->request_date->format('d M Y') }}</td>
                            <td class="px-3 py-2">{{ $request->period_start->format('d M') }} - {{ $request->period_end->format('d M Y') }}</td>
                            <td class="px-3 py-2">{{ $request->requester->full_name }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($request->total_amount, 0) }}</td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $statusVariants = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'paid' => 'info'];
                                @endphp
                                <x-badge :variant="$statusVariants[$request->status]">
                                    {{ ucfirst($request->status) }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('payroll-requests.show', $request) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-gray-500">Tidak ada pengajuan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </x-card>
</div>
@endsection