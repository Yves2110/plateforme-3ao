<x-app-layout>
    <x-slot name="title">{{ __('nav.map') }}</x-slot>

    <x-leaflet-assets />

    @push('styles')
    <style>
        #map { height: 100%; min-height: 500px; }
        .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,.15); }
        .leaflet-popup-content { font-family: 'Inter', sans-serif; margin: 14px 16px; }
        .marker-cluster-3ao { background: rgba(45,106,79,0.6); }
        .marker-cluster-3ao div { background: rgba(45,106,79,0.85); color: white; font-weight: 700; font-family: Inter; }
        .actor-item-active { background: #F8F5F0; border-left: 3px solid #2D6A4F; }
    </style>
    @endpush

    <div x-data="actorMap({
        endpoint: '{{ route('carte.acteurs') }}',
        actorEndpoint: '{{ url('/carte/acteur') }}',
        i18n: {
            map_error: @js(__('carte.map_error')),
            load_error: @js(__('carte.load_error')),
            view_profile: @js(__('carte.view_profile')),
        },
    })" x-init="init()">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
            @endif
            <x-public-manage-bar
                label="Carte des acteurs"
                :permissions="['gerer-carte', 'soumettre-acteur', 'administrer-utilisateurs']"
                :create-route="route('admin.acteurs.create')"
                :list-route="route('admin.acteurs.index')"
            />
        </div>

        {{-- Header avec filtres --}}
        <div class="bg-gradient-to-r from-[#2D6A4F] to-[#40916C] py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                    <div>
                        <h1 class="font-display text-2xl sm:text-3xl font-bold text-white mb-1">{{ __('carte.title') }}</h1>
                        <p class="text-white/80 text-sm">
                            <span x-text="filteredCount"></span> / {{ $actorCount }} {{ __('carte.organisations_displayed') }}
                        </p>
                    </div>

                    <div class="inline-flex bg-white/10 backdrop-blur rounded-xl p-1 border border-white/20">
                        <span class="px-3 py-1.5 rounded-lg text-sm font-semibold bg-white text-[#2D6A4F]">🗺 {{ __('carte.map') }}</span>
                        <a href="{{ route('carte.network') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition-colors">
                            🕸 {{ __('carte.network') }}
                        </a>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="search" x-model.debounce.400ms="search" @input="reload()"
                               placeholder="{{ __('carte.search_placeholder') }}"
                               class="w-full pl-9 pr-3 py-2 text-sm bg-white/10 border border-white/20 rounded-lg text-white placeholder:text-white/50 focus:outline-none focus:bg-white/20 focus:border-white/40">
                    </div>
                    <select x-model="type" @change="reload()"
                            class="w-full px-3 py-2 text-sm bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:bg-white/20 focus:border-white/40">
                        <option value="" class="text-gray-800">{{ __('common.all_types') }}</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" class="text-gray-800">{{ $t }}</option>
                        @endforeach
                    </select>
                    <select x-model="country" @change="reload()"
                            class="w-full px-3 py-2 text-sm bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:bg-white/20 focus:border-white/40">
                        <option value="" class="text-gray-800">{{ __('common.all_countries') }}</option>
                        @foreach($countries as $c)
                            <option value="{{ $c }}" class="text-gray-800">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Layout sidebar + carte + drawer détail --}}
        <div class="flex relative" style="height: calc(100vh - 64px - 100px); min-height: 500px;">

            <aside class="w-72 shrink-0 bg-white border-r border-gray-100 overflow-y-auto hidden lg:block z-10">
                <div class="p-3 border-b border-gray-100 sticky top-0 bg-white z-10">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <span x-text="filteredCount"></span>
                        <span x-text="filteredCount > 1 ? @js(__('carte.results')) : @js(__('carte.result'))"></span>
                    </p>
                </div>
                <div class="divide-y divide-gray-50">
                    <template x-for="a in actors" :key="a.id">
                        <button @click="openDetail(a)"
                                class="w-full text-left px-4 py-3 hover:bg-[#F8F5F0] transition-colors"
                                :class="selected === a.id ? 'actor-item-active' : ''">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#2D6A4F] flex items-center justify-center shrink-0 overflow-hidden">
                                    <template x-if="a.logo">
                                        <img :src="a.logo" :alt="a.name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!a.logo">
                                        <span class="text-white text-xs font-bold" x-text="a.name.substring(0,2).toUpperCase()"></span>
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="a.name"></p>
                                    <p class="text-xs text-gray-400 truncate">
                                        <span x-text="a.type"></span> · <span x-text="a.country"></span>
                                    </p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </button>
                    </template>
                    <template x-if="!loading && actors.length === 0">
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <p class="text-sm text-gray-500">{{ __('carte.no_results') }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ __('carte.no_geo_hint') }}</p>
                            <button @click="reset()" class="mt-3 text-xs text-[#2D6A4F] font-semibold hover:underline">{{ __('carte.reset_filters') }}</button>
                        </div>
                    </template>
                </div>
            </aside>

            <div class="flex-1 relative min-w-0">
                <div x-show="mapError" x-cloak class="absolute inset-0 z-[500] flex items-center justify-center bg-gray-100 p-6">
                    <p class="text-sm text-red-600 text-center" x-text="mapError"></p>
                </div>
                <div id="map" class="w-full h-full z-0" wire:ignore></div>

                <div x-show="loading" x-transition.opacity
                     class="absolute top-4 right-4 z-[400] bg-white rounded-full shadow-lg px-3 py-2 flex items-center gap-2 text-xs text-gray-600">
                    <svg class="w-3 h-3 animate-spin text-[#2D6A4F]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ __('carte.loading') }}
                </div>

                @if(count($legendTypes) > 0)
                <div class="absolute bottom-6 left-4 bg-white rounded-xl shadow-lg p-3 text-xs space-y-1.5 z-[400] max-h-48 overflow-y-auto">
                    <p class="font-semibold text-gray-700 mb-1.5">{{ __('carte.legend') }}</p>
                    @foreach($legendTypes as $type => $color)
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $color }}"></div>
                            <span class="text-gray-600">{{ $type }}</span>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Drawer détail (ne masque pas la carte) --}}
            <div x-show="detailOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="absolute top-0 right-0 bottom-0 w-full max-w-md bg-white border-l border-gray-200 shadow-2xl z-[450] flex flex-col"
                 @click.outside="closeDetail()"
                 style="display: none;">
                <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
                    <button type="button" @click="closeDetail()" class="flex items-center gap-1 text-sm text-gray-600 hover:text-[#2D6A4F] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ __('carte.close_detail') }}
                    </button>
                    <a :href="selectedActor?.url" target="_blank" class="text-sm text-[#2D6A4F] font-semibold hover:underline">{{ __('carte.view_profile') }}</a>
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <template x-if="detailLoading">
                        <p class="text-center text-gray-500 py-8">{{ __('carte.loading') }}</p>
                    </template>
                    <div x-show="!detailLoading" x-html="selectedActor?.html || ''"></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
