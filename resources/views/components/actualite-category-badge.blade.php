@props(['actualite', 'category' => null])

@php
    $label = \App\Support\ActualiteCategories::normalizeLabel($category ?? $actualite?->category);
@endphp

<span {{ $attributes->merge(['class' => 'badge '.\App\Support\ActualiteCategories::badgeClass($label)]) }}>
    {{ $label }}
</span>
