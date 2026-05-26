@props(['items' => []])

{{-- Breadcrumb navigation --}}
@php
    $breadcrumbs = array_merge(
        [['label' => 'Accueil', 'url' => route('home')]],
        $items
    );
@endphp

<nav aria-label="Fil d'Ariane" class="text-sm text-gray-500 mb-4">
    <ol class="flex flex-wrap items-center gap-2" itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach($breadcrumbs as $index => $crumb)
            <li class="flex items-center gap-2" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                @if($index > 0)
                    <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @endif
                
                @if($loop->last || empty($crumb['url']))
                    <span itemprop="name" class="text-gray-700 font-medium" aria-current="page">{{ $crumb['label'] }}</span>
                @else
                    <a href="{{ $crumb['url'] }}" itemprop="item" class="hover:text-[#2D6A4F] transition-colors">
                        <span itemprop="name">{{ $crumb['label'] }}</span>
                    </a>
                @endif
                
                <meta itemprop="position" content="{{ $index + 1 }}" />
            </li>
        @endforeach
    </ol>
</nav>

{{-- JSON-LD BreadcrumbList --}}
@if(count($breadcrumbs) > 1)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        @foreach($breadcrumbs as $index => $crumb)
        {
            "@type": "ListItem",
            "position": {{ $index + 1 }},
            "name": "{{ $crumb['label'] }}",
            @if(!$loop->last && !empty($crumb['url']))
            "item": "{{ $crumb['url'] }}"
            @endif
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif
