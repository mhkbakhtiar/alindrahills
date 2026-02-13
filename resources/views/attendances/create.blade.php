@extends('layouts.app')

@section('title', 'Tambah Absensi')
@section('breadcrumb', 'Payroll / Absensi Tukang / Tambah')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Tambah Absensi Baru">
        <form method="POST" action="{{ route('attendances.store') }}">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Tukang <span class="text-red-500">*</span>
                    </label>
                    <select name="worker_id" required class="w-full px-3 py-2 text-sm border rounded @error('worker_id') border-red-500 @enderror">
                        <option value="">-- Pilih Tukang --</option>
                        @foreach($workers as $worker)
                            <option value="{{ $worker->worker_id }}" {{ old('worker_id') == $worker->worker_id ? 'selected' : '' }}>
                                {{ $worker->worker_code }} - {{ $worker->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('worker_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <x-input 
                    label="Tanggal" 
                    name="attendance_date" 
                    type="date"
                    :required="true"
                    :value="old('attendance_date', date('Y-m-d'))"
                />

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required class="w-full px-3 py-2 text-sm border rounded @error('status') border-red-500 @enderror">
                        <option value="hadir" {{ old('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="izin" {{ old('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ old('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="alpha" {{ old('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <x-input 
                    label="Check In" 
                    name="check_in" 
                    type="time"
                    :value="old('check_in')"
                />

                <x-input 
                    label="Check Out" 
                    name="check_out" 
                    type="time"
                    :value="old('check_out')"
                />

                <x-input 
                    label="Jam Kerja (opsional)" 
                    name="hours_worked" 
                    type="number"
                    step="0.5"
                    :value="old('hours_worked')"
                    placeholder="Kosongkan untuk hitung otomatis"
                />
            </div>

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2 text-sm border rounded @error('notes') border-red-500 @enderror" placeholder="Tambahkan catatan jika diperlukan">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-4">
                <p class="text-xs text-blue-800">
                    <strong>Info:</strong> Jika jam kerja tidak diisi, sistem akan menghitung otomatis berdasarkan check in dan check out.
                </p>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <x-button type="button" variant="secondary" href="{{ route('attendances.index') }}">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection