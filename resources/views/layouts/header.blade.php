<header class="h-14 bg-white border-b border-gray-200 flex items-center px-4">
    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 mr-4">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <div class="flex-1 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            @yield('breadcrumb', 'Dashboard')
        </div>

        <div class="flex items-center space-x-3 text-xs">
            <span class="text-gray-600">{{ now()->format('d M Y, H:i') }}</span>
            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">{{ auth()->user()->full_name }}</span>
        </div>
    </div>
</header>
