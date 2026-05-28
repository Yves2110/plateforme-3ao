@props([
    'label',
    'permissions' => [],
    'createRoute' => null,
    'listRoute' => null,
    'item' => null,
    'editRoute' => null,
    'toggleRoute' => null,
    'publishedKey' => 'is_published',
])

@php
    use App\Support\PublicContentGate;
    $canManage = PublicContentGate::can($permissions);
    $isPublished = $item ? (bool) data_get($item, $publishedKey) : null;
@endphp

@if($canManage)
    <div {{ $attributes->merge(['class' => 'mb-6 bg-[#1A1A2E] text-white rounded-2xl px-4 py-3 flex flex-wrap items-center justify-between gap-3 shadow-sm']) }}>
        <div class="flex items-center gap-2 text-sm flex-wrap">
            <svg class="w-5 h-5 text-[#F4C842] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="font-semibold">Gestion — {{ $label }}</span>
            @if($item && $isPublished === false)
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase bg-amber-500/30 text-amber-200 rounded-full">Non publié</span>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($createRoute)
                <a href="{{ $createRoute }}"
                   class="px-3 py-1.5 text-xs font-semibold bg-[#2D6A4F] hover:bg-[#40916C] rounded-lg transition-colors">
                    + Ajouter
                </a>
            @endif
            @if($listRoute)
                <a href="{{ $listRoute }}"
                   class="px-3 py-1.5 text-xs font-semibold bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                    Liste admin
                </a>
            @endif
            @if($item && $editRoute)
                <a href="{{ $editRoute }}"
                   class="px-3 py-1.5 text-xs font-semibold bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                    Modifier
                </a>
            @endif
            @if($item && $toggleRoute)
                <form method="POST" action="{{ $toggleRoute }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $isPublished ? 'bg-amber-600 hover:bg-amber-500' : 'bg-[#52B788] hover:bg-[#40916C]' }}">
                        {{ $isPublished ? 'Dépublier' : 'Publier' }}
                    </button>
                </form>
            @endif
        </div>
    </div>
@endif
