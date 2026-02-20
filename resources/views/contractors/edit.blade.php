@extends('layouts.app')

@section('title', 'Edit Contractor')
@section('breadcrumb', 'Master / Contractor / Edit')

@section('content')
<div class="max-w-3xl mx-auto">
    <x-card title="Edit Contractor">
        <form method="POST" action="{{ route('contractors.update', $contractor) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <x-input label="Nama Contractor" name="contractor_name" :value="$contractor->contractor_name" required />
                <x-input label="PIC" name="pic_name" :value="$contractor->pic_name" />
                <x-input label="Telepon" name="phone" :value="$contractor->phone" />
            </div>

            <div class="mb-4">
                <label class="text-xs font-semibold mb-1 block">Alamat</label>
                <textarea name="address" rows="2" class="w-full px-3 py-2 text-sm border rounded">{{ $contractor->address }}</textarea>
            </div>

            <div class="mb-4">
                <label class="text-xs font-semibold mb-1 block">Status</label>
                <select name="status" class="w-full px-3 py-2 text-sm border rounded">
                    <option value="active" {{ $contractor->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $contractor->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <x-button href="{{ route('contractors.index') }}" variant="secondary">Batal</x-button>
                <x-button type="submit" variant="primary">Update</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
