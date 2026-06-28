{{-- ===== Barre supérieure ===== --}}
<div class="bg-[#2D6A4F] text-white text-xs py-1.5 px-4 hidden md:flex items-center justify-between">
    <span class="font-medium tracking-wide">{{ __('nav.tagline') }}</span>
    <div class="flex items-center gap-4">
        {{-- Réseaux sociaux --}}
        <a href="#" class="hover:text-[#F4C842] transition-colors" aria-label="Facebook">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
        </a>
        <a href="#" class="hover:text-[#F4C842] transition-colors" aria-label="Twitter/X">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>
        <a href="#" class="hover:text-[#F4C842] transition-colors" aria-label="YouTube">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 001.46 6.42 29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/></svg>
        </a>
        <a href="#" class="hover:text-[#F4C842] transition-colors" aria-label="LinkedIn">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
        </a>
        {{-- Switcher de langue FR / EN --}}
        @php $currentLocale = app()->getLocale(); @endphp
        <div class="flex items-center gap-0.5 ml-2 border-l border-white/30 pl-3 bg-white/10 rounded-lg p-0.5">
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'fr']) }}"
               class="flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold transition-colors {{ $currentLocale === 'fr' ? 'bg-white text-[#2D6A4F]' : 'text-white/80 hover:text-white' }}"
               aria-label="Français" title="Français">
                <span>🇫🇷</span><span>FR</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
               class="flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold transition-colors {{ $currentLocale === 'en' ? 'bg-white text-[#2D6A4F]' : 'text-white/80 hover:text-white' }}"
               aria-label="English" title="English">
                <span>🇬🇧</span><span>EN</span>
            </a>
        </div>
    </div>
</div>

{{-- ===== Navigation principale ===== --}}
<nav x-data="{ mobileOpen: false, searchOpen: false }"
     class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <x-logo href="{{ route('home') }}" size="md" :show-subtitle="true" />

            {{-- Menu principal (desktop) --}}
            <div class="hidden lg:flex items-center gap-1">
                <a href="{{ route('home') }}"
                   class="nav-link px-3 py-2 rounded-lg hover:bg-[#F8F5F0] {{ request()->routeIs('home') ? 'text-[#2D6A4F] font-semibold bg-[#F8F5F0]' : '' }}">
                    {{ __('nav.home') }}
                </a>
                <a id="nav-about" href="{{ route('about.index') }}"
                   class="nav-link px-3 py-2 rounded-lg hover:bg-[#F8F5F0] {{ request()->routeIs('about.*') ? 'text-[#2D6A4F] font-semibold bg-[#F8F5F0]' : '' }}">
                    {{ __('nav.about') }}
                </a>
                <a id="nav-bibliotheque" href="{{ route('bibliotheque.index') }}"
                   class="nav-link px-3 py-2 rounded-lg hover:bg-[#F8F5F0] {{ request()->routeIs('bibliotheque.*') ? 'text-[#2D6A4F] font-semibold bg-[#F8F5F0]' : '' }}">
                    {{ __('nav.library') }}
                </a>
                <a id="nav-forum" href="{{ route('communaute.index') }}"
                   class="nav-link px-3 py-2 rounded-lg hover:bg-[#F8F5F0] {{ request()->routeIs('communaute.*') ? 'text-[#2D6A4F] font-semibold bg-[#F8F5F0]' : '' }}">
                    {{ __('nav.community') }}
                </a>
                <a href="{{ route('multimedia.index') }}"
                   class="nav-link px-3 py-2 rounded-lg hover:bg-[#F8F5F0] {{ request()->routeIs('multimedia.*') ? 'text-[#2D6A4F] font-semibold bg-[#F8F5F0]' : '' }}">
                    {{ __('nav.multimedia') }}
                </a>
                <a id="nav-carte" href="{{ route('carte.index') }}"
                   class="nav-link px-3 py-2 rounded-lg hover:bg-[#F8F5F0] {{ request()->routeIs('carte.*') ? 'text-[#2D6A4F] font-semibold bg-[#F8F5F0]' : '' }}">
                    {{ __('nav.map') }}
                </a>
                <a id="nav-evenements" href="{{ route('evenements.index') }}"
                   class="nav-link px-3 py-2 rounded-lg hover:bg-[#F8F5F0] {{ request()->routeIs('evenements.*') ? 'text-[#2D6A4F] font-semibold bg-[#F8F5F0]' : '' }}">
                    {{ __('nav.events') }}
                </a>
                <a id="nav-formation" href="{{ route('formation.index') }}"
                   class="nav-link px-3 py-2 rounded-lg hover:bg-[#F8F5F0] {{ request()->routeIs('formation.*') ? 'text-[#2D6A4F] font-semibold bg-[#F8F5F0]' : '' }}">
                    <x-icon name="graduation" class="w-4 h-4 inline-block -mt-0.5" /> {{ __('nav.training') }}
                </a>
            </div>

            {{-- Actions (droite) --}}
            <div class="flex items-center gap-2">
                {{-- Recherche --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-100 transition-colors" aria-label="{{ __('nav.search_aria') }}">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 p-3 z-[100]">
                        <form action="{{ route('search') }}" method="GET">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="search" name="q" placeholder="{{ __('nav.search_placeholder') }}"
                                       class="search-global" autofocus>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Auth --}}
                @auth
                    @php
                        $u = auth()->user();
                        $isAdmin = $u && $u->canAccessBackOffice();
                        $canValidateRegistrations = $u && $u->canValidateRegistrations();
                        $adminMenuUrl = $isAdmin ? route('admin.dashboard') : ($canValidateRegistrations ? route('admin.users.pending') : null);
                    @endphp
                    <div x-data="{ open: false }" class="relative hidden sm:block">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-[#2D6A4F] rounded-full hover:bg-[#F8F5F0] transition-colors">
                            <div class="w-8 h-8 rounded-full bg-[#2D6A4F] text-white flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <span class="hidden md:inline">{{ explode(' ', $u->name)[0] }}</span>
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                             class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                            <div class="px-4 py-2 border-b border-gray-50">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $u->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $u->email }}</p>
                            </div>
                            @if($adminMenuUrl)
                                <a href="{{ $adminMenuUrl }}" class="flex items-center gap-2 px-4 py-2 text-sm text-[#D4A017] hover:bg-[#F8F5F0]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    {{ $canValidateRegistrations && ! $isAdmin ? 'Valider les inscriptions' : __('nav.admin') }}
                                </a>
                            @endif
                            <a href="{{ route('membre.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-[#F8F5F0] hover:text-[#2D6A4F]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ __('nav.my_space') }}
                            </a>
                            <a href="{{ route('learning.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-[#F8F5F0] hover:text-[#2D6A4F]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Mon apprentissage
                            </a>
                            <a href="{{ route('membre.show', $u) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-[#F8F5F0] hover:text-[#2D6A4F]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ __('nav.public_profile') }}
                            </a>
                            <div class="border-t border-gray-50 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        {{ __('nav.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-[#2D6A4F] rounded-full hover:bg-[#F8F5F0] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        {{ __('nav.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm py-2 px-5 hidden sm:inline-flex">
                        {{ __('nav.join_platform') }}
                    </a>
                @endauth

                {{-- Burger mobile --}}
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors ml-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menu mobile --}}
    <div x-show="mobileOpen" x-transition class="lg:hidden border-t border-gray-100 bg-white px-4 py-4 space-y-1">
        <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-[#F8F5F0] hover:text-[#2D6A4F]">{{ __('nav.home') }}</a>
        <a href="{{ route('about.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-[#F8F5F0] hover:text-[#2D6A4F]">{{ __('nav.about') }}</a>
        <a href="{{ route('bibliotheque.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-[#F8F5F0] hover:text-[#2D6A4F]">{{ __('nav.library') }}</a>
        <a href="{{ route('communaute.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-[#F8F5F0] hover:text-[#2D6A4F]">{{ __('nav.community') }}</a>
        <a href="{{ route('multimedia.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-[#F8F5F0] hover:text-[#2D6A4F]">{{ __('nav.multimedia') }}</a>
        <a href="{{ route('carte.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-[#F8F5F0] hover:text-[#2D6A4F]">{{ __('nav.map') }}</a>
        <a href="{{ route('evenements.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-[#F8F5F0] hover:text-[#2D6A4F]">{{ __('nav.events') }}</a>
        <a href="{{ route('formation.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-[#F8F5F0] hover:text-[#2D6A4F]">{{ __('nav.training') }}</a>
        @php $mobileLocale = app()->getLocale(); @endphp
        <div class="flex items-center gap-2 pt-2">
            <span class="text-xs text-gray-500 px-3">{{ __('nav.language') }}</span>
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'fr']) }}"
               class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $mobileLocale === 'fr' ? 'bg-[#2D6A4F] text-white' : 'bg-gray-100 text-gray-700' }}">FR</a>
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
               class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $mobileLocale === 'en' ? 'bg-[#2D6A4F] text-white' : 'bg-gray-100 text-gray-700' }}">EN</a>
        </div>
        <div class="pt-2 border-t border-gray-100 flex gap-2">
            @guest
                <a href="{{ route('login') }}" class="flex-1 text-center py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-full hover:bg-gray-50">{{ __('nav.login') }}</a>
                <a href="{{ route('register') }}" class="flex-1 text-center py-2 text-sm font-medium text-white bg-[#2D6A4F] rounded-full hover:bg-[#40916C]">{{ __('nav.join_short') }}</a>
            @endguest
        </div>
    </div>
</nav>
