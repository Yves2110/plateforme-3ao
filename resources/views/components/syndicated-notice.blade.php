@props(['actualite', 'size' => 'sm'])

@if($actualite->isSyndicated())
    <span {{ $attributes->merge([
        'class' => 'inline-flex items-center gap-1 font-medium rounded-full bg-amber-50 text-amber-800 border border-amber-200 '
            . ($size === 'xs' ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-0.5 text-xs'),
    ]) }}>
        <svg class="{{ $size === 'xs' ? 'w-3 h-3' : 'w-3.5 h-3.5' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
        </svg>
        {{ __('actualites.syndicated_from', ['source' => $actualite->syndicated_source]) }}
    </span>
@endif
