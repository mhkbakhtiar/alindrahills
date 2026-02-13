@extends('layouts.app')

@section('title', 'Pengajuan Pembelian')
@section('breadcrumb', 'Material Management / Pengajuan Pembelian')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Pengajuan Pembelian Material</h2>
        @if(auth()->user()->isTeknik() || (auth()->user()->isSuperadmin()))
            <x-button variant="primary" href="{{ route('purchase-requests.create') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Pengajuan
            </x-button>
        @endif
    </div>

    <x-card>
        <form method="GET" class="flex gap-3 mb-4">
            <select name="status" class="pe-8 py-2 text-sm border rounded">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="purchased" {{ request('status') == 'purchased' ? 'selected' : '' }}>Purchased</option>
            </select>
            <x-button type="submit" variant="secondary">Filter</x-button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No. Pengajuan</th>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">Diajukan Oleh</th>
                        <th class="px-3 py-2 text-left font-semibold">Surat Pengantar</th>
                        <th class="px-3 py-2 text-left font-semibold">Tujuan</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $request->request_number }}</td>
                            <td class="px-3 py-2">{{ $request->request_date->format('d M Y') }}</td>
                            <td class="px-3 py-2">{{ $request->requester->full_name }}</td>
                            <td class="px-3 py-2">{{ $request->letter_number }}</td>
                            <td class="px-3 py-2">{{ Str::limit($request->purpose, 40) }}</td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $statusVariants = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'purchased' => 'info',
                                    ];
                                @endphp
                                <x-badge :variant="$statusVariants[$request->status] ?? 'default'">
                                    {{ ucfirst($request->status) }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('purchase-requests.show', $request) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
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