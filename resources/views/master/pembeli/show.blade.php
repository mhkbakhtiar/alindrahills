<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pembeli
            </h2>
            <div class="flex space-x-2">
                <x-button variant="warning" href="{{ route('pembeli.edit', $pembeli) }}">
                    Edit
                </x-button>
                <x-button variant="secondary" href="{{ route('pembeli.index') }}">
                    Kembali
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Informasi Pembeli</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nama</label>
                            <p class="text-gray-900">{{ $pembeli->nama }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900">{{ $pembeli->email ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Telepon</label>
                            <p class="text-gray-900">{{ $pembeli->telepon ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">No. Identitas</label>
                            <p class="text-gray-900">{{ $pembeli->no_identitas ?? '-' }}</p>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Alamat</label>
                            <p class="text-gray-900">{{ $pembeli->alamat ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <x-badge variant="{{ $pembeli->is_active ? 'success' : 'danger' }}">
                                {{ $pembeli->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </x-badge>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Daftar Kavling</h3>

                    @if($pembeli->kavlings->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kavling</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Blok</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Booking</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Akad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Jual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pembeli->kavlings as $kavling)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $kavling->kavling }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $kavling->blok }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $kavling->pivot->tanggal_booking ? $kavling->pivot->tanggal_booking->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $kavling->pivot->tanggal_akad ? $kavling->pivot->tanggal_akad->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $kavling->pivot->harga_jual ? 'Rp ' . number_format($kavling->pivot->harga_jual, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $badgeVariant = match($kavling->pivot->status) {
                                                    'booking' => 'info',
                                                    'akad' => 'warning',
                                                    'lunas' => 'success',
                                                    'batal' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <x-badge variant="{{ $badgeVariant }}">
                                                {{ ucfirst($kavling->pivot->status) }}
                                            </x-badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 text-center py-4">Belum ada kavling terkait</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>