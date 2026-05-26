<x-app-layout>
    <x-slot name="title">Profil de {{ $user->name }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 py-10">

        {{-- En-tête profil --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-5">
            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                 class="w-20 h-20 rounded-full object-cover ring-2 ring-[#52B788] ring-offset-2">
            <div class="flex-1">
                <h1 class="text-2xl font-display font-bold text-[#1A1A2E]">{{ $user->name }}</h1>
                @if($user->organization)
                    <p class="text-sm text-[#2D6A4F] font-medium mt-0.5">{{ $user->organization }}</p>
                @endif
                @if($user->country)
                    <p class="text-sm text-gray-500 mt-0.5">📍 {{ $user->country }}</p>
                @endif
                @if($user->bio)
                    <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ $user->bio }}</p>
                @endif
            </div>
            <div class="flex gap-3 flex-shrink-0 text-center">
                <div class="px-4 py-2 bg-[#F8F5F0] rounded-xl">
                    <div class="text-lg font-bold text-[#2D6A4F]">{{ $user->threads()->where('is_validated', true)->count() }}</div>
                    <div class="text-xs text-gray-500">Discussions</div>
                </div>
                <div class="px-4 py-2 bg-[#F8F5F0] rounded-xl">
                    <div class="text-lg font-bold text-[#2D6A4F]">{{ $user->ressources()->where('is_validated', true)->count() }}</div>
                    <div class="text-xs text-gray-500">Ressources</div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">

            {{-- Dernières discussions --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-base font-semibold text-[#1A1A2E] mb-3 flex items-center gap-2">
                    <span class="text-[#2D6A4F]">💬</span> Dernières discussions
                </h2>
                @forelse($threads as $thread)
                    <a href="{{ route('thread.show', [$thread->category, $thread->slug]) }}"
                       class="block py-2 border-b border-gray-50 last:border-0 text-sm text-gray-700 hover:text-[#2D6A4F] transition-colors">
                        {{ Str::limit($thread->title, 60) }}
                        <span class="text-xs text-gray-400 block">{{ $thread->created_at->diffForHumans() }}</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-400">Aucune discussion publiée.</p>
                @endforelse
            </div>

            {{-- Dernières ressources --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-base font-semibold text-[#1A1A2E] mb-3 flex items-center gap-2">
                    <span class="text-[#2D6A4F]">📚</span> Ressources partagées
                </h2>
                @forelse($ressources as $ressource)
                    <a href="{{ route('bibliotheque.show', $ressource->slug) }}"
                       class="block py-2 border-b border-gray-50 last:border-0 text-sm text-gray-700 hover:text-[#2D6A4F] transition-colors">
                        {{ Str::limit($ressource->title, 60) }}
                        <span class="text-xs text-gray-400 block">{{ ucfirst($ressource->type) }} · {{ $ressource->created_at->diffForHumans() }}</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-400">Aucune ressource partagée.</p>
                @endforelse
            </div>

        </div>

    </div>
</x-app-layout>
