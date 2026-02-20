{{-- resources/views/settings/prefix/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Format Nomor')
@section('breadcrumb', 'Settings / Master Format Nomor / Tambah')

@section('content')
<div class="space-y-4 max-w-4xl flex flex-col mx-auto">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Tambah Format Nomor</h2>
            <p class="text-xs text-gray-500 mt-0.5">Buat format penomoran baru untuk dokumen sistem</p>
        </div>
        <a href="{{ route('settings.prefix.index') }}"
            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

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

    <form action="{{ route('settings.prefix.store') }}" method="POST" id="prefixForm">
        @csrf

        {{-- ── Identitas Dokumen ─────────────────────────────────────────── --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold flex-shrink-0">1</span>
                <h3 class="text-sm font-semibold text-gray-800">Identitas Dokumen</h3>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Kode Jenis <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode_jenis" id="kode_jenis"
                        value="{{ old('kode_jenis') }}"
                        placeholder="PR / JU / TKG / MTR"
                        maxlength="50"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono uppercase @error('kode_jenis') border-red-400 @enderror"
                        required>
                    <p class="text-xs text-gray-400 mt-1">Kode unik, huruf kapital, tidak bisa diubah setelah disimpan</p>
                    @error('kode_jenis')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Prefix / Awalan Nomor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="prefix" id="prefix"
                        value="{{ old('prefix') }}"
                        placeholder="PR / JU / TKG"
                        maxlength="20"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono @error('prefix') border-red-400 @enderror"
                        oninput="updatePreview()" required>
                    <p class="text-xs text-gray-400 mt-1">Teks yang muncul di awal nomor dokumen</p>
                    @error('prefix')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Nama Lengkap Dokumen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_jenis"
                        value="{{ old('nama_jenis') }}"
                        placeholder="Nomor Surat Pengajuan Pembelian Material"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_jenis') border-red-400 @enderror"
                        required>
                    @error('nama_jenis')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2"
                        placeholder="Keterangan penggunaan format nomor ini (opsional)"
                        class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Konfigurasi Format ─────────────────────────────────────────── --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold flex-shrink-0">2</span>
                <h3 class="text-sm font-semibold text-gray-800">Konfigurasi Format</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            Format Tahun <span class="text-red-500">*</span>
                        </label>
                        <select name="format_tahun" id="format_tahun"
                            class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="updatePreview()" required>
                            <option value="YYYY" {{ old('format_tahun', 'YYYY') === 'YYYY' ? 'selected' : '' }}>YYYY (2024)</option>
                            <option value="YY" {{ old('format_tahun') === 'YY' ? 'selected' : '' }}>YY (24)</option>
                            <option value="none" {{ old('format_tahun') === 'none' ? 'selected' : '' }}>Tidak ada</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            Format Bulan <span class="text-red-500">*</span>
                        </label>
                        <select name="format_bulan" id="format_bulan"
                            class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="updatePreview()" required>
                            <option value="MM" {{ old('format_bulan', 'MM') === 'MM' ? 'selected' : '' }}>MM (01)</option>
                            <option value="none" {{ old('format_bulan') === 'none' ? 'selected' : '' }}>Tidak ada</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            Pemisah (Separator) <span class="text-red-500">*</span>
                        </label>
                        <select name="separator" id="separator"
                            class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                            onchange="updatePreview()" required>
                            <option value="/" {{ old('separator', '/') === '/' ? 'selected' : '' }}>/ (Slash)</option>
                            <option value="-" {{ old('separator') === '-' ? 'selected' : '' }}>- (Dash)</option>
                            <option value="." {{ old('separator') === '.' ? 'selected' : '' }}>. (Titik)</option>
                            <option value="" {{ old('separator') === '' ? 'selected' : '' }}>(Tanpa pemisah)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            Panjang Nomor Urut <span class="text-red-500">*</span>
                        </label>
                        <select name="panjang_urutan" id="panjang_urutan"
                            class="w-full px-3 py-2 text-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                            onchange="updatePreview()" required>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ old('panjang_urutan', 4) == $i ? 'selected' : '' }}>
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
                    <p class="text-xs text-blue-400 mt-1">Nomor pertama yang akan digenerate</p>
                </div>
            </div>
        </div>

        {{-- ── Aturan Reset ─────────────────────────────────────────────────── --}}
        <div class="bg-white border rounded-lg overflow-hidden mb-4">
            <div class="bg-gray-50 px-4 py-3 border-b flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold flex-shrink-0">3</span>
                <h3 class="text-sm font-semibold text-gray-800">Aturan Reset Nomor Urut</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="reset_per" value="bulan" class="sr-only peer"
                            {{ old('reset_per', 'bulan') === 'bulan' ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl p-3 transition-all text-center
                            border-gray-200 hover:border-orange-300
                            peer-checked:border-orange-500 peer-checked:bg-orange-50">
                            <div class="text-xl mb-1">📅</div>
                            <p class="text-xs font-bold text-gray-800">Reset per Bulan</p>
                            <p class="text-xs text-gray-500 mt-0.5">Nomor urut kembali ke 0001 setiap awal bulan</p>
                            <p class="text-xs text-orange-600 mt-1 font-mono">PR/2024/01/0001 → PR/2024/02/0001</p>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="reset_per" value="tahun" class="sr-only peer"
                            {{ old('reset_per') === 'tahun' ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl p-3 transition-all text-center
                            border-gray-200 hover:border-blue-300
                            peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <div class="text-xl mb-1">📆</div>
                            <p class="text-xs font-bold text-gray-800">Reset per Tahun</p>
                            <p class="text-xs text-gray-500 mt-0.5">Nomor urut kembali ke 0001 setiap awal tahun</p>
                            <p class="text-xs text-blue-600 mt-1 font-mono">KEG/2024/0001 → KEG/2025/0001</p>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="reset_per" value="never" class="sr-only peer"
                            {{ old('reset_per') === 'never' ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl p-3 transition-all text-center
                            border-gray-200 hover:border-gray-400
                            peer-checked:border-gray-600 peer-checked:bg-gray-50">
                            <div class="text-xl mb-1">♾️</div>
                            <p class="text-xs font-bold text-gray-800">Tidak Pernah Reset</p>
                            <p class="text-xs text-gray-500 mt-0.5">Nomor terus bertambah tidak pernah diulang</p>
                            <p class="text-xs text-gray-600 mt-1 font-mono">TKG-0001 → TKG-0002 → ...</p>
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
                    <p class="text-xs text-gray-500 mt-0.5">Format nomor yang non-aktif tidak dapat digunakan untuk generate nomor</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                        {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
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
            <a href="{{ route('settings.prefix.index') }}"
                class="inline-flex items-center px-4 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Format
            </button>
        </div>

    </form>
</div>

<script>
    // Auto uppercase kode_jenis
    document.getElementById('kode_jenis').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // ── Preview Generator ─────────────────────────────────────────────────────
    function updatePreview() {
        const prefix    = document.getElementById('prefix').value || 'PREFIX';
        const tahun     = document.getElementById('format_tahun').value;
        const bulan     = document.getElementById('format_bulan').value;
        const sep       = document.getElementById('separator').value;
        const panjang   = parseInt(document.getElementById('panjang_urutan').value) || 4;

        const now  = new Date();
        const year = now.getFullYear();
        const y2   = String(year).slice(-2);
        const mon  = String(now.getMonth() + 1).padStart(2, '0');

        const segments = [prefix];
        if (tahun === 'YYYY') segments.push(String(year));
        else if (tahun === 'YY') segments.push(y2);
        if (bulan === 'MM') segments.push(mon);
        segments.push('1'.padStart(panjang, '0'));

        document.getElementById('previewNomor').textContent = segments.join(sep);
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    document.getElementById('prefix').addEventListener('input', updatePreview);
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endsection