#!/bin/bash

echo "🎨 Creating Blade Components & Dashboard..."

# Pastikan folder ada
mkdir -p resources/views/components
mkdir -p resources/views/dashboard

# =============================================
# ALERT COMPONENT
# =============================================
cat > resources/views/components/alert.blade.php << 'EOF'
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
        <div class="text-sm">{{ $message }}</div>
        <button @click="show=false" class="ml-2 text-xs">✕</button>
    </div>
</div>
EOF

# =============================================
# BUTTON COMPONENT
# =============================================
cat > resources/views/components/button.blade.php << 'EOF'
@props(['type' => 'button', 'variant' => 'primary', 'href' => null])

@php
$variants = [
    'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
    'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-700',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white',
    'success' => 'bg-green-600 hover:bg-green-700 text-white',
];
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class'=>"px-3 py-2 rounded text-sm ".$variants[$variant]]) }}>
    {{ $slot }}
</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class'=>"px-3 py-2 rounded text-sm ".$variants[$variant]]) }}>
    {{ $slot }}
</button>
@endif
EOF

# =============================================
# CARD COMPONENT
# =============================================
cat > resources/views/components/card.blade.php << 'EOF'
@props(['title' => null])

<div class="bg-white border rounded shadow-sm">
    @if($title)
        <div class="px-4 py-2 border-b font-semibold text-sm">{{ $title }}</div>
    @endif
    <div class="p-4">
        {{ $slot }}
    </div>
</div>
EOF

# =============================================
# BADGE COMPONENT
# =============================================
cat > resources/views/components/badge.blade.php << 'EOF'
@props(['variant' => 'default'])

@php
$variants = [
    'default' => 'bg-gray-100 text-gray-700',
    'success' => 'bg-green-100 text-green-700',
    'danger' => 'bg-red-100 text-red-700',
    'warning' => 'bg-yellow-100 text-yellow-700',
    'info' => 'bg-blue-100 text-blue-700',
];
@endphp

<span class="px-2 py-1 rounded text-xs font-medium {{ $variants[$variant] }}">
    {{ $slot }}
</span>
EOF

# =============================================
# DASHBOARD VIEW
# =============================================
cat > resources/views/dashboard/index.blade.php << 'EOF'
@extends('layouts.app')

@section('title','Dashboard')

@section('content')
<div class="grid grid-cols-4 gap-4 mb-6">
    <x-card title="Total Material">
        <p class="text-2xl font-bold">{{ $stats['total_materials'] }}</p>
    </x-card>

    <x-card title="Pending Request">
        <p class="text-2xl font-bold">{{ $stats['pending_requests'] }}</p>
    </x-card>

    <x-card title="Kegiatan Aktif">
        <p class="text-2xl font-bold">{{ $stats['active_activities'] }}</p>
    </x-card>

    <x-card title="Low Stock">
        <p class="text-2xl font-bold text-red-600">{{ $stats['low_stock_items'] }}</p>
    </x-card>
</div>

<x-card title="Total Nilai Persediaan">
    <p class="text-3xl font-bold text-center">
        Rp {{ number_format($stockValue,0,',','.') }}
    </p>
</x-card>
@endsection
EOF

echo "✅ Components & Dashboard created successfully!"
