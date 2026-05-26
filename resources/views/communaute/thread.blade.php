<x-app-layout>
    <x-slot name="title">{{ $forumThread->title }}</x-slot>

    {{-- Fil d'Ariane --}}
    <div class="bg-[#F8F5F0] border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-wrap items-center gap-1.5 text-sm text-gray-500">
            <a href="{{ route('communaute.index') }}" class="hover:text-[#2D6A4F]">Communauté</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('communaute.category', $category) }}" class="hover:text-[#2D6A4F]">{{ $categoryName }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#2D6A4F] font-medium line-clamp-1">{{ Str::limit($forumThread->title, 50) }}</span>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- ===== Thread principal ===== --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-6">
            {{-- En-tête --}}
            <div class="p-6 border-b border-gray-50">
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($forumThread->is_pinned)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold bg-amber-100 text-amber-700 rounded-full">
                            ⭐ Épinglé
                        </span>
                    @endif
                    @if($forumThread->is_locked)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full">
                            🔒 Verrouillé
                        </span>
                    @endif
                    <span class="px-2.5 py-0.5 text-xs font-semibold bg-[#F8F5F0] text-[#2D6A4F] rounded-full">{{ $categoryName }}</span>
                </div>
                <h1 class="font-display text-xl sm:text-2xl font-bold text-[#1A1A2E] leading-tight mb-4">{{ $forumThread->title }}</h1>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#2D6A4F] flex items-center justify-center text-white text-sm font-bold shrink-0">
                        {{ strtoupper(substr($forumThread->author?->name ?? 'A', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $forumThread->author?->name }}</p>
                        <p class="text-xs text-gray-400">{{ $forumThread->created_at->translatedFormat('d F Y à H:i') }} · {{ $forumThread->views }} vue(s)</p>
                    </div>
                </div>
            </div>
            {{-- Corps --}}
            <div class="p-6 prose prose-green max-w-none text-gray-700 leading-relaxed">
                {!! nl2br(e($forumThread->body)) !!}
            </div>

            {{-- Actions modération --}}
            @can('moderer-forum')
                <div class="px-6 pb-4 flex flex-wrap gap-2">
                    <form action="{{ route('communaute.moderate', [$forumThread->is_pinned ? 'unpin' : 'pin', $forumThread->id]) }}" method="POST">
                        @csrf
                        <button class="px-3 py-1.5 text-xs bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100">
                            {{ $forumThread->is_pinned ? '📌 Désépingler' : '📌 Épingler' }}
                        </button>
                    </form>
                    <form action="{{ route('communaute.moderate', [$forumThread->is_locked ? 'unlock' : 'lock', $forumThread->id]) }}" method="POST">
                        @csrf
                        <button class="px-3 py-1.5 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                            {{ $forumThread->is_locked ? '🔓 Déverrouiller' : '🔒 Verrouiller' }}
                        </button>
                    </form>
                    <form action="{{ route('communaute.moderate', ['delete', $forumThread->id]) }}" method="POST"
                          onsubmit="return confirm('Supprimer définitivement cette discussion ?')">
                        @csrf
                        <button class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100">🗑 Supprimer</button>
                    </form>
                </div>
            @endcan
        </div>

        {{-- ===== Sondage ===== --}}
        @if($forumThread->poll)
            @php
                $poll = $forumThread->poll;
                $totalVotes = $poll->votes->count();
                $isClosed = $poll->closes_at && $poll->closes_at->isPast();
            @endphp
            <div class="bg-white rounded-2xl border border-[#B7E4C7] p-6 mb-6">
                <h3 class="font-display font-semibold text-gray-800 mb-1 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#52B788]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Sondage
                </h3>
                <p class="text-sm font-medium text-gray-700 mb-4">{{ $poll->question }}</p>

                @if($userVote || $isClosed || !auth()->check())
                    {{-- Résultats --}}
                    <div class="space-y-3">
                        @foreach($poll->options as $i => $option)
                            @php
                                $count = $poll->votes->where('option_index', $i)->count();
                                $pct = $totalVotes > 0 ? round($count / $totalVotes * 100) : 0;
                                $isMyVote = $userVote?->option_index === $i;
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1 text-sm">
                                    <span class="{{ $isMyVote ? 'font-semibold text-[#2D6A4F]' : 'text-gray-700' }}">
                                        {{ $option }}{{ $isMyVote ? ' ✓' : '' }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $pct }}% ({{ $count }})</span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $isMyVote ? 'bg-[#2D6A4F]' : 'bg-[#52B788]' }} rounded-full transition-all"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-3">{{ $totalVotes }} vote(s) · {{ $isClosed ? 'Sondage clos' : ($userVote ? 'Vous avez déjà voté' : 'Connectez-vous pour voter') }}</p>
                @else
                    {{-- Formulaire vote --}}
                    <form action="{{ route('communaute.vote', [$category, $forumThread->slug]) }}" method="POST" class="space-y-2">
                        @csrf
                        @foreach($poll->options as $i => $option)
                            <label class="flex items-center gap-3 p-3 bg-[#F8F5F0] rounded-xl cursor-pointer hover:bg-[#E8F5E9] transition-colors">
                                <input type="radio" name="option_index" value="{{ $i }}" class="text-[#2D6A4F]" required>
                                <span class="text-sm text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                        <button type="submit" class="btn-primary text-sm py-2 mt-2">Voter</button>
                    </form>
                @endif
            </div>
        @endif

        {{-- ===== Réponses ===== --}}
        <div class="mb-6">
            <h2 class="font-display font-semibold text-gray-800 mb-4">
                {{ $replies->total() }} réponse(s)
            </h2>

            <div class="space-y-4">
                @forelse($replies as $reply)
                    <div id="reply-{{ $reply->id }}" class="bg-white rounded-2xl border {{ $reply->is_solution ? 'border-[#52B788] bg-green-50/20' : 'border-gray-100' }} overflow-hidden">
                        <div class="p-5">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#40916C] flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($reply->author?->name ?? 'A', 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <span class="text-sm font-semibold text-gray-800">{{ $reply->author?->name }}</span>
                                        @if($reply->is_solution)
                                            <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 font-semibold rounded-full">✓ Solution</span>
                                        @endif
                                        <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-sm text-gray-700 leading-relaxed">
                                        {!! nl2br(e($reply->body)) !!}
                                    </div>

                                    {{-- Réponses imbriquées --}}
                                    @if($reply->children->count())
                                        <div class="mt-4 pl-4 border-l-2 border-gray-100 space-y-3">
                                            @foreach($reply->children as $child)
                                                <div class="flex gap-2">
                                                    <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-bold shrink-0">
                                                        {{ strtoupper(substr($child->author?->name ?? 'A', 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <span class="text-xs font-semibold text-gray-700">{{ $child->author?->name }}</span>
                                                        <span class="text-xs text-gray-400 ml-1">{{ $child->created_at->diffForHumans() }}</span>
                                                        <p class="text-xs text-gray-600 mt-0.5">{{ $child->body }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Bouton répondre inline --}}
                                    @auth
                                        @unless($forumThread->is_locked)
                                            <button x-data x-on:click="$dispatch('open-reply', {parentId: {{ $reply->id }}, author: '{{ addslashes($reply->author?->name) }}'})"
                                                    class="mt-2 text-xs text-[#2D6A4F] hover:underline font-medium">
                                                Répondre
                                            </button>
                                        @endunless
                                    @endauth
                                </div>

                                {{-- Actions modération réponse --}}
                                @can('moderer-forum')
                                    <form action="{{ route('communaute.moderate', ['delete-reply', $reply->id]) }}" method="POST"
                                          onsubmit="return confirm('Supprimer cette réponse ?')">
                                        @csrf
                                        <button class="text-xs text-red-400 hover:text-red-600">🗑</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6">Aucune réponse pour l'instant. Soyez le premier !</p>
                @endforelse
            </div>

            @if($replies->hasPages())
                <div class="mt-6">{{ $replies->links() }}</div>
            @endif
        </div>

        {{-- ===== Formulaire de réponse ===== --}}
        @auth
            @unless($forumThread->is_locked)
                <div class="bg-white rounded-2xl border border-gray-100 p-6"
                     x-data="{ parentId: null, parentAuthor: '' }"
                     x-on:open-reply.window="parentId = $event.detail.parentId; parentAuthor = $event.detail.author; $el.scrollIntoView({behavior:'smooth'})">

                    <h3 class="font-display font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#52B788]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        <span x-text="parentAuthor ? 'Répondre à ' + parentAuthor : 'Laisser une réponse'">Laisser une réponse</span>
                    </h3>

                    <form action="{{ route('communaute.reply', [$category, $forumThread->slug]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="parent_id" x-bind:value="parentId">
                        <div x-show="parentAuthor" class="mb-3 px-3 py-2 bg-[#F8F5F0] rounded-lg text-xs text-gray-500 flex items-center justify-between">
                            <span>En réponse à <strong x-text="parentAuthor"></strong></span>
                            <button type="button" x-on:click="parentId = null; parentAuthor = ''" class="text-gray-400 hover:text-gray-600">✕</button>
                        </div>
                        <textarea name="body" rows="4" required minlength="5" placeholder="Partagez votre expérience ou posez une question…"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#52B788] text-sm resize-none"></textarea>
                        @error('body')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <div class="mt-3 flex justify-end">
                            <button type="submit" class="btn-primary">Publier la réponse</button>
                        </div>
                    </form>
                </div>
            @else
                <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-500 text-center">
                    🔒 Cette discussion est verrouillée. Les nouvelles réponses ne sont plus acceptées.
                </div>
            @endunless
        @else
            <div class="p-5 bg-[#F8F5F0] rounded-2xl text-sm text-center text-gray-600">
                <a href="{{ route('login') }}" class="text-[#2D6A4F] font-semibold hover:underline">Connectez-vous</a>
                pour participer à cette discussion.
            </div>
        @endauth
    </div>
</x-app-layout>
