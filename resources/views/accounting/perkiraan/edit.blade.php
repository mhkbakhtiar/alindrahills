{{-- resources/views/accounting/perkiraan/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Perkiraan')
@section('breadcrumb', 'Accounting / Perkiraan / Edit')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Edit Perkiraan</h2>
            <p class="text-xs text-gray-600 mt-1">{{ $perkiraan->kode_perkiraan }} - {{ $perkiraan->nama_perkiraan }}</p>
        </div>
        <x-button variant="secondary" href="{{ route('accounting.perkiraan.show', $perkiraan) }}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </x-button>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="font-semibold text-sm mb-2">Terdapat kesalahan:</div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-card>
        <form method="POST" action="{{ route('accounting.perkiraan.update', $perkiraan) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Basic Information --}}
            <div class="border-b pb-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">
                            Kode Perkiraan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kode_perkiraan" value="{{ old('kode_perkiraan', $perkiraan->kode_perkiraan) }}" 
                            class="w-full px-3 py-2 text-xs border rounded-lg @error('kode_perkiraan') border-red-500 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('kode_perkiraan')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">
                            Jenis Akun <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_akun" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Jenis</option>
                            @foreach($jenis_akun as $jenis)
                                <option value="{{ $jenis }}" {{ old('jenis_akun', $perkiraan->jenis_akun) == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-medium mb-1 text-gray-700">
                        Nama Perkiraan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_perkiraan" value="{{ old('nama_perkiraan', $perkiraan->nama_perkiraan) }}" 
                        class="w-full px-3 py-2 text-xs border rounded-lg @error('nama_perkiraan') border-red-500 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('nama_perkiraan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Additional Information --}}
            <div class="border-b pb-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Informasi Tambahan</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Kategori</label>
                        <select name="kategori" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k }}" {{ old('kategori', $perkiraan->kategori) == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Departemen</label>
                        <input type="text" name="departemen" value="{{ old('departemen', $perkiraan->departemen) }}" 
                            class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Parent Perkiraan</label>
                        <select name="parent_id" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Tidak Ada (Header)</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $perkiraan->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->kode_perkiraan }} - {{ $parent->nama_perkiraan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Anggaran (Opsional)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-xs text-gray-500">Rp</span>
                            <input type="number" name="anggaran" value="{{ old('anggaran', $perkiraan->anggaran) }}" 
                                class="w-full pl-8 pr-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                step="0.01" min="0">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flags & Settings --}}
            <div class="border-b pb-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Pengaturan Akun</h3>
                
                <div class="space-y-3">
                    <label class="flex items-start">
                        <input type="checkbox" name="is_header" value="1" {{ old('is_header', $perkiraan->is_header) ? 'checked' : '' }}
                            class="mt-0.5 mr-2 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500">
                        <div>
                            <span class="text-xs font-medium text-gray-700">Header Account</span>
                            <p class="text-xs text-gray-500">Akun ini hanya untuk grouping, tidak bisa digunakan untuk transaksi</p>
                        </div>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="is_cash_bank" value="1" {{ old('is_cash_bank', $perkiraan->is_cash_bank) ? 'checked' : '' }}
                            class="mt-0.5 mr-2 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500">
                        <div>
                            <span class="text-xs font-medium text-gray-700">Akun Kas/Bank</span>
                            <p class="text-xs text-gray-500">Tandai jika akun ini merupakan Kas atau Bank</p>
                        </div>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $perkiraan->is_active) ? 'checked' : '' }}
                            class="mt-0.5 mr-2 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500">
                        <div>
                            <span class="text-xs font-medium text-gray-700">Status Aktif</span>
                            <p class="text-xs text-gray-500">Nonaktifkan jika akun tidak digunakan lagi</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">Keterangan</label>
                <textarea name="keterangan" rows="3" 
                    class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan', $perkiraan->keterangan) }}</textarea>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-between pt-4 border-t">
                @if(!$perkiraan->hasTransactions() && $perkiraan->children->count() == 0)
                <button type="button" 
                    onclick="if(confirm('Yakin ingin menghapus perkiraan ini?')) document.getElementById('delete-form').submit();"
                    class="px-4 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
                </button>
                @else
                <div class="text-xs text-gray-500">
                    @if($perkiraan->hasTransactions())
                        ⚠️ Tidak bisa dihapus: memiliki transaksi
                    @elseif($perkiraan->children->count() > 0)
                        ⚠️ Tidak bisa dihapus: memiliki sub-akun
                    @endif
                </div>
                @endif

                <div class="flex space-x-2">
                    <x-button type="button" variant="secondary" onclick="window.history.back()">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Perkiraan
                    </x-button>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Warning Card --}}
    @if($perkiraan->hasTransactions())
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="text-xs">
                <div class="font-semibold text-yellow-800 mb-1">Perhatian</div>
                <p class="text-yellow-700">Akun ini sudah memiliki {{ $perkiraan->itemJurnal->count() }} transaksi. Perubahan kode atau jenis akun dapat mempengaruhi laporan keuangan.</p>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Delete Form (Hidden) --}}
<form id="delete-form" action="{{ route('accounting.perkiraan.destroy', $perkiraan) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection