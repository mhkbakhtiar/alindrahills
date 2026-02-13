@extends('layouts.app')

@section('title', 'Absensi Tukang')
@section('breadcrumb', 'Payroll / Absensi Tukang')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Absensi Tukang</h2>
        <div class="flex gap-2">
            <x-button variant="secondary" href="{{ route('attendances.monthly-report') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Laporan Bulanan
            </x-button>
            <x-button variant="success" onclick="document.getElementById('bulkModal').classList.remove('hidden')">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Absen Masal
            </x-button>
            <x-button variant="primary" href="{{ route('attendances.create') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Absensi
            </x-button>
        </div>
    </div>

    <!-- Filter Section -->
    <x-card>
        <form method="GET" action="{{ route('attendances.index') }}" class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date', $selectedDate) }}" 
                       class="w-full px-3 py-2 text-sm border rounded">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tukang</label>
                <select name="worker_id" class="w-full px-3 py-2 text-sm border rounded">
                    <option value="">Semua Tukang</option>
                    @foreach($workers as $worker)
                        <option value="{{ $worker->worker_id }}" {{ request('worker_id') == $worker->worker_id ? 'selected' : '' }}>
                            {{ $worker->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 text-sm border rounded">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>

            <div class="flex items-end">
                <x-button type="submit" variant="primary" class="w-auto">Filter</x-button>
            </div>
        </form>
    </x-card>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">Kode</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Tukang</th>
                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                        <th class="px-3 py-2 text-center font-semibold">Check In</th>
                        <th class="px-3 py-2 text-center font-semibold">Check Out</th>
                        <th class="px-3 py-2 text-center font-semibold">Jam Kerja</th>
                        <th class="px-3 py-2 text-left font-semibold">Catatan</th>
                        <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $attendance->attendance_date->format('d/m/Y') }}</td>
                            <td class="px-3 py-2 font-medium">{{ $attendance->worker->worker_code }}</td>
                            <td class="px-3 py-2">{{ $attendance->worker->full_name }}</td>
                            <td class="px-3 py-2 text-center">
                                <x-badge :variant="$attendance->status_badge">
                                    {{ ucfirst($attendance->status) }}
                                </x-badge>
                            </td>
                            <td class="px-3 py-2 text-center">
                                {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                {{ $attendance->hours_worked > 0 ? number_format($attendance->hours_worked, 1) . ' jam' : '-' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $attendance->notes ? Str::limit($attendance->notes, 30) : '-' }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('attendances.show', $attendance) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                                    <a href="{{ route('attendances.edit', $attendance) }}" class="text-green-600 hover:text-green-800">Edit</a>
                                    <form method="POST" action="{{ route('attendances.destroy', $attendance) }}" 
                                          onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-8 text-center text-gray-500">Tidak ada data absensi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $attendances->appends(request()->query())->links() }}
        </div>
    </x-card>
</div>

<!-- Bulk Create Modal -->
<div id="bulkModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Absensi Masal</h3>
            <form method="POST" action="{{ route('attendances.bulk-create') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="attendance_date" required 
                               class="w-full px-3 py-2 text-sm border rounded" value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status Default</label>
                        <select name="status" required class="w-full px-3 py-2 text-sm border rounded">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpha">Alpha</option>
                        </select>
                    </div>
                    <p class="text-xs text-gray-600">Akan membuat absensi untuk semua tukang aktif</p>
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="button" onclick="document.getElementById('bulkModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 text-sm bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                        Buat Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection