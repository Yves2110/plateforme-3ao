<x-app-layout>
    <x-slot name="title">{{ __('evenements.title') }}</x-slot>

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#2D6A4F] to-[#40916C] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-3xl font-bold text-white mb-2">{{ __('evenements.header_title') }}</h1>
            <p class="text-white/80">{{ __('evenements.header_subtitle') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <x-public-manage-bar
            label="Événements"
            :permissions="['creer-evenements', 'administrer-utilisateurs']"
            :create-route="route('admin.evenements.create')"
            :list-route="route('admin.evenements.index')"
        />

        {{-- Filtres rapides --}}
        <div class="flex flex-wrap gap-2 mb-8">
            @foreach([__('evenements.filter_all') => '', 'Forum' => 'Forum', 'Atelier' => 'Atelier', 'Webinaire' => 'Webinaire', __('evenements.filter_fair') => 'Foire agricole', 'Conférence' => 'Conférence'] as $label => $val)
                <a href="{{ route('evenements.index', $val ? ['type' => $val] : []) }}"
                   class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                          {{ request('type') === $val || ($val === '' && !request('type'))
                             ? 'bg-[#2D6A4F] text-white'
                             : 'bg-white text-gray-600 border border-gray-200 hover:border-[#52B788] hover:text-[#2D6A4F]' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Grille événements --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                @php $evStatus = $event->schedule()->status(); @endphp
                <div class="card group overflow-visible relative
                            {{ $evStatus === 'expired' ? 'opacity-80' : '' }}
                            {{ $evStatus === 'soon' ? 'ring-2 ring-[#F4C842] ring-offset-1' : '' }}">
                @if($evStatus === 'soon')
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#F4C842] z-20 rounded-t-2xl"></div>
                @endif
                @if(!empty($canManage))
                    <x-public-manage-card-actions
                        :item="$event"
                        :toggle-route="route('contenu.evenements.toggle', $event)"
                        published-key="is_validated"
                        :edit-route="route('admin.evenements.edit', $event)"
                    />
                @endif
                    {{-- Thumbnail --}}
                    <div class="relative h-40 overflow-hidden rounded-t-2xl bg-gradient-to-br from-[#2D6A4F] to-[#40916C] {{ $evStatus === 'expired' ? 'grayscale' : '' }}">
                        @if($event->thumbnail)
                            <img src="{{ asset('storage/'.$event->thumbnail) }}" alt="{{ $event->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        @endif
                        {{-- Date overlay --}}
                        <div class="absolute top-3 left-3 rounded-xl shadow-md px-3 py-2 text-center min-w-[52px]
                                    {{ $evStatus === 'expired' ? 'bg-gray-100' : 'bg-white' }}">
                            <div class="font-display font-bold text-xl leading-none {{ $evStatus === 'expired' ? 'text-gray-500' : 'text-[#2D6A4F]' }}">{{ $event->start_date->format('d') }}</div>
                            <div class="text-xs font-medium uppercase tracking-wide {{ $evStatus === 'expired' ? 'text-gray-400' : 'text-gray-500' }}">{{ $event->start_date->translatedFormat('M') }}</div>
                        </div>
                        <div class="absolute top-3 right-3 flex flex-col items-end gap-1.5">
                            <x-event-schedule-badge :event="$event" />
                            <span class="badge badge-evenement">{{ $event->type }}</span>
                        </div>
                        @if($event->is_online)
                            <span class="absolute bottom-3 left-3 inline-flex items-center gap-1 px-2 py-0.5 bg-blue-600 text-white text-xs font-medium rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 100 4v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 100-4V6z"/></svg>
                                {{ __('evenements.online') }}
                            </span>
                        @endif
                    </div>

                    <div class="p-5">
                        <h3 class="font-display font-semibold text-gray-900 group-hover:text-[#2D6A4F] transition-colors mb-2 text-sm leading-snug line-clamp-2">
                            <a href="{{ route('evenements.show', $event->slug) }}">{{ $event->title }}</a>
                        </h3>

                        <div class="space-y-1.5 text-xs text-gray-500">
                            {{-- Dates --}}
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#52B788] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>
                                    {{ $event->start_date->translatedFormat('d M Y') }}
                                    @if($event->end_date && $event->end_date->ne($event->start_date))
                                          {{ $event->end_date->translatedFormat('d M Y') }}
                                    @endif
                                </span>
                            </div>
                            {{-- Lieu --}}
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#52B788] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>{{ $event->is_online ? __('evenements.online') : ($event->location . ', ' . $event->country) }}</span>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('evenements.show', $event->slug) }}"
                               class="flex-1 text-center py-2 px-3 bg-[#F8F5F0] hover:bg-[#2D6A4F] hover:text-white text-[#2D6A4F] text-xs font-semibold rounded-lg transition-colors">
                                Voir le détail
                            </a>
                            @if($event->registration_url)
                                <a href="{{ $event->registration_url }}" target="_blank"
                                   class="flex-1 text-center py-2 px-3 bg-[#2D6A4F] hover:bg-[#40916C] text-white text-xs font-semibold rounded-lg transition-colors">
                                    S'inscrire
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-16 text-center text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="font-medium text-lg">{{ __('evenements.no_events') }}</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($events->hasPages())
            <div class="mt-10">{{ $events->links() }}</div>
        @endif
    </div>
</x-app-layout>
