<x-app-layout>
    <x-slot name="title">{{ __('membres.dashboard_title') }}</x-slot>

    <div class="max-w-5xl mx-auto px-4 py-10">

        <div class="flex items-center gap-4 mb-8">
            <img src="{{ $user->profile_photo_url }}" class="w-14 h-14 rounded-full ring-2 ring-[#52B788] ring-offset-2" alt="">
            <div>
                <h1 class="text-2xl font-display font-bold text-[#1A1A2E] flex items-center gap-2">
                    Bonjour, {{ $user->name }}
                    <x-icon name="wave" class="w-6 h-6 text-[#2D6A4F]" />
                </h1>
                <p class="text-sm text-gray-500">Membre depuis {{ $user->created_at->translatedFormat('F Y') }}</p>
            </div>
            <a href="{{ route('membre.show', $user) }}" class="ml-auto text-sm text-[#2D6A4F] hover:underline font-medium">
                Voir mon profil public →
            </a>
        </div>

        @php
            $quickLinks = array_filter([
                auth()->user()->can('publier-bibliotheque') ? ['label' => 'Ajouter une ressource', 'route' => 'admin.ressources.create', 'icon' => 'book'] : null,
                auth()->user()->can('soumettre-acteur') || auth()->user()->can('gerer-carte') ? ['label' => 'Ajouter un acteur', 'route' => 'admin.acteurs.create', 'icon' => 'map'] : null,
                auth()->user()->can('creer-evenements') ? ['label' => 'Créer un événement', 'route' => 'admin.evenements.create', 'icon' => 'calendar'] : null,
                auth()->user()->can('contribuer-multimedia') ? ['label' => 'Ajouter un média', 'route' => 'admin.medias.create', 'icon' => 'film'] : null,
                auth()->user()->can('publier-actualites') ? ['label' => 'Publier une actualité', 'route' => 'admin.actualites.create', 'icon' => 'news'] : null,
            ]);
        @endphp

        @if(count($quickLinks))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-8">
            <h2 class="font-semibold text-[#1A1A2E] mb-4 flex items-center gap-2">
                <x-icon name="target" class="w-5 h-5 text-[#D4A017]" />
                Actions rapides
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($quickLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-[#52B788] hover:bg-[#F8F5F0] transition-colors">
                        <x-icon :name="$link['icon']" class="w-5 h-5 text-[#2D6A4F]" />
                        <span class="text-sm font-medium text-gray-700">{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>
            @if(auth()->user()->canAccessBackOffice())
                <a href="{{ route('admin.dashboard') }}" class="inline-block mt-4 text-sm text-[#2D6A4F] font-semibold hover:underline">Accéder au back-office →</a>
            @elseif(auth()->user()->canValidateRegistrations())
                <a href="{{ route('admin.users.pending') }}" class="inline-block mt-4 text-sm text-[#2D6A4F] font-semibold hover:underline">Valider les inscriptions en attente →</a>
            @endif
        </div>
        @endif

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            @foreach([
                ['label' => 'Discussions', 'count' => $threads->count(), 'icon' => 'chat'],
                ['label' => 'Ressources', 'count' => $ressources->count(), 'icon' => 'book'],
                ['label' => 'Actualités', 'count' => $actualites->count(), 'icon' => 'news'],
            ] as $kpi)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="flex justify-center mb-1"><x-icon :name="$kpi['icon']" class="w-6 h-6 text-[#2D6A4F]" /></div>
                <div class="text-2xl font-bold text-[#2D6A4F]">{{ $kpi['count'] }}</div>
                <div class="text-xs text-gray-500">{{ $kpi['label'] }}</div>
            </div>
            @endforeach
            <a href="{{ route('learning.dashboard') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center hover:border-[#52B788] hover:bg-[#F8F5F0] transition-colors flex flex-col items-center justify-center gap-1">
                <x-icon name="graduation" class="w-6 h-6 text-[#2D6A4F]" />
                <div class="text-sm font-semibold text-[#2D6A4F]">Mon apprentissage</div>
            </a>
        </div>

        <div class="flex flex-wrap gap-3 mb-8">
            <a href="{{ route('communaute.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                <x-icon name="news" class="w-5 h-5" />
                Nouvelle discussion
            </a>
            <a href="{{ route('formation.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                <x-icon name="graduation" class="w-5 h-5 text-[#2D6A4F]" />
                Parcourir les formations
            </a>
        </div>

        @if($threads->count())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6">
            <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2">
                <x-icon name="chat" class="w-5 h-5 text-[#2D6A4F]" />
                <h2 class="font-semibold text-[#1A1A2E]">Mes discussions</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($threads as $thread)
                <a href="{{ route('thread.show', [$thread->category, $thread->slug]) }}"
                   class="flex items-center justify-between px-5 py-3 hover:bg-[#F8F5F0] transition-colors">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ Str::limit($thread->title, 70) }}</p>
                        <p class="text-xs text-gray-400">{{ $thread->category }} · {{ $thread->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full {{ $thread->is_validated ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $thread->is_validated ? 'Publié' : 'En attente' }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($ressources->count())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6">
            <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2">
                <x-icon name="book" class="w-5 h-5 text-[#2D6A4F]" />
                <h2 class="font-semibold text-[#1A1A2E]">Mes ressources</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($ressources as $r)
                <a href="{{ route('bibliotheque.show', $r->slug) }}"
                   class="flex items-center justify-between px-5 py-3 hover:bg-[#F8F5F0] transition-colors">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ Str::limit($r->title, 70) }}</p>
                        <p class="text-xs text-gray-400">{{ ucfirst($r->type) }} · {{ $r->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full {{ $r->is_validated ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $r->is_validated ? 'Validée' : 'En attente' }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-5 mt-8">
            <h2 class="font-semibold text-red-700 mb-1 flex items-center gap-2">
                <x-icon name="warning" class="w-5 h-5" />
                Zone de danger
            </h2>
            <p class="text-sm text-gray-500 mb-4">La suppression de votre compte est irréversible. Toutes vos données personnelles seront effacées sous 30 jours.</p>
            <form method="POST" action="{{ route('membre.delete') }}"
                  x-data="{ open: false }"
                  @submit.prevent="if(open) $el.submit(); else open = true">
                @csrf
                @method('DELETE')
                <div x-show="open" class="mb-3">
                    <label class="text-sm font-medium text-gray-700">Confirmez votre mot de passe</label>
                    <input type="password" name="password" placeholder="Mot de passe actuel"
                           class="mt-1 w-full max-w-xs px-3 py-2 text-sm border border-red-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                    :class="open ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-red-50 hover:bg-red-100 text-red-600'"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors">
                    <span x-text="open ? 'Confirmer la suppression définitive' : 'Supprimer mon compte'"></span>
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
