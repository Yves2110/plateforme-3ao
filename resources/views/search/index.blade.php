<x-app-layout>
    <x-slot name="title">Recherche — {{ $q }}</x-slot>

    <div class="bg-[#F8F5F0] border-b border-gray-200 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-2xl font-bold text-[#1A1A2E] mb-4">
                Résultats pour <span class="text-[#2D6A4F]">"{{ $q }}"</span>
            </h1>
            <form action="{{ route('search') }}" method="GET" class="flex gap-2">
                <div class="relative flex-1">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search" name="q" value="{{ $q }}"
                           class="w-full pl-11 pr-4 py-3 rounded-xl bg-white border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#52B788] text-sm">
                </div>
                <button type="submit" class="btn-primary">Rechercher</button>
            </form>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if(strlen($q) < 2)
            <div class="text-center py-12 text-gray-400">
                <p>Saisissez au moins 2 caractères pour lancer une recherche.</p>
            </div>
        @elseif($results->isEmpty())
            <div class="text-center py-12 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="font-medium text-lg">Aucun résultat trouvé pour "{{ $q }}"</p>
                <p class="text-sm mt-1">Essayez d'autres mots-clés</p>
            </div>
        @else
            <p class="text-sm text-gray-500 mb-6"><strong class="text-gray-800">{{ $results->count() }}</strong> résultat(s) trouvé(s)</p>

            <div class="space-y-4">
                @foreach($results as $result)
                    <div class="bg-white rounded-xl border border-gray-100 hover:border-[#52B788] hover:shadow-sm transition-all p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    @php $type = $result['_type'] ?? 'ressource'; @endphp
                                    <span class="badge badge-{{ $type === 'actualite' ? 'actualite' : ($type === 'evenement' ? 'evenement' : 'publication') }}">
                                        {{ ucfirst($type) }}
                                    </span>
                                </div>
                                <h3 class="font-semibold text-gray-900 hover:text-[#2D6A4F] text-sm mb-1 line-clamp-1">
                                    {{ $result['title'] }}
                                </h3>
                                <p class="text-xs text-gray-500 line-clamp-2">
                                    {{ $result['excerpt'] ?? $result['abstract'] ?? $result['content'] ?? '' }}
                                </p>
                            </div>
                            @php
                                $type = $result['_type'] ?? 'ressource';
                                $url = match($type) {
                                    'ressource' => route('bibliotheque.show', $result['slug']),
                                    'actualite' => route('actualites.show', $result['slug']),
                                    'evenement' => route('evenements.show', $result['slug']),
                                    'acteur' => route('carte.acteur', $result['slug']),
                                    'forum' => route('communaute.thread', [$result['category'] ?? 'general', $result['slug']]),
                                    'multimedia' => route('multimedia.show', $result['slug']),
                                    default => '#',
                                };
                            @endphp
                            <a href="{{ $url }}"
                               class="shrink-0 px-4 py-2 bg-[#F8F5F0] hover:bg-[#2D6A4F] hover:text-white text-[#2D6A4F] text-xs font-semibold rounded-lg transition-colors">
                                Voir →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
