@props(['type' => 'info', 'message'])

@php
$classes = [
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'error' => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'info' => 'bg-blue-50 border-blue-200 text-blue-800',
];
@endphp

<div class="mb-4 p-3 border rounded {{ $classes[$type] }}" x-data="{show:true}" x-show="show">
    <div class="flex justify-between items-start">
        <div class="text-sm">{!! $message !!}</div>
        <button @click="show=false" class="ml-2 text-xs">✕</button>
    </div>
</div>
