@props([
    'src' => null,
    'alt' => '',
    'mode' => 'cover',
    'heightClass' => 'h-48',
    'roundedClass' => '',
])

@php
    $imageUrl = $src ?: \App\Support\BrandAssets::logoUrl();
    $isLogo = $mode === 'logo';
@endphp

<div {{ $attributes->merge(['class' => "relative overflow-hidden bg-gradient-to-br from-[#2D6A4F] to-[#40916C] {$heightClass} {$roundedClass}"]) }}>
    @if($isLogo)
        <div class="absolute inset-0 flex items-center justify-center p-6">
            <img src="{{ $imageUrl }}" alt="{{ $alt ?: \App\Support\BrandAssets::logoAlt() }}"
                 class="max-h-full max-w-[min(100%,220px)] w-auto h-auto object-contain drop-shadow-lg"
                 loading="lazy">
        </div>
    @else
        <img src="{{ $imageUrl }}" alt="{{ $alt }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
             loading="lazy">
    @endif
    {{ $slot }}
</div>
