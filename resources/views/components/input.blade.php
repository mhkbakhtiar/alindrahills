@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'required' => false,
    'error' => null
])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent']) }}
    >
    
    @if($error)
        <p class="mt-1 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>
