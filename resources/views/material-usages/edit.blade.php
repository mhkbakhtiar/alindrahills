@extends('layouts.app')

@section('title', 'Edit Pengeluaran Material')
@section('breadcrumb', 'Project / Pengeluaran Material / Edit')

@section('content')
<div class="max-w-4xl mx-auto">
    <x-card>
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-yellow-800">Edit Tidak Direkomendasikan</h3>
                    <p class="text-xs text-yellow-700 mt-1">
                        Pengeluaran material yang sudah dicatat tidak dapat diubah karena sudah mempengaruhi stok dan batch tracking. 
                        Jika ada kesalahan, disarankan untuk:
                    </p>
                    <ul class="list-disc list-inside text-xs text-yellow-700 mt-2 space-y-1">
                        <li>Membuat pengeluaran baru dengan data yang benar</li>
                        <li>Atau hubungi administrator untuk adjustment manual</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-4 flex justify-center">
            <x-button variant="secondary" href="{{ route('material-usages.show', $materialUsage) }}">
                Kembali ke Detail
            </x-button>
        </div>
    </x-card>
</div>
@endsection