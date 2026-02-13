@props(['type' => 'button', 'variant' => 'primary', 'href' => null])

@php
$variants = [
    'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
    'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-700',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white',
    'success' => 'bg-green-600 hover:bg-green-700 text-white',
    'info' => 'bg-cyan-600 hover:bg-cyan-700 text-white',
    'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white',
];
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class'=>"px-3 py-2 rounded text-sm flex items-center ".$variants[$variant]]) }}>
    {{ $slot }}
</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class'=>"px-3 py-2 rounded text-sm flex items-center ".$variants[$variant]]) }}>
    {{ $slot }}
</button>
@endif