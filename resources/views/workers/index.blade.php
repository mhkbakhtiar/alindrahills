@extends('layouts.app')

@section('title', 'Data Tukang')
@section('breadcrumb', 'Payroll / Data Tukang')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Data Tukang</h2>
        <x-button variant="primary" href="{{ route('workers.create') }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Tukang
        </x-button>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama</th>
                        <th class="px-3 py-2 text-left font-semibold">Jenis</th>
                        <th class="px-3 py-2 text-left font-semibold">No. Telp</th>
                        <th class="px-3 py-2 text-right font-semibold">Upah Harian</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($workers as $worker)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium">{{ $worker->worker_code }}</td>
                            <td class="px-3 py-2">{{ $worker->full_name }}</td>
                            <td class="px-3 py-2">{{ $worker->worker_type }}</td>
                            <td class="px-3 py-2">{{ $worker->phone }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($worker->daily_rate, 0) }}</td>
                            <td class="px-3 py-2 text-center">
                                <x-badge :variant="$worker->is_active ? 'success' : 'danger'">
                                    {{ $worker->is_active ? 'Active' : 'Inactive' }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('workers.edit', $worker) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-gray-500">Tidak ada data tukang</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $workers->links() }}
        </div>
    </x-card>
</div>
@endsection