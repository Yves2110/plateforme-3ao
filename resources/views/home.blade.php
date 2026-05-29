<x-app-layout>
    <x-slot name="title">{{ __('home.title') }}</x-slot>

    @php
        $sortedHero = $heroSlides->sortBy('sort_order')->values();
        $firstHero = $sortedHero->first();
        $otherHeroImages = $firstHero ? $sortedHero->slice(1) : $sortedHero;

        $mapImageSlide = fn ($s) => [
            'type' => 'image',
            'url'  => $s->imageUrl(),
            'alt'  => $s->alt_text ?? '',
        ];

        $eventSlides = $urgentHeroEvents->map(fn ($e) => [
            'type'     => 'event',
            'url'      => $e->schedule()->heroImageUrl(),
            'title'    => $e->title,
            'href'     => route('evenements.show', $e->slug),
            'label'    => $e->schedule()->label(),
            'typeName' => $e->type,
            'date'     => $e->start_date->translatedFormat('d M Y'),
            'location' => $e->is_online ? __('evenements.online') : trim(($e->location ? $e->location.', ' : '').($e->country ?? '')),
        ]);

        $heroSlidesJson = collect();
        if ($firstHero) {
            $heroSlidesJson->push($mapImageSlide($firstHero));
        }
        $heroSlidesJson = $heroSlidesJson
            ->merge($eventSlides)
            ->merge($otherHeroImages->map($mapImageSlide))
            ->values();

        if ($heroSlidesJson->isEmpty()) {
            $heroSlidesJson = collect([['type' => 'image', 'url' => '', 'alt' => '']]);
        }

        $partnersJson = $partners->map(fn ($p) => [
            'name' => $p->name,
            'url'  => $p->logoUrl(),
            'link' => $p->website_url,
        ])->values();
    @endphp

    {{-- =========================================================
         SECTION 1 : HERO SLIDER + ACTUALITÉS
    ========================================================= --}}
    <section class="relative bg-[#1A1A2E] overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-10 items-stretch">

                {{-- ===== Col gauche : Slider ===== --}}
                <div class="lg:col-span-3 relative min-h-[480px] lg:min-h-[520px] rounded-2xl overflow-hidden"
                     x-data="heroSlider(@js($heroSlidesJson), 6000)"
                     x-init="start()">

                    {{-- Slides images + rappels événements (≤ 7 jours) --}}
                    <template x-for="(slide, index) in slides" :key="index">
                        <div x-show="current === index"
                             x-transition:enter="transition-opacity duration-700 ease-out"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="absolute inset-0">
                            <template x-if="slide.type === 'event' && !slide.url">
                                <div class="w-full h-full bg-gradient-to-br from-[#2D6A4F] to-[#40916C]"></div>
                            </template>
                            <template x-if="slide.url">
                                <img :src="slide.url" :alt="slide.alt || slide.title"
                                     class="w-full h-full object-cover"
                                     loading="eager">
                            </template>
                            <div class="absolute inset-0"
                                 :class="slide.type === 'event'
                                     ? 'bg-gradient-to-t from-[#1A1A2E]/95 via-[#1A1A2E]/50 to-[#2D6A4F]/30'
                                     : 'bg-gradient-to-r from-[#1A1A2E]/85 via-[#2D6A4F]/70 to-[#2D6A4F]/40'"></div>
                            <div x-show="slide.type === 'event'"
                                 class="absolute top-0 left-0 right-0 h-2 bg-[#F4C842] shadow-[0_2px_12px_rgba(244,200,66,0.6)] z-10"></div>
                            <div x-show="slide.type === 'event'"
                                 class="absolute bottom-0 left-0 right-0 z-10 p-6 sm:p-8">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#F4C842] text-[#1A1A2E] text-xs font-bold uppercase tracking-wide mb-3"
                                      x-text="slide.label"></span>
                                <p class="text-[#F4C842] text-xs font-semibold uppercase tracking-wider mb-1" x-text="slide.typeName"></p>
                                <h2 class="font-display text-xl sm:text-2xl font-bold text-white leading-snug mb-2 max-w-lg" x-text="slide.title"></h2>
                                <p class="text-white/80 text-sm mb-4" x-text="slide.date + ' · ' + slide.location"></p>
                                <a :href="slide.href"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#D4A017] hover:bg-[#F4C842] text-white text-sm font-semibold rounded-xl transition-colors">
                                    {{ __('evenements.see_event') }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </template>

                    {{-- Contenu texte (toutes les images hero ; masqué sur les rappels événement) --}}
                    <div class="relative z-10 h-full flex flex-col justify-center p-6 sm:p-8 lg:p-10"
                         x-show="showHeroContent"
                         x-transition.opacity>
                        <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm border border-white/30 rounded-full px-4 py-1.5 text-white/90 text-sm mb-4 w-fit">
                            <span class="w-2 h-2 bg-[#F4C842] rounded-full animate-pulse"></span>
                            {{ __('home.badge') }}
                        </div>

                        <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-snug mb-4 max-w-xl">
                            {{ __('home.hero_title') }}
                        </h1>

                        <p class="text-white/85 text-base sm:text-lg leading-relaxed mb-6 max-w-lg">
                            {{ __('home.hero_subtitle') }}
                        </p>

                        <div class="flex flex-wrap gap-3 mb-8">
                            <a href="{{ route('bibliotheque.index') }}" class="btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                {{ __('home.hero_cta_primary') }}
                            </a>
                            <a href="{{ route('communaute.index') }}" class="btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ __('home.hero_cta_secondary') }}
                            </a>
                        </div>

                        {{-- Chiffres clés (données réelles) --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-1 bg-white/10 backdrop-blur-sm rounded-2xl p-2"
                             x-data="counters(@js($stats))" x-init="startCounting()">
                            <a href="{{ $statsLinks['pays'] }}" class="stat-card hover:bg-white/10 transition-colors rounded-xl">
                                <div class="stat-number" x-text="formatCount('pays')"></div>
                                <div class="stat-label">{{ __('home.stats_countries') }}</div>
                            </a>
                            <a href="{{ $statsLinks['orgs'] }}" class="stat-card hover:bg-white/10 transition-colors rounded-xl">
                                <div class="stat-number" x-text="formatCount('orgs')"></div>
                                <div class="stat-label">{{ __('home.stats_actors') }}</div>
                            </a>
                            <a href="{{ $statsLinks['ressources'] }}" class="stat-card hover:bg-white/10 transition-colors rounded-xl">
                                <div class="stat-number" x-text="formatCount('ressources')"></div>
                                <div class="stat-label">{{ __('home.stats_resources') }}</div>
                            </a>
                            <a href="{{ $statsLinks['events'] }}" class="stat-card hover:bg-white/10 transition-colors rounded-xl">
                                <div class="stat-number" x-text="formatCount('events')"></div>
                                <div class="stat-label">{{ __('home.stats_events') }}</div>
                            </a>
                        </div>
                    </div>

                    {{-- Contrôles slider --}}
                    <div class="absolute bottom-4 left-4 right-4 z-20 flex items-center justify-between gap-3" x-show="slides.length > 1">
                        <div class="flex gap-1.5">
                            <template x-for="(_, index) in slides" :key="'dot-'+index">
                                <button type="button" @click="goTo(index)"
                                        class="h-2 rounded-full transition-all"
                                        :class="current === index ? 'w-6 bg-[#F4C842]' : 'w-2 bg-white/50 hover:bg-white/80'"
                                        :aria-label="'Slide ' + (index + 1)"></button>
                            </template>
                        </div>
                        <div class="flex gap-1">
                            <button type="button" @click="prev()" class="p-2 rounded-full bg-black/30 text-white hover:bg-black/50 backdrop-blur-sm" aria-label="Précédent">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button type="button" @click="next()" class="p-2 rounded-full bg-black/30 text-white hover:bg-black/50 backdrop-blur-sm" aria-label="Suivant">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ===== Col droite : Actualités récentes (40%) ===== --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                            <h2 class="font-display font-bold text-gray-800 text-base">{{ __('home.news_title') }}</h2>
                            <a href="{{ route('actualites.index') }}"
                               class="text-[#2D6A4F] text-sm font-medium hover:underline flex items-center gap-1">
                                {{ __('common.see_all') }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>

                        {{-- Liste actualités --}}
                        <div class="divide-y divide-gray-50">
                            @forelse($actualites as $actu)
                                <a href="{{ route('actualites.show', $actu->slug) }}" class="flex gap-3 p-4 hover:bg-gray-50 transition-colors group">
                                    {{-- Thumbnail --}}
                                    <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-gray-100">
                                        @if($actu->thumbnail)
                                            <img src="{{ asset('storage/' . $actu->thumbnail) }}"
                                                 alt="{{ $actu->title }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                                 loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-[#52B788] to-[#2D6A4F] flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- Texte --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="mb-1 flex flex-wrap items-center gap-1.5">
                                            <x-actualite-category-badge :actualite="$actu" />
                                            <x-syndicated-notice :actualite="$actu" size="xs" />
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800 group-hover:text-[#2D6A4F] transition-colors line-clamp-2 leading-snug">
                                            {{ $actu->title }}
                                        </p>
                                        <div class="flex items-center gap-1 mt-1.5 text-xs text-gray-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $actu->published_at?->translatedFormat('d F Y') }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                {{-- État vide --}}
                                <div class="p-8 text-center text-gray-400 text-sm">
                                    <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                    {{ __('home.no_news') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- =========================================================
         SECTION 2 : THÉMATIQUES PHARES
    ========================================================= --}}
    <section class="py-16 bg-[#F8F5F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="section-title">{{ __('home.themes_title') }}</h2>
                <p class="section-subtitle">{{ __('home.themes_subtitle') }}</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach([
                    ['label' => 'Agroécologie', 'icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'bg-green-50 text-green-700 hover:bg-green-100'],
                    ['label' => 'Semences paysannes', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'color' => 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100'],
                    ['label' => 'Eau & Sols', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'bg-blue-50 text-blue-700 hover:bg-blue-100'],
                    ['label' => 'Marchés & Filières', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'bg-orange-50 text-orange-700 hover:bg-orange-100'],
                    ['label' => 'Politiques publiques', 'icon' => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z', 'color' => 'bg-purple-50 text-purple-700 hover:bg-purple-100'],
                    ['label' => 'Climat & Résilience', 'icon' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z', 'color' => 'bg-teal-50 text-teal-700 hover:bg-teal-100'],
                ] as $theme)
                    <a href="{{ route('bibliotheque.index', ['theme' => $theme['label']]) }}"
                       class="flex flex-col items-center gap-3 p-5 {{ $theme['color'] }} rounded-2xl transition-colors cursor-pointer group">
                        <svg class="w-8 h-8 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $theme['icon'] }}"/>
                        </svg>
                        <span class="text-xs font-semibold text-center leading-tight">{{ $theme['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =========================================================
         SECTION 3 : DERNIÈRES RESSOURCES
    ========================================================= --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h2 class="section-title">{{ __('home.resources_title') }}</h2>
                    <p class="section-subtitle">{{ __('home.resources_subtitle') }}</p>
                </div>
                <a href="{{ route('bibliotheque.index') }}" class="btn-outline text-sm py-2.5 hidden sm:inline-flex">
                    {{ __('home.see_library') }} →
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($ressources as $ressource)
                    <a href="{{ route('bibliotheque.show', $ressource->slug) }}" class="card group">
                        {{-- Thumbnail --}}
                        <div class="relative h-48 bg-gradient-to-br from-[#52B788] to-[#2D6A4F] overflow-hidden">
                            @if($ressource->thumbnail)
                                <img src="{{ asset('storage/' . $ressource->thumbnail) }}"
                                     alt="{{ $ressource->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            @endif
                            {{-- Type badge --}}
                            <span class="absolute top-3 left-3 badge badge-publication">
                                {{ $ressource->type ?? 'Document' }}
                            </span>
                        </div>
                        {{-- Content --}}
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-900 group-hover:text-[#2D6A4F] transition-colors line-clamp-2 mb-2 text-sm">
                                {{ $ressource->title }}
                            </h3>
                            <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $ressource->abstract }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $ressource->author ?? 'Auteur inconnu' }}
                                </span>
                                <span>{{ ($ressource->published_at ?? $ressource->created_at)?->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    @for($i = 0; $i < 3; $i++)
                        <div class="card animate-pulse">
                            <div class="h-48 bg-gray-100"></div>
                            <div class="p-5 space-y-3">
                                <div class="h-4 bg-gray-100 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-100 rounded w-full"></div>
                                <div class="h-3 bg-gray-100 rounded w-2/3"></div>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    {{-- =========================================================
         SECTION 4 : ÉVÉNEMENTS À VENIR
    ========================================================= --}}
    <section class="py-16 bg-[#F8F5F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h2 class="section-title">{{ __('home.events_title') }}</h2>
                    <p class="section-subtitle">{{ __('home.events_subtitle') }}</p>
                </div>
                <a href="{{ route('evenements.index') }}" class="btn-outline text-sm py-2.5 hidden sm:inline-flex">
                    {{ __('home.see_agenda') }} →
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($evenements as $event)
                    @php $evStatus = $event->schedule()->status(); @endphp
                    <a href="{{ route('evenements.show', $event->slug) }}"
                       class="card group block cursor-pointer relative overflow-hidden
                              {{ $evStatus === 'expired' ? 'opacity-75' : '' }}
                              {{ $evStatus === 'soon' ? 'ring-2 ring-[#F4C842] ring-offset-2' : '' }}">
                        @if($evStatus === 'soon')
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#F4C842] z-10"></div>
                        @endif
                        <div class="flex gap-0 {{ $evStatus === 'expired' ? 'grayscale' : '' }}">
                            <div class="w-20 shrink-0 flex flex-col items-center justify-center py-5 text-white rounded-l-2xl
                                        {{ $evStatus === 'expired' ? 'bg-gray-400' : 'bg-[#2D6A4F]' }}">
                                <span class="font-display font-bold text-2xl leading-none">{{ $event->start_date->format('d') }}</span>
                                <span class="text-xs uppercase tracking-wider mt-1 text-white/80">{{ $event->start_date->translatedFormat('M') }}</span>
                                <span class="text-xs text-white/60">{{ $event->start_date->format('Y') }}</span>
                            </div>
                            <div class="flex-1 p-4">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="badge badge-evenement">{{ $event->type ?? 'Événement' }}</span>
                                    <x-event-schedule-badge :event="$event" />
                                </div>
                                <h3 class="font-semibold text-sm text-gray-900 group-hover:text-[#2D6A4F] transition-colors line-clamp-2 mb-2">
                                    {{ $event->title }}
                                </h3>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $event->is_online ? 'En ligne' : ($event->location . ', ' . $event->country) }}
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="lg:col-span-3 text-center py-12 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="font-medium">{{ __('home.no_events') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- =========================================================
         SECTION 5 : REJOINDRE LA PLATEFORME (CTA)
    ========================================================= --}}
    <section class="py-20 bg-gradient-to-br from-[#2D6A4F] to-[#40916C] relative overflow-hidden">
        <div class="absolute inset-0 opacity-5">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid slice">
                <defs>
                    <pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1" fill="white"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#dots)"/>
            </svg>
        </div>
        <div class="relative max-w-4xl mx-auto px-4 text-center">
            <div class="inline-flex items-center gap-2 bg-white/15 rounded-full px-4 py-1.5 text-white/90 text-sm mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                {{ __('home.cta_badge') }}
            </div>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-white mb-4">
                {{ __('home.cta_title_line1') }}<br>
                <span class="text-[#F4C842]">{{ __('home.cta_title_highlight') }}</span>
            </h2>
            <p class="text-white/80 text-lg mb-8 max-w-2xl mx-auto">
                {{ __('home.cta_body') }}
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-3.5 bg-[#D4A017] hover:bg-[#F4C842] text-white hover:text-gray-900 font-semibold rounded-full transition-colors shadow-lg">
                    {{ __('home.cta_button') }}
                </a>
                <a href="#" class="px-8 py-3.5 bg-white/15 hover:bg-white/25 text-white font-semibold rounded-full border border-white/40 backdrop-blur-sm transition-colors">
                    {{ __('home.cta_learn_more') }}
                </a>
            </div>
        </div>
    </section>

    {{-- =========================================================
         SECTION 6 : PARTENAIRES (slider logos)
    ========================================================= --}}
    <section class="py-14 bg-white border-t border-gray-100"
             x-data="partnerLogoSlider(@js($partnersJson), 4000)"
             x-init="init(); start()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-400 font-medium uppercase tracking-widest mb-8">
                {{ __('home.partners_title') }}
            </p>

            <template x-if="items.length === 0">
                <p class="text-center text-gray-400 text-sm">Aucun partenaire configuré.</p>
            </template>

            <template x-if="items.length > 0">
                <div class="relative">
                    <div class="overflow-hidden">
                        <div class="flex transition-transform duration-500 ease-in-out"
                             :class="canSlide ? '' : 'justify-center'"
                             :style="canSlide ? `transform: translateX(-${offsetPercent}%)` : ''">
                            <template x-for="(partner, index) in items" :key="index">
                                <div class="flex-shrink-0 px-3 sm:px-4" :style="`width: ${slotWidthPercent}%`">
                                    <a :href="partner.link || '#'"
                                       @click="!partner.link && $event.preventDefault()"
                                       :target="partner.link ? '_blank' : null"
                                       :rel="partner.link ? 'noopener noreferrer' : null"
                                       class="flex items-center justify-center h-20 sm:h-24 bg-gray-50 hover:bg-[#F8F5F0] rounded-xl transition-colors px-4">
                                        <template x-if="partner.url">
                                            <img :src="partner.url" :alt="partner.name"
                                                 class="max-h-12 sm:max-h-14 max-w-[140px] sm:max-w-[160px] w-full object-contain opacity-80 hover:opacity-100 transition-opacity"
                                                 loading="lazy">
                                        </template>
                                        <template x-if="!partner.url">
                                            <span class="font-display font-bold text-gray-400 hover:text-[#2D6A4F] transition-colors text-xs sm:text-sm tracking-wide text-center"
                                                  x-text="partner.name"></span>
                                        </template>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>

                    <template x-if="canSlide">
                        <div class="flex items-center justify-center gap-4 mt-6">
                            <button type="button" @click="prev()" aria-label="Précédent"
                                    class="w-9 h-9 rounded-full border border-gray-200 text-gray-500 hover:border-[#2D6A4F] hover:text-[#2D6A4F] transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <div class="flex gap-1.5">
                                <template x-for="(_, i) in slideCount" :key="i">
                                    <button type="button" @click="goTo(i)"
                                            class="w-2 h-2 rounded-full transition-colors"
                                            :class="current === i ? 'bg-[#2D6A4F]' : 'bg-gray-300 hover:bg-gray-400'"
                                            :aria-label="'Slide ' + (i + 1)"></button>
                                </template>
                            </div>
                            <button type="button" @click="next()" aria-label="Suivant"
                                    class="w-9 h-9 rounded-full border border-gray-200 text-gray-500 hover:border-[#2D6A4F] hover:text-[#2D6A4F] transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </section>

    @push('scripts')
    <script>
        function heroSlider(slides, intervalMs) {
            return {
                slides: slides.length ? slides : [{ url: '', alt: '' }],
                current: 0,
                timer: null,
                get showHeroContent() {
                    const slide = this.slides[this.current];
                    return !slide || slide.type !== 'event';
                },
                start() {
                    if (this.slides.length <= 1) return;
                    this.timer = setInterval(() => this.next(), intervalMs);
                },
                goTo(i) {
                    this.current = i;
                    this.resetTimer();
                },
                next() {
                    this.current = (this.current + 1) % this.slides.length;
                },
                prev() {
                    this.current = (this.current - 1 + this.slides.length) % this.slides.length;
                    this.resetTimer();
                },
                resetTimer() {
                    if (this.timer) clearInterval(this.timer);
                    if (this.slides.length > 1) {
                        this.timer = setInterval(() => this.next(), intervalMs);
                    }
                },
            };
        }

        function partnerLogoSlider(items, intervalMs) {
            return {
                items: items.length ? items : [],
                current: 0,
                perView: 4,
                timer: null,
                init() {
                    this.updatePerView();
                    window.addEventListener('resize', () => this.updatePerView());
                },
                updatePerView() {
                    const w = window.innerWidth;
                    this.perView = w >= 1280 ? 5 : w >= 1024 ? 4 : w >= 640 ? 3 : 2;
                    if (this.current > this.maxIndex) {
                        this.current = this.maxIndex;
                    }
                },
                get canSlide() {
                    return this.items.length > this.perView;
                },
                get maxIndex() {
                    return Math.max(0, this.items.length - this.perView);
                },
                get slideCount() {
                    return this.maxIndex + 1;
                },
                get slotWidthPercent() {
                    return this.items.length ? 100 / this.perView : 100;
                },
                get offsetPercent() {
                    return this.current * this.slotWidthPercent;
                },
                start() {
                    if (!this.canSlide) return;
                    this.timer = setInterval(() => this.next(), intervalMs);
                },
                goTo(i) {
                    this.current = i;
                    this.resetTimer();
                },
                next() {
                    this.current = this.current >= this.maxIndex ? 0 : this.current + 1;
                    this.resetTimer();
                },
                prev() {
                    this.current = this.current <= 0 ? this.maxIndex : this.current - 1;
                    this.resetTimer();
                },
                resetTimer() {
                    if (this.timer) clearInterval(this.timer);
                    if (this.canSlide) {
                        this.timer = setInterval(() => this.next(), intervalMs);
                    }
                },
            };
        }

        function counters(targets) {
            return {
                counts: { pays: 0, orgs: 0, ressources: 0, events: 0 },
                targets: targets,
                formatCount(key) {
                    const n = this.counts[key] ?? 0;
                    return n > 0 ? n + '+' : '0';
                },
                startCounting() {
                    const duration = 2000;
                    const steps = 60;
                    const interval = duration / steps;
                    let step = 0;
                    const timer = setInterval(() => {
                        step++;
                        const progress = step / steps;
                        const eased = 1 - Math.pow(1 - progress, 3);
                        Object.keys(this.targets).forEach(key => {
                            this.counts[key] = Math.round(this.targets[key] * eased);
                        });
                        if (step >= steps) {
                            clearInterval(timer);
                            Object.keys(this.targets).forEach(key => {
                                this.counts[key] = this.targets[key];
                            });
                        }
                    }, interval);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
