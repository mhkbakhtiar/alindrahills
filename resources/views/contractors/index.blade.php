@extends('layouts.app')

@section('title', 'Contractor')
@section('breadcrumb', 'Master / Contractor')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Data Contractor</h2>
        <x-button href="{{ route('contractors.create') }}" variant="primary">
            + Tambah Contractor
        </x-button>
    </div>

    <x-card>
        <form method="GET" class="flex gap-2 mb-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari contractor..."
                   class="flex-1 px-3 py-2 text-sm border rounded">
            <x-button type="submit" variant="secondary">Cari</x-button>
        </form>

        <table class="min-w-full text-xs border">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Kode</th>
                    <th class="px-3 py-2 text-left">Nama</th>
                    <th class="px-3 py-2 text-left">PIC</th>
                    <th class="px-3 py-2 text-left">Telepon</th>
                    <th class="px-3 py-2 text-center">Status</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contractors as $c)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-3 py-2">{{ $c->contractor_code }}</td>
                        <td class="px-3 py-2">{{ $c->contractor_name }}</td>
                        <td class="px-3 py-2">{{ $c->pic_name ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $c->phone ?? '-' }}</td>
                        <td class="px-3 py-2 text-center">
                            <x-badge :variant="$c->status === 'active' ? 'success' : 'danger'">
                                {{ ucfirst($c->status) }}
                            </x-badge>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('contractors.edit', $c) }}" class="text-blue-600 mr-2">Edit</a>
                            <form action="{{ route('contractors.destroy', $c) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Hapus contractor?')" class="text-red-600">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">
                            Data kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $contractors->links() }}
        </div>
    </x-card>
</div>
@endsection
