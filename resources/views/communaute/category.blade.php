<x-app-layout>
    <x-slot name="title">{{ $categoryName }} · Forum 3AO</x-slot>

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#2D6A4F] to-[#40916C] py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('communaute.index') }}" class="text-white/70 hover:text-white text-sm mb-1 block">← Communauté</a>
                <h1 class="font-display text-2xl font-bold text-white">{{ $categoryName }}</h1>
            </div>
            @auth
                <a href="{{ route('communaute.create') }}?category={{ $category }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-[#D4A017] hover:bg-[#F4C842] text-white font-semibold rounded-xl transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouvelle discussion
                </a>
            @endauth
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <x-public-manage-bar
            label="Forum · {{ $categoryName }}"
            :permissions="['moderer-forum', 'administrer-utilisateurs']"
            :list-route="route('admin.forum.index')"
        />

        {{-- Threads épinglés --}}
        @php $pinned = $threads->filter(fn($t) => $t->is_pinned); @endphp
        @if($pinned->count())
            <div class="mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Épinglés</p>
                @foreach($pinned as $thread)
                    @include('communaute._thread-row', ['thread' => $thread, 'category' => $category, 'pinned' => true])
                @endforeach
            </div>
            <hr class="my-4 border-gray-100">
        @endif

        {{-- Tous les threads --}}
        <div class="space-y-2">
            @forelse($threads->reject(fn($t) => $t->is_pinned) as $thread)
                @include('communaute._thread-row', ['thread' => $thread, 'category' => $category, 'pinned' => false])
            @empty
                <div class="py-16 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <p class="font-medium">Aucune discussion dans cette catégorie.</p>
                    @auth
                        <a href="{{ route('communaute.create') }}" class="btn-primary mt-4 text-sm py-2">Lancer la première discussion</a>
                    @endauth
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($threads->hasPages())
            <div class="mt-8">{{ $threads->links() }}</div>
        @endif
    </div>
</x-app-layout>
