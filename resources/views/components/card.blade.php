@props(['title' => null])

<div {{ $attributes->merge([
    'class' => 'bg-white border rounded shadow-sm'
]) }}>
    @if($title)
        <div class="px-4 py-2 border-b font-semibold text-sm">
            {{ $title }}
        </div>
    @endif

    <div class="p-4">
        {{ $slot }}
    </div>
</div>
