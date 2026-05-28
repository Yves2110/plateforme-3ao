@if(isset($featuredEvent) && $featuredEvent)
@php $featuredSoon = $featuredEvent->schedule()->isSoon(); @endphp
<div x-data="{
        dismissed: localStorage.getItem('featured-event-{{ $featuredEvent->id }}') === '1',
        close() {
            this.dismissed = true;
            localStorage.setItem('featured-event-{{ $featuredEvent->id }}', '1');
        }
    }"
     x-show="!dismissed"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-cloak
     class="fixed top-20 left-4 right-4 z-50 md:left-auto md:right-6 md:max-w-md pointer-events-none">
    <div class="pointer-events-auto bg-white rounded-2xl shadow-2xl overflow-hidden ring-4
                {{ $featuredSoon ? 'border-2 border-[#F4C842] ring-[#F4C842]/30' : 'border border-gray-200 ring-gray-200/50' }}">
        @if($featuredSoon)
            <div class="h-1.5 bg-[#F4C842]"></div>
        @endif
        <div class="bg-gradient-to-r from-[#2D6A4F] to-[#40916C] px-4 py-2 flex items-center justify-between gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-[#F4C842] flex items-center gap-1.5">
                <span class="w-2 h-2 bg-[#F4C842] rounded-full animate-pulse"></span>
                @if($featuredSoon)
                    {{ $featuredEvent->schedule()->label() }}
                @else
                    {{ __('evenements.upcoming') }}
                @endif
            </span>
            <button type="button" @click="close()" class="p-1 text-white/80 hover:text-white rounded-lg hover:bg-white/10" aria-label="Fermer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4">
            <div class="flex gap-3">
                <div class="shrink-0 w-14 text-center bg-[#F8F5F0] rounded-xl py-2">
                    <div class="font-display font-bold text-xl text-[#2D6A4F] leading-none">{{ $featuredEvent->start_date->format('d') }}</div>
                    <div class="text-[10px] font-semibold text-gray-500 uppercase">{{ $featuredEvent->start_date->translatedFormat('M') }}</div>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-semibold text-[#2D6A4F] uppercase">{{ $featuredEvent->type }}</span>
                    <p class="font-semibold text-sm text-gray-900 line-clamp-2 leading-snug mt-0.5">{{ $featuredEvent->title }}</p>
                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        {{ $featuredEvent->is_online ? 'En ligne' : trim(($featuredEvent->location ? $featuredEvent->location.', ' : '').($featuredEvent->country ?? '')) }}
                    </p>
                </div>
            </div>
            <a href="{{ route('evenements.show', $featuredEvent->slug) }}"
               class="mt-3 flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-[#D4A017] hover:bg-[#F4C842] text-white text-sm font-semibold rounded-xl transition-colors">
                Voir les détails
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>
@endif
