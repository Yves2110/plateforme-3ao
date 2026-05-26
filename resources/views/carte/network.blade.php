<x-app-layout>
    <x-slot name="title">{{ __('carte.network_title') }}</x-slot>
    <x-slot name="description">{{ __('carte.network_description') }}</x-slot>

    @push('styles')
    <style>
        #network-graph { width: 100%; min-height: 600px; height: 600px; border-radius: 1rem; overflow: hidden; background: #F8F5F0; }
        #network-graph svg { display: block; }
        .node circle { stroke: #fff; stroke-width: 2px; cursor: pointer; transition: r 0.2s; }
        .node text { font-size: 10px; font-family: Inter, sans-serif; fill: #1A1A2E; pointer-events: none; }
        .link { stroke: #52B788; stroke-opacity: 0.45; }
        .tooltip-graph { position: absolute; background: #1A1A2E; color: white; padding: 8px 12px; border-radius: 10px; font-size: 12px; pointer-events: none; white-space: nowrap; z-index: 20; }
    </style>
    @endpush

    <div class="max-w-6xl mx-auto px-4 py-10">
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-display font-bold text-[#1A1A2E] mb-1">🕸 {{ __('carte.network_title') }}</h1>
                <p class="text-sm text-gray-500">{{ __('carte.network_description') }}</p>
            </div>
            <div class="inline-flex bg-gray-100 rounded-xl p-1">
                <a href="{{ route('carte.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 hover:text-[#2D6A4F] transition-colors">
                    🗺 {{ __('carte.map') }}
                </a>
                <span class="px-3 py-1.5 rounded-lg text-sm font-semibold bg-white text-[#2D6A4F] shadow-sm">🕸 {{ __('carte.network') }}</span>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 mb-4 text-xs">
            @foreach(['ONG' => '#2D6A4F', 'Institution' => '#D4A017', 'Réseau' => '#40916C', 'Entreprise' => '#52B788', 'Gouvernement' => '#1A1A2E'] as $type => $color)
            <span class="flex items-center gap-1.5 px-2.5 py-1 bg-white rounded-full border border-gray-100 shadow-sm">
                <span class="w-3 h-3 rounded-full" style="background:{{ $color }}"></span>
                {{ $type }}
            </span>
            @endforeach
        </div>

        <div class="relative bg-white rounded-2xl border border-gray-100 shadow-sm min-h-[600px]">
            <div id="network-graph" aria-label="{{ __('carte.network_title') }}"></div>
            <div id="network-empty" class="hidden absolute inset-0 flex items-center justify-center text-sm text-gray-500 p-8 text-center rounded-2xl bg-[#F8F5F0]">
                {{ __('carte.network_empty') }}
            </div>
            <div class="tooltip-graph hidden" id="graph-tooltip"></div>
        </div>

        <p class="text-xs text-gray-400 mt-3 text-center">{{ __('carte.actors_links', ['nodes' => $nodeCount, 'links' => $linkCount]) }}</p>
    </div>

    <script type="application/json" id="graph-nodes-data">{!! $nodesJson !!}</script>
    <script type="application/json" id="graph-links-data">{!! $linksJson !!}</script>

    @push('scripts')
    <script src="{{ asset('js/vendor/d3.min.js') }}"></script>
    <script src="{{ asset('js/network-graph.js') }}"></script>
    @endpush
</x-app-layout>
