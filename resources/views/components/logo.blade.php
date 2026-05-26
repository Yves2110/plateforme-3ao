@props([
    'size' => 'md',
    'showSubtitle' => false,
    'href' => null,
])

@php
    $heights = [
        'xs' => 'h-7',
        'sm' => 'h-9',
        'md' => 'h-10',
        'lg' => 'h-14',
        'xl' => 'h-20',
    ];
    $imgClass = ($heights[$size] ?? $heights['md']) . ' w-auto object-contain';
    $logoUrl = asset(config('brand.logo', 'images/logo-3ao.jpeg'));
    $alt = config('brand.logo_alt');
    $wrapperTag = $href ? 'a' : 'div';
@endphp

<{{ $wrapperTag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 shrink-0']) }}
>
    <img src="{{ $logoUrl }}" alt="{{ $alt }}" class="{{ $imgClass }} rounded-md" width="120" height="48" loading="eager">
    @if($showSubtitle)
        <div class="hidden sm:block min-w-0">
            <div class="font-display font-bold text-[#2D6A4F] text-base leading-tight">{{ config('brand.name') }}</div>
            <div class="text-[10px] text-gray-500 leading-tight uppercase tracking-wider">{{ __('nav.logo_subtitle') }}</div>
        </div>
    @endif
</{{ $wrapperTag }}>
