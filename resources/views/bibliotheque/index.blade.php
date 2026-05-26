<x-app-layout>
    <x-slot name="title">{{ __('bibliotheque.title') }}</x-slot>

    {{-- Fil d'Ariane --}}
    <div class="bg-[#F8F5F0] border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-[#2D6A4F]">{{ __('nav.home') }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#2D6A4F] font-medium">{{ __('nav.library') }}</span>
        </div>
    </div>

    {{-- Header section --}}
    <div class="bg-gradient-to-r from-[#2D6A4F] to-[#40916C] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-3xl font-bold text-white mb-2">{{ __('bibliotheque.title') }}</h1>
            <p class="text-white/80 text-lg">{{ __('bibliotheque.subtitle') }}</p>

            {{-- Barre de recherche proéminente --}}
            <form action="{{ route('bibliotheque.index') }}" method="GET" class="mt-6 flex gap-2 max-w-2xl">
                <div class="relative flex-1">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}"
                           placeholder="{{ __('bibliotheque.search_placeholder') }}"
                           class="w-full pl-11 pr-4 py-3 rounded-xl text-gray-800 bg-white border-0 focus:outline-none focus:ring-2 focus:ring-[#F4C842] text-base">
                </div>
                <button type="submit" class="px-6 py-3 bg-[#D4A017] hover:bg-[#F4C842] text-white font-semibold rounded-xl transition-colors">
                    {{ __('common.search') }}
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ===== Sidebar filtres ===== --}}
            <aside class="w-full lg:w-64 shrink-0">
                <form action="{{ route('bibliotheque.index') }}" method="GET" id="filter-form">
                    <input type="hidden" name="q" value="{{ request('q') }}">

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-6">
                        <h3 class="font-display font-semibold text-gray-800">{{ __('common.filters') }}</h3>

                        {{-- Type de document --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{{ __('common.type') }}</label>
                            <div class="space-y-1.5">
                                @foreach(['Guide technique', 'Étude de cas', 'Publication scientifique', 'Fiche projet', 'Vidéo', 'Ouvrage'] as $type)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" name="type" value="{{ $type }}"
                                               {{ request('type') === $type ? 'checked' : '' }}
                                               class="accent-[#2D6A4F]" onchange="document.getElementById('filter-form').submit()">
                                        <span class="text-sm text-gray-700 group-hover:text-[#2D6A4F]">{{ $type }}</span>
                                    </label>
                                @endforeach
                                @if(request('type'))
                                    <a href="{{ route('bibliotheque.index', array_merge(request()->except('type'), [])) }}"
                                       class="text-xs text-red-500 hover:underline mt-1 block">✕ Effacer le filtre</a>
                                @endif
                            </div>
                        </div>

                        {{-- Langue --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Langue</label>
                            <div class="space-y-1.5">
                                @foreach(['fr' => 'Français', 'en' => 'English'] as $code => $label)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" name="langue" value="{{ $code }}"
                                               {{ request('langue') === $code ? 'checked' : '' }}
                                               class="accent-[#2D6A4F]" onchange="document.getElementById('filter-form').submit()">
                                        <span class="text-sm text-gray-700 group-hover:text-[#2D6A4F]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Thématiques --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Thématique</label>
                            <div class="space-y-1.5">
                                @foreach(['Agroécologie', 'Semences paysannes', 'Eau & Sols', 'Politiques publiques', 'Changement climatique', 'Marchés & Filières'] as $theme)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" name="theme" value="{{ $theme }}"
                                               {{ request('theme') === $theme ? 'checked' : '' }}
                                               class="accent-[#2D6A4F]" onchange="document.getElementById('filter-form').submit()">
                                        <span class="text-sm text-gray-700 group-hover:text-[#2D6A4F]">{{ $theme }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        @if(request()->hasAny(['type', 'langue', 'theme', 'q']))
                            <a href="{{ route('bibliotheque.index') }}"
                               class="block text-center py-2 px-4 bg-gray-100 hover:bg-red-50 hover:text-red-600 text-sm font-medium rounded-lg transition-colors">
                                Effacer tous les filtres
                            </a>
                        @endif
                    </div>
                </form>
            </aside>

            {{-- ===== Grille de ressources ===== --}}
            <div class="flex-1">
                {{-- Barre résultats + tri --}}
                <div class="flex items-center justify-between mb-6">
                    <p class="text-sm text-gray-500">
                        <strong class="text-gray-800">{{ $ressources->total() }}</strong> ressource(s) trouvée(s)
                        @if(request('q')) pour <em>"{{ request('q') }}"</em> @endif
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Vue :</span>
                        <button x-data @click="$dispatch('set-view', 'grid')"
                                class="p-1.5 rounded hover:bg-gray-100 text-gray-500 hover:text-[#2D6A4F]" title="Grille">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z"/></svg>
                        </button>
                        <button x-data @click="$dispatch('set-view', 'list')"
                                class="p-1.5 rounded hover:bg-gray-100 text-gray-500 hover:text-[#2D6A4F]" title="Liste">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Grille --}}
                <div x-data="{ view: 'grid' }"
                     @set-view.window="view = $event.detail"
                     :class="view === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5' : 'flex flex-col gap-4'">

                    @forelse($ressources as $r)
                        {{-- Carte grille --}}
                        <div x-show="view === 'grid'"
                             class="card group">
                            <div class="relative h-44 overflow-hidden bg-gradient-to-br from-[#52B788] to-[#2D6A4F]">
                                @if($r->thumbnail)
                                    <img src="{{ asset('storage/'.$r->thumbnail) }}" alt="{{ $r->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-14 h-14 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <span class="absolute top-2 left-2 badge badge-publication">{{ $r->type }}</span>
                                @if($r->language === 'en')
                                    <span class="absolute top-2 right-2 badge bg-blue-100 text-blue-700">EN</span>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-sm text-gray-900 group-hover:text-[#2D6A4F] transition-colors line-clamp-2 mb-1.5">
                                    <a href="{{ route('bibliotheque.show', $r->slug) }}">{{ $r->title }}</a>
                                </h3>
                                @if($r->abstract)
                                    <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $r->abstract }}</p>
                                @endif
                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    <span>{{ $r->author ?? 'Auteur inconnu' }}</span>
                                    <div class="flex items-center gap-2">
                                        @if($r->file_path)
                                            <svg class="w-3.5 h-3.5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        @endif
                                        <span>{{ $r->published_at?->format('Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Vue liste --}}
                        <div x-show="view === 'list'" x-cloak
                             class="bg-white rounded-xl border border-gray-100 hover:border-[#52B788] hover:shadow-sm transition-all p-4 flex gap-4 group">
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-gradient-to-br from-[#52B788] to-[#2D6A4F] shrink-0 flex items-center justify-center">
                                @if($r->thumbnail)
                                    <img src="{{ asset('storage/'.$r->thumbnail) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h3 class="font-semibold text-sm text-gray-900 group-hover:text-[#2D6A4F] transition-colors line-clamp-1">
                                        <a href="{{ route('bibliotheque.show', $r->slug) }}">{{ $r->title }}</a>
                                    </h3>
                                    <span class="badge badge-publication shrink-0">{{ $r->type }}</span>
                                </div>
                                <p class="text-xs text-gray-500 line-clamp-2 mb-2">{{ $r->abstract }}</p>
                                <div class="flex items-center gap-3 text-xs text-gray-400">
                                    <span>{{ $r->author ?? 'Auteur inconnu' }}</span>
                                    <span>·</span>
                                    <span>{{ $r->country }}</span>
                                    <span>·</span>
                                    <span>{{ strtoupper($r->language) }}</span>
                                    <span>·</span>
                                    <span>{{ $r->published_at?->format('Y') }}</span>
                                </div>
                            </div>
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('bibliotheque.show', $r->slug) }}"
                                   class="px-4 py-2 bg-[#F8F5F0] hover:bg-[#2D6A4F] hover:text-white text-[#2D6A4F] text-xs font-semibold rounded-lg transition-colors">
                                    Consulter
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 py-16 text-center text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p class="font-medium text-lg">Aucune ressource trouvée</p>
                            <p class="text-sm mt-1">Essayez d'autres termes ou effacez les filtres</p>
                            <a href="{{ route('bibliotheque.index') }}" class="btn-primary mt-4 text-sm py-2">
                                Voir toutes les ressources
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($ressources->hasPages())
                    <div class="mt-10">
                        {{ $ressources->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
