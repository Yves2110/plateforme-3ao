@props([
    'selected' => [],
    'mode' => 'public',
    'search' => null,
])

@php
    $selected = is_array($selected) ? $selected : [];
    $categories = \App\Support\ActualiteCategories::all();
    $extra = $mode === 'admin' && $search ? ['search' => $search] : [];
    $allUrl = $mode === 'admin'
        ? \App\Support\ActualiteCategories::adminFilterUrl([], null, $extra)
        : \App\Support\ActualiteCategories::filterUrl([], null, $extra);
@endphp

<div class="mb-8">
    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
        {{ __('actualites.filter_categories') }}
    </p>
    <div class="flex flex-wrap gap-2">
        <a href="{{ $allUrl }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors border
                  {{ $selected === [] ? 'bg-[#2D6A4F] text-white border-[#2D6A4F]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#52B788] hover:text-[#2D6A4F]' }}">
            {{ __('actualites.filter_all') }}
        </a>
        @foreach($categories as $label => $meta)
            @php
                $isActive = in_array($label, $selected, true);
                $url = $mode === 'admin'
                    ? \App\Support\ActualiteCategories::adminFilterUrl($selected, $label, $extra)
                    : \App\Support\ActualiteCategories::filterUrl($selected, $label, $extra);
            @endphp
            <a href="{{ $url }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition-all border
                      {{ $isActive
                          ? 'bg-[#2D6A4F] text-white border-[#2D6A4F] ring-2 ring-[#52B788]/40'
                          : 'bg-white text-gray-600 border-gray-200 hover:border-[#52B788]' }}">
                <span class="badge badge-{{ $meta['badge'] }} {{ $isActive ? '!bg-white/20 !text-white' : '' }}">{{ $label }}</span>
            </a>
        @endforeach
    </div>
    @if(count($selected) > 1)
        <p class="text-xs text-gray-400 mt-2">{{ __('actualites.filter_multi_hint', ['count' => count($selected)]) }}</p>
    @endif
</div>
