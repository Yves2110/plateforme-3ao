<x-app-layout>
    <x-slot name="title">{{ __('home.title') }}</x-slot>

    {{-- =========================================================
         SECTION 1 : HERO + ACTUALITÉS
    ========================================================= --}}
    <section class="relative min-h-[600px] lg:min-h-[640px] bg-[#2D6A4F] overflow-hidden">

        {{-- Image de fond (agriculteurs) --}}
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-agriculture.jpg') }}"
                 alt="Agriculteurs en Afrique de l'Ouest"
                 class="w-full h-full object-cover object-center opacity-40"
                 loading="eager">
            <div class="absolute inset-0 hero-overlay"></div>
        </div>

        {{-- Motif décoratif --}}
        <div class="absolute top-0 right-0 w-96 h-96 opacity-5">
            <svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="200" cy="200" r="180" stroke="white" stroke-width="2"/>
                <circle cx="200" cy="200" r="120" stroke="white" stroke-width="2"/>
                <circle cx="200" cy="200" r="60" stroke="white" stroke-width="2"/>
            </svg>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-start">

                {{-- ===== Col gauche : Hero text (60%) ===== --}}
                <div class="lg:col-span-3">
                    {{-- Badge --}}
                    <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm border border-white/30 rounded-full px-4 py-1.5 text-white/90 text-sm mb-6 animate-fade-in-up">
                        <span class="w-2 h-2 bg-[#F4C842] rounded-full animate-pulse"></span>
                        {{ __('home.badge') }}
                    </div>

                    <h1 class="font-display text-4xl sm:text-5xl lg:text-5xl xl:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up animate-delay-200">
                        {{ __('home.hero_title') }}
                    </h1>

                    <p class="text-white/85 text-lg leading-relaxed mb-8 max-w-xl animate-fade-in-up animate-delay-400">
                        {{ __('home.hero_subtitle') }}
                    </p>

                    <div class="flex flex-wrap gap-3 mb-12 animate-fade-in-up animate-delay-600">
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

                    {{-- ===== Compteurs statistiques (Alpine.js) ===== --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1 bg-white/10 backdrop-blur-sm rounded-2xl p-2"
                         x-data="counters()" x-init="startCounting()">
                        <div class="stat-card">
                            <svg class="w-6 h-6 text-[#F4C842] mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                            <div class="stat-number" x-text="counts.pays + '+'"></div>
                            <div class="stat-label">{{ __('home.stats_countries') }}</div>
                        </div>
                        <div class="stat-card">
                            <svg class="w-6 h-6 text-[#F4C842] mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div class="stat-number" x-text="counts.orgs + '+'"></div>
                            <div class="stat-label">{{ __('home.stats_actors') }}</div>
                        </div>
                        <div class="stat-card">
                            <svg class="w-6 h-6 text-[#F4C842] mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div class="stat-number" x-text="counts.ressources + '+'"></div>
                            <div class="stat-label">{{ __('home.stats_resources') }}</div>
                        </div>
                        <div class="stat-card">
                            <svg class="w-6 h-6 text-[#F4C842] mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div class="stat-number" x-text="counts.events + '+'"></div>
                            <div class="stat-label">{{ __('home.stats_events') }}</div>
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
                                <a href="#" class="flex gap-3 p-4 hover:bg-gray-50 transition-colors group">
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
                                        <div class="mb-1">
                                            <span class="badge badge-{{ strtolower($actu->category ?? 'actualite') }}">
                                                {{ $actu->category ?? 'Actualité' }}
                                            </span>
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
                    <a href="#" class="card group">
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
                                <span>{{ $ressource->published_at?->format('Y') }}</span>
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
                    <div class="card group cursor-pointer">
                        <div class="flex gap-0">
                            {{-- Date block --}}
                            <div class="w-20 shrink-0 bg-[#2D6A4F] flex flex-col items-center justify-center py-5 text-white rounded-l-2xl">
                                <span class="font-display font-bold text-2xl leading-none">{{ $event->start_date->format('d') }}</span>
                                <span class="text-xs uppercase tracking-wider mt-1 text-white/80">{{ $event->start_date->translatedFormat('M') }}</span>
                                <span class="text-xs text-white/60">{{ $event->start_date->format('Y') }}</span>
                            </div>
                            {{-- Event info --}}
                            <div class="flex-1 p-4">
                                <span class="badge badge-evenement mb-2 block w-fit">{{ $event->type ?? 'Événement' }}</span>
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
                    </div>
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
         SECTION 6 : PARTENAIRES
    ========================================================= --}}
    <section class="py-14 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-400 font-medium uppercase tracking-widest mb-8">
                {{ __('home.partners_title') }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12">
                @foreach(['ROPPA', 'CIRAD', 'FAO', 'GIZ', 'ARAA', 'CEDEAO', 'ENDA-PRONAT'] as $partenaire)
                    <div class="flex items-center justify-center h-12 px-6 bg-gray-50 hover:bg-[#F8F5F0] rounded-xl transition-colors cursor-pointer">
                        <span class="font-display font-bold text-gray-400 hover:text-[#2D6A4F] transition-colors text-sm tracking-wide">
                            {{ $partenaire }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        function counters() {
            return {
                counts: { pays: 0, orgs: 0, ressources: 0, events: 0 },
                targets: { pays: 15, orgs: 120, ressources: 300, events: 45 },
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
