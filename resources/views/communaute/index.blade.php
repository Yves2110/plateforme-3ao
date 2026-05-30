<x-app-layout>
    <x-slot name="title">{{ __('forum.title') }}</x-slot>

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#2D6A4F] to-[#40916C] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-display text-3xl font-bold text-white mb-1">{{ __('forum.header_title') }}</h1>
                <p class="text-white/80">{{ __('forum.header_subtitle') }}</p>
            </div>
            @auth
                <a href="{{ route('communaute.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-[#D4A017] hover:bg-[#F4C842] text-white font-semibold rounded-xl transition-colors shadow-md shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('forum.new_discussion') }}
                </a>
            @endauth
        </div>
    </div>

    {{-- Stats --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 grid grid-cols-3 divide-x divide-gray-100">
            @foreach([__('forum.stats_discussions') => $stats['threads'], __('forum.stats_replies') => $stats['replies'], __('forum.stats_members') => $stats['members']] as $label => $val)
                <div class="px-6 text-center first:pl-0 last:pr-0">
                    <div class="font-display text-2xl font-bold text-[#2D6A4F]">{{ number_format($val) }}</div>
                    <div class="text-xs text-gray-500 uppercase tracking-wide">{{ $label }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <x-public-manage-bar
            label="Forum"
            :permissions="['moderer-forum', 'administrer-utilisateurs']"
            :list-route="route('admin.forum.index')"
        />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ===== Catégories ===== --}}
            <div class="lg:col-span-2">
                <h2 class="font-display font-bold text-xl text-gray-800 mb-4">{{ __('forum.categories_title') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach([
                        ['slug' => 'pratiques', 'title' => 'Pratiques agroécologiques', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945', 'desc' => 'Techniques de terrain, compostage, agroforesterie…'],
                        ['slug' => 'semences',    'title' => 'Semences & Biodiversité',     'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'desc' => 'Conservation, échange et valorisation'],
                        ['slug' => 'politique', 'title' => 'Politique & Plaidoyer',       'icon' => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4', 'desc' => 'Cadres politiques et actions collectives'],
                        ['slug' => 'marches',     'title' => 'Marchés & Filières',          'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'desc' => 'Circuits courts, prix et commercialisation'],
                        ['slug' => 'formation', 'title' => 'Formation & Éducation',       'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13', 'desc' => 'Formations et ressources pédagogiques'],
                        ['slug' => 'financement', 'title' => 'Financement & Projets',       'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1', 'desc' => 'Appels à projets et opportunités de financement'],
                    ] as $cat)
                        <a href="{{ route('communaute.category', $cat['slug']) }}"
                           class="flex gap-4 p-5 bg-white rounded-2xl border border-gray-100 hover:border-[#52B788] hover:shadow-sm transition-all group">
                            <div class="w-11 h-11 bg-[#F8F5F0] group-hover:bg-[#2D6A4F] rounded-xl flex items-center justify-center shrink-0 transition-colors">
                                <svg class="w-5 h-5 text-[#2D6A4F] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $cat['icon'] }}"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-sm text-gray-900 group-hover:text-[#2D6A4F] transition-colors mb-0.5">{{ $cat['title'] }}</h3>
                                <p class="text-xs text-gray-500">{{ $cat['desc'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ===== Sidebar : dernières discussions ===== --}}
            <div>
                <h2 class="font-display font-bold text-xl text-gray-800 mb-4">{{ __('forum.recent_discussions') }}</h2>
                <div class="space-y-3">
                    @forelse($recentThreads as $thread)
                        <a href="{{ route('communaute.thread', [$thread->category, $thread->slug]) }}"
                           class="flex gap-3 p-4 bg-white rounded-xl border border-gray-100 hover:border-[#52B788] hover:shadow-sm transition-all group">
                            <div class="w-9 h-9 rounded-full bg-[#2D6A4F] flex items-center justify-center shrink-0 text-white text-xs font-bold">
                                {{ strtoupper(substr($thread->author?->name ?? 'A', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 group-hover:text-[#2D6A4F] transition-colors line-clamp-1">{{ $thread->title }}</p>
                                <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-400">
                                    <span>{{ $thread->replies->count() }} réponse(s)</span>
                                    <span>·</span>
                                    <span>{{ $thread->last_reply_at?->diffForHumans() }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400 py-4 text-center">Aucune discussion pour l'instant.</p>
                    @endforelse
                </div>

                @auth
                    <div class="mt-6 p-4 bg-[#F8F5F0] rounded-2xl border border-[#B7E4C7]">
                        <p class="text-sm font-semibold text-[#2D6A4F] mb-2">Envie de partager ?</p>
                        <p class="text-xs text-gray-600 mb-3">Lancez une discussion et échangez avec la communauté.</p>
                        <a href="{{ route('communaute.create') }}" class="btn-primary text-sm py-2 w-full justify-center">
                            Créer une discussion
                        </a>
                    </div>
                @else
                    <div class="mt-6 p-4 bg-[#F8F5F0] rounded-2xl">
                        <p class="text-xs text-gray-600 mb-2">
                            <a href="{{ route('login') }}" class="text-[#2D6A4F] font-semibold hover:underline">Connectez-vous</a> pour participer aux discussions.
                        </p>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>
