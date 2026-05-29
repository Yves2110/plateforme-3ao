@props(['class' => 'w-full h-40', 'size' => 'lg'])

@php
    $iconClass = $size === 'sm' ? 'w-10 h-10' : ($size === 'md' ? 'w-14 h-14' : 'w-16 h-16');
@endphp

<div {{ $attributes->merge(['class' => $class . ' bg-gradient-to-br from-[#F8F5F0] via-[#d4e8dc] to-[#b7dfc8] flex items-center justify-center']) }}>
    <x-icon name="graduation" :class="$iconClass . ' text-[#2D6A4F]/70'" />
</div>
