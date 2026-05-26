<x-app-layout>
    <x-slot name="title">{{ $event->title }}</x-slot>

    {{-- Fil d'Ariane --}}
    <div class="bg-[#F8F5F0] border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-[#2D6A4F]">Accueil</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('evenements.index') }}" class="hover:text-[#2D6A4F]">Événements</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#2D6A4F] font-medium">{{ Str::limit($event->title, 50) }}</span>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- ===== Contenu principal ===== --}}
            <div class="lg:col-span-2">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="badge badge-evenement">{{ $event->type }}</span>
                    @if($event->is_online)
                        <span class="badge bg-blue-100 text-blue-700">En ligne</span>
                    @endif
                </div>

                <h1 class="font-display text-2xl sm:text-3xl font-bold text-[#1A1A2E] mb-6 leading-tight">
                    {{ $event->title }}
                </h1>

                @if($event->thumbnail)
                    <img src="{{ asset('storage/'.$event->thumbnail) }}" alt="{{ $event->title }}"
                         class="w-full h-56 object-cover rounded-2xl mb-6">
                @endif

                @if($event->description)
                    <div class="prose prose-green max-w-none text-gray-700 leading-relaxed">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                @endif

                {{-- Formulaire d'inscription --}}
                @guest
                    <div class="mt-10 p-5 bg-[#F8F5F0] rounded-2xl border border-[#B7E4C7]">
                        <p class="text-sm text-gray-700 mb-3">
                            <a href="{{ route('login') }}" class="text-[#2D6A4F] font-semibold hover:underline">Connectez-vous</a>
                            ou <a href="{{ route('register') }}" class="text-[#2D6A4F] font-semibold hover:underline">créez un compte</a>
                            pour vous inscrire à cet événement.
                        </p>
                    </div>
                @else
                    <div class="mt-10 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="font-display font-semibold text-lg text-gray-800 mb-4">S'inscrire à cet événement</h2>
                        @if(session('success'))
                            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form action="{{ route('evenements.inscription', $event->slug) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                                    <input type="text" name="name" value="{{ auth()->user()->name }}" required
                                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#52B788] text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" value="{{ auth()->user()->email }}" required
                                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#52B788] text-sm">
                                </div>
                            </div>
                            <button type="submit" class="btn-primary w-full justify-center">
                                Confirmer mon inscription
                            </button>
                        </form>
                    </div>
                @endguest
            </div>

            {{-- ===== Sidebar info ===== --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-24 space-y-4">
                    <h3 class="font-display font-semibold text-gray-800 border-b border-gray-100 pb-3">Informations</h3>

                    <div class="space-y-4 text-sm">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-[#F8F5F0] rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Date(s)</p>
                                <p class="font-semibold text-gray-800">{{ $event->start_date->translatedFormat('d F Y') }}</p>
                                @if($event->end_date && $event->end_date->ne($event->start_date))
                                    <p class="text-gray-500">→ {{ $event->end_date->translatedFormat('d F Y') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-[#F8F5F0] rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Lieu</p>
                                <p class="font-semibold text-gray-800">
                                    {{ $event->is_online ? 'Événement en ligne' : ($event->location ?? 'N/A') }}
                                </p>
                                @if(!$event->is_online && $event->country)
                                    <p class="text-gray-500">{{ $event->country }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-4 border-t border-gray-100 space-y-2">
                        @if($event->registration_url)
                            <a href="{{ $event->registration_url }}" target="_blank"
                               class="btn-primary w-full justify-center text-sm py-2.5">
                                S'inscrire (externe)
                            </a>
                        @endif
                        <a href="{{ route('evenements.index') }}"
                           class="block text-center py-2 text-sm text-gray-500 hover:text-[#2D6A4F] transition-colors">
                            ← Retour aux événements
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
