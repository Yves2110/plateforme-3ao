<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') — 3AO</title>
    <link rel="icon" type="image/jpeg" href="{{ asset(config('brand.logo')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body class="h-full font-sans text-[#1A1A2E]" x-data="{ sidebarOpen: false }">

<div class="flex h-full">

    {{-- ===== Sidebar ===== --}}
    <aside class="hidden lg:flex lg:flex-col w-64 bg-[#1A1A2E] shrink-0 fixed inset-y-0 left-0 z-50">
        {{-- Logo --}}
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-6 py-5 border-b border-white/10 hover:bg-white/5 transition-colors">
            <x-logo size="sm" class="!gap-2" />
            <div class="min-w-0">
                <p class="text-white font-display font-bold text-sm leading-none">Back-office</p>
                <p class="text-white/40 text-xs truncate">Administration 3AO</p>
            </div>
        </a>

        {{-- Navigation --}}
        @php
            $u = auth()->user();
            $isSuperAdmin = $u && $u->hasRole('super_admin');

            // Helper closure pour rendre un item
            $navItem = function($route, $label, $icon, $badge = null, $highlight = false) {
                $active = request()->routeIs(rtrim($route, '.index') . '*') || request()->routeIs($route);
                $activeClass = $active ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5';
                return compact('route', 'label', 'icon', 'badge', 'active', 'activeClass', 'highlight');
            };

            $section = function($title) {
                return '<p class="px-3 pt-4 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-white/30">' . $title . '</p>';
            };
        @endphp

        <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">

            {{-- ===== Pilotage ===== --}}
            {!! $section('Pilotage') !!}
            <a id="admin-nav-dashboard" href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.dashboard') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Tableau de bord
            </a>

            {{-- ===== Contenus ===== --}}
            @canany(['publier-actualites', 'publier-bibliotheque', 'creer-evenements', 'contribuer-multimedia', 'gerer-formations'])
                {!! $section('Contenus') !!}
            @endcanany

            {{-- ===== Administration ===== --}}
            @can('administrer-utilisateurs')
                {!! $section('Administration') !!}

                <a id="admin-nav-users-pending" href="{{ route('admin.users.pending') }}"
                   class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.users.pending') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Inscriptions en attente
                    </span>
                    @if($adminCounts['users_pending'] > 0)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 font-bold">{{ $adminCounts['users_pending'] }}</span>
                    @endif
                </a>

                <a id="admin-nav-users" href="{{ route('admin.utilisateurs.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.utilisateurs.*') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Utilisateurs
                </a>
            @endcan

            @can('publier-actualites')
                <a id="admin-nav-actualites" href="{{ route('admin.actualites.index') }}"
                   class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.actualites.*') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 11h4"/></svg>
                        Actualités
                    </span>
                    @if($adminCounts['actualites_drafts'] > 0)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-[#D4A017]/20 text-[#D4A017] font-bold">{{ $adminCounts['actualites_drafts'] }}</span>
                    @endif
                </a>
            @endcan

            @can('publier-bibliotheque')
                <a id="admin-nav-ressources" href="{{ route('admin.ressources.index') }}"
                   class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.ressources.*') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18"/></svg>
                        Bibliothèque
                    </span>
                    @if($adminCounts['ressources_pending'] > 0)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-[#D4A017]/20 text-[#D4A017] font-bold">{{ $adminCounts['ressources_pending'] }}</span>
                    @endif
                </a>
            @endcan

            @can('creer-evenements')
                <a id="admin-nav-evenements" href="{{ route('admin.evenements.index') }}"
                   class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.evenements.*') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Événements
                    </span>
                    @if($adminCounts['evenements_pending'] > 0)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-[#D4A017]/20 text-[#D4A017] font-bold">{{ $adminCounts['evenements_pending'] }}</span>
                    @endif
                </a>
            @endcan

            @can('contribuer-multimedia')
                <a id="admin-nav-medias" href="{{ route('admin.medias.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.medias.*') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Médias
                </a>
            @endcan

            @can('gerer-formations')
                <a id="admin-nav-formations" href="{{ route('admin.formations.index') }}"
                   class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.formations.*') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0v9"/></svg>
                        Formations
                    </span>
                    @if($adminCounts['formations_pending'] > 0)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-[#D4A017]/20 text-[#D4A017] font-bold">{{ $adminCounts['formations_pending'] }}</span>
                    @endif
                </a>
            @endcan

            {{-- ===== Communauté & Réseau ===== --}}
            @canany(['gerer-carte', 'soumettre-acteur', 'moderer-forum'])
                {!! $section('Communauté & Réseau') !!}
            @endcanany

            @canany(['gerer-carte', 'soumettre-acteur'])
                <a id="admin-nav-acteurs" href="{{ route('admin.acteurs.index') }}"
                   class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.acteurs.*') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Acteurs & Carte
                    </span>
                    @if($adminCounts['acteurs_pending'] > 0)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-[#D4A017]/20 text-[#D4A017] font-bold">{{ $adminCounts['acteurs_pending'] }}</span>
                    @endif
                </a>
            @endcanany

            @can('moderer-forum')
                <a id="admin-nav-forum" href="{{ route('admin.forum.index') }}"
                   class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.forum.*') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                        Forum
                    </span>
                    @if($adminCounts['forum_pending'] > 0)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 font-bold">{{ $adminCounts['forum_pending'] }}</span>
                    @endif
                </a>
            @endcan

            {{-- ===== Système ===== --}}
            @if($isSuperAdmin)
                {!! $section('Système') !!}

                <a id="admin-nav-users-system" href="{{ route('admin.utilisateurs.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.utilisateurs.*') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                    Utilisateurs & Droits
                </a>
            @endif

            @can('gerer-newsletter')
                @if(! $isSuperAdmin && ! auth()->user()->can('gerer-rss'))
                    {!! $section('Communication') !!}
                @elseif($isSuperAdmin)
                    {!! $section('Communication') !!}
                @endif
                <a id="admin-nav-newsletter" href="{{ route('admin.newsletter.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.newsletter.*') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Newsletter
                </a>
            @endcan

            @can('gerer-rss')
                @if(! $isSuperAdmin && ! auth()->user()->can('gerer-newsletter'))
                    {!! $section('Paramètres') !!}
                @endif
                <a id="admin-nav-rss" href="{{ route('admin.rss.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.rss.*') ? 'bg-[#52B788]/20 text-[#52B788]' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 5c7.18 0 13 5.82 13 13M6 11a7 7 0 017 7m-6 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                    Flux RSS entrants
                </a>
            @endcan
        </nav>

        {{-- Bas sidebar --}}
        <div class="px-4 py-4 border-t border-white/10">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-[#52B788] flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-white/40 text-xs truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs text-white/50 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour au site
            </a>
        </div>
    </aside>

    {{-- ===== Contenu principal ===== --}}
    <div class="flex-1 flex flex-col min-h-full lg:pl-64">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between gap-4 sticky top-0 z-40">
            <div class="flex items-center gap-4 min-w-0">
                <x-logo href="{{ route('admin.dashboard') }}" size="sm" class="hidden md:inline-flex shrink-0" />
                <div class="min-w-0">
                    <h1 class="font-display font-bold text-lg text-[#1A1A2E] truncate">@yield('page-title', 'Administration')</h1>
                    @hasSection('page-subtitle')
                        <p class="text-xs text-gray-400 truncate">@yield('page-subtitle')</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button"
                        @click="window.dispatchEvent(new CustomEvent('admin-tutorial-restart'))"
                        class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-[#2D6A4F] bg-[#F8F5F0] hover:bg-[#E8F0EB] rounded-lg transition-colors"
                        title="Ouvrir le guide d'utilisation du back-office">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Guide d'utilisation</span>
                </button>
                <a href="{{ route('home') }}" class="hidden sm:flex items-center gap-1.5 text-sm text-gray-500 hover:text-[#2D6A4F] transition-colors mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Retour au site
                </a>
                @yield('header-actions')
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if(session('warning'))
                <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                    {{ session('warning') }}
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 px-6 pb-8">
            @yield('content')
        </main>
    </div>
</div>

<x-admin-tutorial
    :steps="$adminGuideSteps ?? []"
    :show-on-load="$showAdminGuideOnLoad ?? false"
    :complete-url="route('admin.guide.complete')"
    :role-label="$adminGuideRoleLabel ?? ''"
/>

@stack('before-livewire')
@livewireScripts
@stack('scripts')

</body>
</html>
