<aside 
    x-show="sidebarOpen" 
    class="w-56 bg-gray-900 text-gray-300 flex-shrink-0 transition-all duration-300"
    x-transition:enter="transform transition ease-in-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
>
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex flex-col gap-2 items-center justify-center h-22 border-b border-gray-800 p-4">
            <img src="{{asset('assets/images/logo.png')}}" alt="" srcset="" class="h-10 w-auto">
            <h1 class="text-sm font-bold text-white">Logistics System</h1>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-2 text-xs">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 hover:bg-gray-800 {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : '' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- Material Management -->
            <div x-data="{ open: {{ request()->is('materials/*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Material Management
                    </span>
                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-90' : ''" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" class="pl-8 space-y-1">
                    <a href="{{ route('materials.index') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('materials.*') ? 'text-white' : '' }}">Master Material</a>
                    <a href="{{ route('purchase-requests.index') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('purchase-requests.*') ? 'text-white' : '' }}">Pengajuan Pembelian</a>
                    <a href="{{ route('goods-receipts.index') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('goods-receipts.*') ? 'text-white' : '' }}">Penerimaan Barang</a>
                </div>
            </div>

            <!-- Warehouse Management -->
            <div x-data="{ open: {{ request()->is('warehouse/*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Warehouse
                    </span>
                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-90' : ''" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" class="pl-8 space-y-1">
                    <a href="{{ route('warehouses.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Gudang</a>
                    <a href="{{ route('stocks.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Stok Material</a>
                    <a href="{{ route('batches.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Batch Tracking</a>
                    <a href="{{ route('mutations.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Mutasi Stok</a>
                </div>
            </div>

            <!-- Project Management -->
            <div x-data="{ open: {{ request()->is('projects/*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Project
                    </span>
                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-90' : ''" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" class="pl-8 space-y-1">
                    <a href="{{ route('contractors.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Contractor</a>
                    <a href="{{ route('locations.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Lokasi Proyek</a>
                    <a href="{{ route('activities.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Kegiatan</a>
                    <a href="{{ route('material-usages.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Penggunaan Material</a>
                </div>
            </div>

            <!-- Payroll Management -->
            <div x-data="{ open: {{ request()->is('payroll/*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Payroll
                    </span>
                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-90' : ''" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" class="pl-8 space-y-1">
                    <a href="{{ route('workers.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Data Tukang</a>
                    <a href="{{ route('attendances.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Absensi</a>
                    <a href="{{ route('payroll-requests.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Pengajuan Gaji</a>
                </div>
            </div>

            <!-- Reports -->
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Master Data
                    </span>
                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-90' : ''" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" class="pl-8 space-y-1">
                    <a href="{{ route('master.pembeli.index') }}" 
                    class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('master.pembeli.*') ? 'bg-gray-800' : '' }}">
                        Pembeli
                    </a>
                    <a href="{{ route('master.kavling-pembeli.index') }}" 
                    class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('master.kavling-pembeli.*') ? 'bg-gray-800' : '' }}">
                        Kavling & Pembeli
                    </a>
                </div>
            </div>
            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Reports
                    </span>
                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-90' : ''" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" class="pl-8 space-y-1">
                    <a href="{{ route('reports.stock.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Laporan Stok</a>
                    <a href="{{ route('reports.activities.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Laporan Kegiatan</a>
                    <a href="{{ route('reports.payroll.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Laporan Payroll</a>
                </div>
            </div>
            
            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Keuangan
                    </span>
                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-90' : ''" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" class="pl-8 space-y-1">
                    <a href="{{ route('accounting.tahun-anggaran.index') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('accounting.tahun-anggaran.*') ? 'text-white' : '' }}">Tahun Anggaran</a>
                    <a href="{{ route('purchases.index') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('purchases.*') ? 'text-white' : '' }}">Pembelian Material</a>
                    <a href="{{ route('accounting.perkiraan.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Perkiraan</a>
                    <a href="{{ route('accounting.jurnal.index') }}" class="block px-4 py-1.5 hover:bg-gray-800">Jurnal</a>
                </div>
            </div>
            
            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan Keuangan
                    </span>
                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-90' : ''" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" class="pl-8 space-y-1">
                    <a href="{{ route('accounting.laporan.jurnal-umum') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('accounting.laporan.jurnal-umum') ? 'text-white' : '' }}">Jurnal Umum</a>
                    <a href="{{ route('accounting.laporan.buku-besar') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('accounting.laporan.buku-besar') ? 'text-white' : '' }}">Buku Besar</a>
                    <a href="{{ route('accounting.laporan.calk') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('accounting.laporan.calk') ? 'text-white' : '' }}">Catatan Atas Laporan Keuangan (CALK)</a>
                    <a href="{{ route('accounting.laporan.buku-pembantu-kavling') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('accounting.laporan.buku-pembantu-kavling') ? 'text-white' : '' }}">Buku Pembantu Kavling</a>
                    <a href="{{ route('accounting.laporan.neraca') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('accounting.laporan.neraca') ? 'text-white' : '' }}">Neraca</a>
                    <a href="{{ route('accounting.laporan.laba-rugi') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('accounting.laporan.laba-rugi') ? 'text-white' : '' }}">Laba Rugi</a>
                </div>
            </div>
            @endif

            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M8.832 20h6.673M12 4.354a4 4 0 110 5.292M12 4.354a4 4 0 100 5.292M12 4.354v1.292m0 4.708v1.292m-6.364-6.364l-.916.916m5.656 5.656l-.916.916m0-5.656l.916.916m-5.656 5.656l-.916-.916" />
                        </svg>
                        Pengaturan
                    </span>
                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-90' : ''" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" class="pl-8 space-y-1">
                    <a href="{{ route('settings.prefix.index') }}" class="block px-4 py-1.5 hover:bg-gray-800 {{ request()->routeIs('settings.prefix.*') ? 'text-white' : '' }}">Master Prefix Nomor</a>
                </div>
            </div>
        </nav>

        <!-- User Info -->
        <div class="border-t border-gray-800 p-3">
            <div class="flex items-center text-xs">
                <div class="flex-1">
                    <p class="font-medium text-white">{{ auth()->user()->full_name }}</p>
                    <p class="text-gray-400">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
