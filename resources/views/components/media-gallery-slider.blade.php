@props(['slides' => []])

@if(count($slides) > 0)
<div class="relative rounded-2xl overflow-hidden min-h-[320px] sm:min-h-[380px] mb-10 shadow-lg"
     x-data="{
         slides: @js($slides),
         current: 0,
         timer: null,
         start() {
             if (this.slides.length <= 1) return;
             this.timer = setInterval(() => { this.current = (this.current + 1) % this.slides.length; }, 5500);
         },
         goTo(i) { this.current = i; if (this.timer) clearInterval(this.timer); this.start(); }
     }"
     x-init="start()">

    <template x-for="(slide, index) in slides" :key="index">
        <div x-show="current === index"
             x-transition:enter="transition-opacity duration-700 ease-out"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="absolute inset-0 bg-gradient-to-br from-[#2D6A4F] to-[#40916C]">
            <template x-if="slide.mode === 'logo'">
                <div class="absolute inset-0 flex items-center justify-center p-10">
                    <img :src="slide.url" :alt="slide.alt || slide.title"
                         class="max-h-[55%] max-w-[min(100%,280px)] object-contain drop-shadow-xl"
                         loading="lazy">
                </div>
            </template>
            <template x-if="slide.mode !== 'logo'">
                <img :src="slide.url" :alt="slide.alt || slide.title"
                     class="w-full h-full object-cover" loading="lazy">
            </template>
            <div class="absolute inset-0 bg-gradient-to-t from-[#1A1A2E]/90 via-[#1A1A2E]/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-8 z-10">
                <h2 class="font-display text-xl sm:text-2xl font-bold text-white mb-3 max-w-2xl" x-text="slide.title"></h2>
                <a :href="slide.href"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#D4A017] hover:bg-[#F4C842] text-[#1A1A2E] text-sm font-semibold rounded-xl transition-colors">
                    Voir le média
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </template>

    <div class="absolute bottom-4 right-4 z-20 flex items-center gap-2" x-show="slides.length > 1">
        <template x-for="(_, index) in slides" :key="'dot-'+index">
            <button type="button" @click="goTo(index)"
                    class="h-2 rounded-full transition-all"
                    :class="current === index ? 'w-6 bg-[#F4C842]' : 'w-2 bg-white/50 hover:bg-white/80'"
                    :aria-label="'Slide ' + (index + 1)"></button>
        </template>
    </div>
</div>
@endif
