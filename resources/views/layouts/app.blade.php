<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '') | Alindra Hills Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full" x-data="{ sidebarOpen: true }">
    <div class="flex h-full">
        @include('layouts.sidebar')
        
        <div class="flex-1 flex flex-col min-w-0">
            @include('layouts.header')
            
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4">
                @if(session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif
                
                @if(session('error'))
                    <x-alert type="error" :message="session('error')" />
                @endif

                @if($errors->any())
                    <x-alert type="error" :message="$errors->first()" />
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
