@props(['variant' => 'default'])

@php
$variants = [
    'default' => 'bg-gray-100 text-gray-700',
    'primary' => 'bg-blue-100 text-blue-700',
    'secondary' => 'bg-gray-200 text-gray-600',
    'success' => 'bg-green-100 text-green-700',
    'danger' => 'bg-red-100 text-red-700',
    'warning' => 'bg-yellow-100 text-yellow-700',
    'info' => 'bg-blue-100 text-blue-700',
];
@endphp

<span class="px-2 py-1 rounded text-xs font-medium {{ $variants[$variant] }}">
    {{ $slot }}
</span>