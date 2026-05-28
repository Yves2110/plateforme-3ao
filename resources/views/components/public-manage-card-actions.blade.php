@props([
    'item',
    'toggleRoute',
    'publishedKey' => 'is_published',
    'showRoute' => null,
    'editRoute' => null,
])

@php
    $isPublished = (bool) data_get($item, $publishedKey);
@endphp

<div class="absolute top-2 right-2 z-20 flex flex-col items-end gap-1" @click.stop>
    @if(! $isPublished)
        <span class="px-2 py-0.5 text-[10px] font-bold uppercase bg-amber-500 text-white rounded-full shadow">Brouillon</span>
    @endif
    <div class="flex gap-1">
        @if($editRoute)
            <a href="{{ $editRoute }}"
               class="px-2 py-1 text-[10px] font-semibold bg-white/95 text-[#1A1A2E] rounded-md shadow hover:bg-white"
               title="Modifier">✎</a>
        @endif
        <form method="POST" action="{{ $toggleRoute }}">
            @csrf
            <button type="submit"
                    class="px-2 py-1 text-[10px] font-semibold rounded-md shadow {{ $isPublished ? 'bg-amber-600 text-white hover:bg-amber-500' : 'bg-[#2D6A4F] text-white hover:bg-[#40916C]' }}"
                    title="{{ $isPublished ? 'Dépublier' : 'Publier' }}">
                {{ $isPublished ? '−' : '✓' }}
            </button>
        </form>
    </div>
</div>
