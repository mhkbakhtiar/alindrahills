{{-- resources/views/settings/prefix/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Format Nomor')
@section('breadcrumb', 'Settings / Master Format Nomor / Edit')

@section('content')
<div class="space-y-4 max-w-4xl flex flex-col mx-auto">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Edit Format Nomor</h2>
            <p class="text-xs text-gray-500 mt-0.5">
                <code class="px-1.5 py-0.5 bg-blue-50 text-blue-700 rounded font-mono text-xs font-bold">{{ $prefix->kode_jenis }}</code>
                — {{ $prefix->nama_jenis }}
            </p>
        </div>
        <a href="{{ route('settings.prefix.show', $prefix) }}"
            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Warning jika sudah digunakan --}}
    @if($prefix->nomor_terakhir > 0)
        <div class="bg-yellow-50 border border-yellow-300 rounded-lg px-4 py-3 flex items-start gap-2 text-sm text-yellow-800">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-semibold">Prefix ini sudah digunakan (Nomor terakhir: {{ number_format($prefix->nomor_terakhir) }})</p>
                <p class="text-xs mt-0.5">Perubahan format akan berlaku untuk nomor berikutnya. Nomor yang sudah diterbitkan tidak terpengaruh.</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <p class="font-semibold mb-1">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('settings.prefix.update', $prefix) }}" method="POST">
        @csrf
        @method('PATCH')

        {{-- ── Identitas (Kode tidak bisa diedit) ──────────────────────── --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">Identitas Dokumen</h3>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kode Jenis</label>
                    <div class="flex items-center gap-2">
                        <code class="px-3 py-2 bg-gray-100 border rounded-lg text-xs font-mono font-bold text-gray-600 flex-1">
                            {{ $prefix->kode_jenis }}
                        </code>
                        <span class="text-xs text-gray-400 whitespace-nowrap">Tidak dapat diubah</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Prefix / Awalan Nomor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="prefix" id="prefix"
                        value="{{ old('prefix', $prefix->prefix) }}"
                        maxlength="20"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono @error('prefix') border-red-400 @enderror"
                        oninput="updatePreview()" required>
                    @error('prefix')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Nama Lengkap Dokumen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_jenis"
                        value="{{ old('nama_jenis', $prefix->nama_jenis) }}"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_jenis') border-red-400 @enderror"
                        required>
                    @error('nama_jenis')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan', $prefix->keterangan) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Konfigurasi Format ────────────────────────────────────────── --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">Konfigurasi Format</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Format Tahun <span class="text-red-500">*</span></label>
                        <select name="format_tahun" id="format_tahun"
                            class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="updatePreview()" required>
                            <option value="YYYY" {{ old('format_tahun', $prefix->format_tahun) === 'YYYY' ? 'selected' : '' }}>YYYY (2024)</option>
                            <option value="YY" {{ old('format_tahun', $prefix->format_tahun) === 'YY' ? 'selected' : '' }}>YY (24)</option>
                            <option value="none" {{ old('format_tahun', $prefix->format_tahun) === 'none' ? 'selected' : '' }}>Tidak ada</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Format Bulan <span class="text-red-500">*</span></label>
                        <select name="format_bulan" id="format_bulan"
                            class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="updatePreview()" required>
                            <option value="MM" {{ old('format_bulan', $prefix->format_bulan) === 'MM' ? 'selected' : '' }}>MM (01)</option>
                            <option value="none" {{ old('format_bulan', $prefix->format_bulan) === 'none' ? 'selected' : '' }}>Tidak ada</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Pemisah <span class="text-red-500">*</span></label>
                        <select name="separator" id="separator"
                            class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                            onchange="updatePreview()" required>
                            <option value="/" {{ old('separator', $prefix->separator) === '/' ? 'selected' : '' }}>/ (Slash)</option>
                            <option value="-" {{ old('separator', $prefix->separator) === '-' ? 'selected' : '' }}>- (Dash)</option>
                            <option value="." {{ old('separator', $prefix->separator) === '.' ? 'selected' : '' }}>. (Titik)</option>
                            <option value="" {{ old('separator', $prefix->separator) === '' ? 'selected' : '' }}>(Tanpa pemisah)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Panjang Urutan <span class="text-red-500">*</span></label>
                        <select name="panjang_urutan" id="panjang_urutan"
                            class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                            onchange="updatePreview()" required>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ old('panjang_urutan', $prefix->panjang_urutan) == $i ? 'selected' : '' }}>
                                    {{ $i }} digit ({{ str_pad('1', $i, '0', STR_PAD_LEFT) }})
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 text-center">
                    <p class="text-xs text-blue-600 font-medium mb-1">Preview Nomor Berikutnya</p>
                    <code id="previewNomor"
                        class="text-2xl font-bold font-mono text-blue-800 tracking-widest">
                        ...
                    </code>
                    <p class="text-xs text-blue-400 mt-1">
                        Nomor terakhir saat ini: <strong>{{ number_format($prefix->nomor_terakhir) }}</strong>
                        — berikutnya: <strong>{{ $prefix->nomor_terakhir + 1 }}</strong>
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Aturan Reset ─────────────────────────────────────────────────── --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">Aturan Reset Nomor Urut</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="reset_per" value="bulan" class="sr-only peer"
                            {{ old('reset_per', $prefix->reset_per) === 'bulan' ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl p-3 transition-all text-center
                            border-gray-200 hover:border-orange-300
                            peer-checked:border-orange-500 peer-checked:bg-orange-50">
                            <div class="text-xl mb-1">📅</div>
                            <p class="text-xs font-bold text-gray-800">Reset per Bulan</p>
                            <p class="text-xs text-gray-500 mt-0.5">Nomor urut reset tiap awal bulan</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="reset_per" value="tahun" class="sr-only peer"
                            {{ old('reset_per', $prefix->reset_per) === 'tahun' ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl p-3 transition-all text-center
                            border-gray-200 hover:border-blue-300
                            peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <div class="text-xl mb-1">📆</div>
                            <p class="text-xs font-bold text-gray-800">Reset per Tahun</p>
                            <p class="text-xs text-gray-500 mt-0.5">Nomor urut reset tiap awal tahun</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="reset_per" value="never" class="sr-only peer"
                            {{ old('reset_per', $prefix->reset_per) === 'never' ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl p-3 transition-all text-center
                            border-gray-200 hover:border-gray-400
                            peer-checked:border-gray-600 peer-checked:bg-gray-50">
                            <div class="text-xl mb-1">♾️</div>
                            <p class="text-xs font-bold text-gray-800">Tidak Pernah Reset</p>
                            <p class="text-xs text-gray-500 mt-0.5">Nomor terus bertambah</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ── Status ───────────────────────────────────────────────────────── --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-800">Status Aktif</p>
                    <p class="text-xs text-gray-500 mt-0.5">Format non-aktif tidak dapat digunakan untuk generate nomor</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                        {{ old('is_active', $prefix->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300
                        rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white
                        after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white
                        after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5
                        after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
        </div>

        {{-- ── Actions ──────────────────────────────────────────────────────── --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('settings.prefix.show', $prefix) }}"
                class="inline-flex items-center px-4 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    function updatePreview() {
        const prefix  = document.getElementById('prefix').value || 'PREFIX';
        const tahun   = document.getElementById('format_tahun').value;
        const bulan   = document.getElementById('format_bulan').value;
        const sep     = document.getElementById('separator').value;
        const panjang = parseInt(document.getElementById('panjang_urutan').value) || 4;
        const next    = {{ $prefix->nomor_terakhir + 1 }};

        const now = new Date();
        const year = now.getFullYear();
        const y2   = String(year).slice(-2);
        const mon  = String(now.getMonth() + 1).padStart(2, '0');

        const segments = [prefix];
        if (tahun === 'YYYY') segments.push(String(year));
        else if (tahun === 'YY') segments.push(y2);
        if (bulan === 'MM') segments.push(mon);
        segments.push(String(next).padStart(panjang, '0'));

        document.getElementById('previewNomor').textContent = segments.join(sep);
    }

    document.getElementById('prefix').addEventListener('input', updatePreview);
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endsection