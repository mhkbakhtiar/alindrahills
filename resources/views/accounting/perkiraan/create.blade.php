{{-- resources/views/accounting/perkiraan/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Perkiraan')
@section('breadcrumb', 'Accounting / Perkiraan / Tambah')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Tambah Perkiraan Baru</h2>
            <p class="text-xs text-gray-600 mt-1">Buat akun perkiraan baru untuk sistem akuntansi</p>
        </div>
        <x-button variant="secondary" href="{{ route('accounting.perkiraan.index') }}">
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
        <form method="POST" action="{{ route('accounting.perkiraan.store') }}" class="space-y-6">
            @csrf

            {{-- Basic Information --}}
            <div class="border-b pb-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">
                            Kode Perkiraan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kode_perkiraan" value="{{ old('kode_perkiraan') }}" 
                            placeholder="Contoh: 1-1001"
                            class="w-full px-3 py-2 text-xs border rounded-lg @error('kode_perkiraan') border-red-500 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            required>
                        @error('kode_perkiraan')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-500 mt-1">Format: X-XXXX (contoh: 1-1001)</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">
                            Jenis Akun <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_akun" 
                            class="w-full px-3 py-2 text-xs border rounded-lg @error('jenis_akun') border-red-500 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            required>
                            <option value="">Pilih Jenis Akun</option>
                            @foreach($jenis_akun as $jenis)
                                <option value="{{ $jenis }}" {{ old('jenis_akun') == $jenis ? 'selected' : '' }}>
                                    {{ $jenis }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_akun')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-500 mt-1">1=Aset, 2=Kewajiban, 3=Modal, 4=Pendapatan, 5=Biaya</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-medium mb-1 text-gray-700">
                        Nama Perkiraan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_perkiraan" value="{{ old('nama_perkiraan') }}" 
                        placeholder="Contoh: Kas Kecil"
                        class="w-full px-3 py-2 text-xs border rounded-lg @error('nama_perkiraan') border-red-500 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required>
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
                            <option value="">Pilih Kategori (Opsional)</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k }}" {{ old('kategori') == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Sub-grouping untuk laporan</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Departemen</label>
                        <input type="text" name="departemen" value="{{ old('departemen') }}" 
                            placeholder="Contoh: Keuangan"
                            class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Departemen yang mengelola akun ini</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Parent Perkiraan</label>
                        <select name="parent_id" class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Tidak Ada (Root/Header)</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->kode_perkiraan }} - {{ $parent->nama_perkiraan }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Untuk struktur hierarchical</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Anggaran (Opsional)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-xs text-gray-500">Rp</span>
                            <input type="number" name="anggaran" value="{{ old('anggaran') }}" 
                                placeholder="0"
                                class="w-full pl-8 pr-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                step="0.01" min="0">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Target anggaran untuk akun ini</p>
                    </div>
                </div>
            </div>

            {{-- Flags & Settings --}}
            <div class="border-b pb-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Pengaturan Akun</h3>
                
                <div class="space-y-3">
                    <label class="flex items-start">
                        <input type="checkbox" name="is_header" value="1" {{ old('is_header') ? 'checked' : '' }}
                            class="mt-0.5 mr-2 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500">
                        <div>
                            <span class="text-xs font-medium text-gray-700">Header Account</span>
                            <p class="text-xs text-gray-500">Akun ini hanya untuk grouping, tidak bisa digunakan untuk transaksi</p>
                        </div>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="is_cash_bank" value="1" {{ old('is_cash_bank') ? 'checked' : '' }}
                            class="mt-0.5 mr-2 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500">
                        <div>
                            <span class="text-xs font-medium text-gray-700">Akun Kas/Bank</span>
                            <p class="text-xs text-gray-500">Tandai jika akun ini merupakan Kas atau Bank</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">Keterangan</label>
                <textarea name="keterangan" rows="3" 
                    placeholder="Catatan atau deskripsi tambahan mengenai akun ini..."
                    class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Informasi tambahan (opsional)</p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end space-x-2 pt-4 border-t">
                <x-button type="button" variant="secondary" onclick="window.history.back()">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perkiraan
                </x-button>
            </div>
        </form>
    </x-card>

    {{-- Help Card --}}
    <x-card>
        <h3 class="text-sm font-semibold text-gray-900 mb-3">💡 Panduan Pengisian</h3>
        <div class="space-y-2 text-xs text-gray-600">
            <div class="flex gap-2">
                <span class="font-semibold min-w-[120px]">Kode Perkiraan:</span>
                <span>Gunakan format X-XXXX dimana digit pertama menunjukkan jenis (1=Aset, 2=Kewajiban, dst)</span>
            </div>
            <div class="flex gap-2">
                <span class="font-semibold min-w-[120px]">Header Account:</span>
                <span>Centang jika akun ini hanya untuk grouping/summary, tidak bisa digunakan untuk input transaksi</span>
            </div>
            <div class="flex gap-2">
                <span class="font-semibold min-w-[120px]">Kas/Bank:</span>
                <span>Centang untuk akun kas atau bank agar mudah diidentifikasi</span>
            </div>
            <div class="flex gap-2">
                <span class="font-semibold min-w-[120px]">Anggaran:</span>
                <span>Isi untuk akun Biaya agar bisa monitor realisasi vs budget</span>
            </div>
        </div>
    </x-card>
</div>
@endsection