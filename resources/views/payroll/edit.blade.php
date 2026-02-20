@extends('layouts.app')

@section('title', 'Edit Pengajuan Penggajian')
@section('breadcrumb', 'Payroll / Pengajuan Penggajian / Edit')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Edit Pengajuan Penggajian</h2>
        <p class="text-sm text-gray-600">{{ $payrollRequest->request_number }}</p>
    </div>

    @if($payrollRequest->status !== 'pending')
        <x-card class="mb-4 bg-yellow-50 border-yellow-200">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm text-yellow-800">Pengajuan ini tidak dapat diubah karena sudah diproses.</p>
            </div>
        </x-card>
    @endif

    <form action="{{ route('payroll-requests.update', $payrollRequest) }}" method="POST" id="payrollForm">
        @csrf
        @method('PATCH')
        
        <x-card class="mb-4" title="Informasi Pengajuan">
            
            @if($payrollRequest->status === 'pending')
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <label class="block text-sm font-medium text-blue-900 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pilih Kegiatan (Opsional)
                    </label>
                    <select id="activitySelect" class="w-full px-3 py-2 border border-blue-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih kegiatan untuk auto-fill tukang --</option>
                        @foreach($activities as $activity)
                            <option value="{{ $activity->activity_id }}" {{ $payrollRequest->activity_id == $activity->activity_id ? 'selected' : '' }}>
                                {{ $activity->activity_code }} - {{ $activity->activity_name }} 
                                ({{ $activity->location->location_name ?? '-' }})
                                @if($activity->activityWorkers->count() > 0)
                                    - {{ $activity->activityWorkers->count() }} Tukang
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="activity_id" id="activityIdInput" value="{{ $payrollRequest->activity_id }}">
                    <p class="mt-2 text-xs text-blue-700">
                        Pilih kegiatan untuk otomatis mengisi daftar tukang yang terlibat dalam kegiatan tersebut
                    </p>
                </div>
            @else
                @if($payrollRequest->activity)
                    <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-md">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Kegiatan Terkait</label>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $payrollRequest->activity->activity_code }} - {{ $payrollRequest->activity->activity_name }}</p>
                                <p class="text-xs text-gray-600">{{ $payrollRequest->activity->location->location_name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengajuan</label>
                    <input type="date" name="request_date" 
                        value="{{ old('request_date', $payrollRequest->request_date->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('request_date') border-red-500 @enderror" 
                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : 'required' }}>
                    @error('request_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Mulai</label>
                    <input type="date" name="period_start" 
                        value="{{ old('period_start', $payrollRequest->period_start->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('period_start') border-red-500 @enderror" 
                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : 'required' }}>
                    @error('period_start')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Selesai</label>
                    <input type="date" name="period_end" 
                        value="{{ old('period_end', $payrollRequest->period_end->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('period_end') border-red-500 @enderror" 
                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : 'required' }}>
                    @error('period_end')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat</label>
                    <input type="date" name="letter_date" 
                        value="{{ old('letter_date', $payrollRequest->letter_date->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('letter_date') border-red-500 @enderror" 
                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : 'required' }}>
                    @error('letter_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror" 
                        placeholder="Catatan tambahan (opsional)" 
                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : '' }}>{{ old('notes', $payrollRequest->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-card>

        <x-card class="mb-4" title="Daftar Tukang">
            <div class="flex items-center justify-between mb-4">
                @if($payrollRequest->status === 'pending')
                    <button type="button" onclick="addWorker()" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Tukang
                    </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Tukang</th>
                            <th class="px-3 py-2 text-center font-semibold">Hari Kerja</th>
                            <th class="px-3 py-2 text-right font-semibold">Upah/Hari</th>
                            <th class="px-3 py-2 text-right font-semibold">Total Upah</th>
                            <th class="px-3 py-2 text-right font-semibold">Bonus</th>
                            <th class="px-3 py-2 text-right font-semibold">Potongan</th>
                            <th class="px-3 py-2 text-right font-semibold">Total Bersih</th>
                            @if($payrollRequest->status === 'pending')
                                <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="workerTableBody" class="divide-y divide-gray-200">
                        @foreach($payrollRequest->details as $index => $detail)
                            <tr id="worker-row-{{ $index }}" class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <select name="workers[{{ $index }}][worker_id]" 
                                        onchange="updateDailyRate({{ $index }})"
                                        class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:ring-blue-500 focus:border-blue-500" 
                                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : 'required' }}>
                                        @foreach($workers as $worker)
                                            <option value="{{ $worker->worker_id }}" 
                                                data-rate="{{ $worker->daily_rate }}"
                                                {{ $detail->worker_id == $worker->worker_id ? 'selected' : '' }}>
                                                {{ $worker->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="workers[{{ $index }}][days_worked]" 
                                        step="0.5" min="0.5" value="{{ $detail->days_worked }}"
                                        onchange="calculateWorker({{ $index }})"
                                        class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:ring-blue-500 focus:border-blue-500" 
                                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : 'required' }}>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="workers[{{ $index }}][daily_rate]" 
                                        value="{{ $detail->daily_rate }}" readonly
                                        onchange="calculateWorker({{ $index }})"
                                        class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
                                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : 'required' }}>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="workers[{{ $index }}][total_wage]" 
                                        value="{{ $detail->total_wage }}" readonly
                                        class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right bg-gray-100" 
                                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : 'required' }}>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="workers[{{ $index }}][bonus]" 
                                        value="{{ $detail->bonus }}" min="0"
                                        onchange="calculateWorker({{ $index }})"
                                        class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:ring-blue-500 focus:border-blue-500"
                                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : '' }}>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="workers[{{ $index }}][deduction]" 
                                        value="{{ $detail->deduction }}" min="0"
                                        onchange="calculateWorker({{ $index }})"
                                        class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:ring-blue-500 focus:border-blue-500"
                                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : '' }}>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="workers[{{ $index }}][net_payment]" 
                                        value="{{ $detail->net_payment }}" readonly
                                        class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right bg-gray-100 font-medium" 
                                        {{ $payrollRequest->status !== 'pending' ? 'disabled' : 'required' }}>
                                </td>
                                @if($payrollRequest->status === 'pending')
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" onclick="removeWorker({{ $index }})" class="text-red-600 hover:text-red-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="{{ $payrollRequest->status === 'pending' ? 6 : 6 }}" class="px-3 py-2 text-right font-semibold">Grand Total:</td>
                            <td class="px-3 py-2 text-right font-semibold">
                                <span id="grandTotal">Rp {{ number_format($payrollRequest->total_amount, 0) }}</span>
                            </td>
                            @if($payrollRequest->status === 'pending')
                                <td></td>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>

        <div class="flex gap-2">
            @if($payrollRequest->status === 'pending')
                <x-button variant="primary" type="submit">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Pengajuan
                </x-button>
            @endif
            <x-button variant="secondary" href="{{ route('payroll-requests.index') }}">
                Kembali
            </x-button>
        </div>
    </form>
</div>

<script>
let workerIndex = {{ $payrollRequest->details->count() }};
const workers = @json($workers);
const isPending = {{ $payrollRequest->status === 'pending' ? 'true' : 'false' }};

@if($payrollRequest->status === 'pending')
// Handle activity selection
document.getElementById('activitySelect').addEventListener('change', function() {
    const activityId = this.value;
    
    if (!activityId) {
        document.getElementById('activityIdInput').value = '';
        return;
    }
    
    if (!confirm('Mengganti kegiatan akan mengganti semua data tukang. Lanjutkan?')) {
        // Reset select to previous value
        this.value = document.getElementById('activityIdInput').value;
        return;
    }
    
    // Show loading
    const button = document.querySelector('button[onclick="addWorker()"]');
    if (button) {
        button.disabled = true;
        button.innerHTML = '<svg class="animate-spin w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading...';
    }
    
    // Fetch activity workers
    fetch(`/payroll-requests/activity/${activityId}/workers`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Set activity_id
                document.getElementById('activityIdInput').value = activityId;
                
                // Auto-fill period dates from activity
                if (data.activity.start_date) {
                    document.querySelector('input[name="period_start"]').value = data.activity.start_date;
                }
                if (data.activity.end_date) {
                    document.querySelector('input[name="period_end"]').value = data.activity.end_date;
                }
                
                // Clear existing workers
                const tbody = document.getElementById('workerTableBody');
                tbody.innerHTML = '';
                workerIndex = 0;
                
                // Add workers from activity
                if (data.workers.length > 0) {
                    data.workers.forEach(worker => {
                        addWorkerFromActivity(worker);
                    });
                } else {
                    tbody.innerHTML = '<tr id="emptyState"><td colspan="8" class="px-3 py-8 text-center text-gray-500">Tidak ada tukang dalam kegiatan ini</td></tr>';
                }
                
                updateGrandTotal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat data tukang dari kegiatan');
        })
        .finally(() => {
            if (button) {
                button.disabled = false;
                button.innerHTML = '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Tambah Tukang';
            }
        });
});

function addWorkerFromActivity(workerData) {
    const emptyState = document.getElementById('emptyState');
    if (emptyState) {
        emptyState.remove();
    }

    const tbody = document.getElementById('workerTableBody');
    const row = document.createElement('tr');
    row.id = `worker-row-${workerIndex}`;
    row.className = 'hover:bg-gray-50';
    
    const totalWage = workerData.days_worked * workerData.daily_rate;
    
    row.innerHTML = `
        <td class="px-3 py-2">
            <select name="workers[${workerIndex}][worker_id]" 
                onchange="updateDailyRate(${workerIndex})"
                class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:ring-blue-500 focus:border-blue-500" required>
                ${workers.map(w => `<option value="${w.worker_id}" data-rate="${w.daily_rate}" ${w.worker_id == workerData.worker_id ? 'selected' : ''}>${w.full_name}</option>`).join('')}
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][days_worked]" 
                step="0.5" min="0.5" value="${workerData.days_worked}"
                onchange="calculateWorker(${workerIndex})"
                class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:ring-blue-500 focus:border-blue-500" required>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][daily_rate]" 
                value="${workerData.daily_rate}" readonly
                onchange="calculateWorker(${workerIndex})"
                class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right bg-gray-50 focus:ring-blue-500 focus:border-blue-500" required>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][total_wage]" 
                value="${totalWage}" readonly
                class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right bg-gray-100" required>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][bonus]" 
                value="0" min="0"
                onchange="calculateWorker(${workerIndex})"
                class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:ring-blue-500 focus:border-blue-500">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][deduction]" 
                value="0" min="0"
                onchange="calculateWorker(${workerIndex})"
                class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:ring-blue-500 focus:border-blue-500">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][net_payment]" 
                value="${totalWage}" readonly
                class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right bg-gray-100 font-medium" required>
        </td>
        <td class="px-3 py-2 text-center">
            <button type="button" onclick="removeWorker(${workerIndex})" class="text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    workerIndex++;
}
@endif

function addWorker() {
    if (!isPending) return;
    
    const tbody = document.getElementById('workerTableBody');
    const row = document.createElement('tr');
    row.id = `worker-row-${workerIndex}`;
    row.className = 'hover:bg-gray-50';
    
    row.innerHTML = `
        <td class="px-3 py-2">
            <select name="workers[${workerIndex}][worker_id]" 
                onchange="updateDailyRate(${workerIndex})"
                class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Pilih Tukang</option>
                ${workers.map(w => `<option value="${w.worker_id}" data-rate="${w.daily_rate}">${w.full_name}</option>`).join('')}
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][days_worked]" 
                step="0.5" min="0.5" value="1"
                onchange="calculateWorker(${workerIndex})"
                class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:ring-blue-500 focus:border-blue-500" required>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][daily_rate]" 
                value="0" readonly
                onchange="calculateWorker(${workerIndex})"
                class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right bg-gray-50 focus:ring-blue-500 focus:border-blue-500" required>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][total_wage]" 
                value="0" readonly
                class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right bg-gray-100" required>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][bonus]" 
                value="0" min="0"
                onchange="calculateWorker(${workerIndex})"
                class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:ring-blue-500 focus:border-blue-500">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][deduction]" 
                value="0" min="0"
                onchange="calculateWorker(${workerIndex})"
                class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:ring-blue-500 focus:border-blue-500">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="workers[${workerIndex}][net_payment]" 
                value="0" readonly
                class="w-28 px-2 py-1 border border-gray-300 rounded text-xs text-right bg-gray-100 font-medium" required>
        </td>
        <td class="px-3 py-2 text-center">
            <button type="button" onclick="removeWorker(${workerIndex})" class="text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    workerIndex++;
}

function updateDailyRate(index) {
    const select = document.querySelector(`select[name="workers[${index}][worker_id]"]`);
    const selectedOption = select.options[select.selectedIndex];
    const dailyRate = selectedOption.dataset.rate || 0;
    
    const dailyRateInput = document.querySelector(`input[name="workers[${index}][daily_rate]"]`);
    dailyRateInput.value = dailyRate;
    
    calculateWorker(index);
}

function calculateWorker(index) {
    const daysWorked = parseFloat(document.querySelector(`input[name="workers[${index}][days_worked]"]`).value) || 0;
    const dailyRate = parseFloat(document.querySelector(`input[name="workers[${index}][daily_rate]"]`).value) || 0;
    const bonus = parseFloat(document.querySelector(`input[name="workers[${index}][bonus]"]`).value) || 0;
    const deduction = parseFloat(document.querySelector(`input[name="workers[${index}][deduction]"]`).value) || 0;
    
    const totalWage = daysWorked * dailyRate;
    const netPayment = totalWage + bonus - deduction;
    
    document.querySelector(`input[name="workers[${index}][total_wage]"]`).value = totalWage;
    document.querySelector(`input[name="workers[${index}][net_payment]"]`).value = netPayment;
    
    updateGrandTotal();
}

function removeWorker(index) {
    if (!isPending) return;
    
    const row = document.getElementById(`worker-row-${index}`);
    if (row) {
        row.remove();
        updateGrandTotal();
    }
}

function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('input[name*="[net_payment]"]').forEach(input => {
        if (!input.disabled) {
            total += parseFloat(input.value) || 0;
        }
    });
    
    document.getElementById('grandTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// Form validation
document.getElementById('payrollForm').addEventListener('submit', function(e) {
    if (!isPending) {
        e.preventDefault();
        return false;
    }
    
    const tbody = document.getElementById('workerTableBody');
    if (tbody.children.length === 0) {
        e.preventDefault();
        alert('Minimal harus ada 1 tukang dalam pengajuan');
        return false;
    }
});
</script>
@endsection