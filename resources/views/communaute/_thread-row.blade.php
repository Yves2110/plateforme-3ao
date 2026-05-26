<a href="{{ route('communaute.thread', [$category, $thread->slug]) }}"
   class="flex items-center gap-4 px-5 py-4 bg-white rounded-xl border
          {{ $pinned ? 'border-[#D4A017]/40 bg-amber-50/30' : 'border-gray-100 hover:border-[#52B788]' }}
          hover:shadow-sm transition-all group">

    {{-- Avatar auteur --}}
    <div class="w-10 h-10 rounded-full bg-[#2D6A4F] flex items-center justify-center shrink-0 text-white text-xs font-bold">
        {{ strtoupper(substr($thread->author?->name ?? 'A', 0, 2)) }}
    </div>

    {{-- Contenu --}}
    <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-0.5">
            @if($pinned)
                <span class="inline-flex items-center gap-1 text-xs text-amber-600 font-semibold">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Épinglé
                </span>
            @endif
            @if($thread->is_locked)
                <span class="text-xs text-gray-400">🔒 Verrouillé</span>
            @endif
        </div>
        <p class="font-semibold text-sm text-gray-900 group-hover:text-[#2D6A4F] transition-colors line-clamp-1">
            {{ $thread->title }}
        </p>
        <p class="text-xs text-gray-400 mt-0.5">
            par <span class="font-medium text-gray-600">{{ $thread->author?->name }}</span>
            · {{ $thread->created_at->diffForHumans() }}
        </p>
    </div>

    {{-- Stats --}}
    <div class="hidden sm:flex flex-col items-end shrink-0 text-xs text-gray-400 gap-1">
        <span class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            {{ $thread->replies->count() }}
        </span>
        <span class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            {{ $thread->views }}
        </span>
    </div>
</a>
