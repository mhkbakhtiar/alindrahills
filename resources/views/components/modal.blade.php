@props(['id', 'title', 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'max-w-md',
        'md' => 'max-w-2xl',
        'lg' => 'max-w-4xl',
        'xl' => 'max-w-6xl',
    ];
@endphp

<div 
    x-data="{ show: false }"
    @open-modal-{{ $id }}.window="show = true"
    @close-modal.window="show = false"
    @keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <!-- Backdrop -->
    <div 
        class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
        @click="show = false"
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div 
            class="relative bg-white rounded-lg shadow-xl w-full {{ $sizes[$size] }}"
            @click.away="show = false"
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
        >
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
