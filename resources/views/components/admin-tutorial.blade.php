@props([
    'steps' => [],
    'showOnLoad' => false,
    'completeUrl' => '',
    'roleLabel' => '',
])

@once
<style>
    .admin-tutorial-nav-target {
        position: relative !important;
        z-index: 10002 !important;
        outline: 3px solid #F4C842 !important;
        outline-offset: 2px !important;
        box-shadow: 0 0 0 4px rgba(244, 200, 66, 0.45) !important;
        border-radius: 12px !important;
        background-color: rgba(82, 183, 136, 0.35) !important;
        color: #fff !important;
    }
</style>
<script>
function createAdminGuide(steps, showOnLoad, completeUrl) {
    return {
        active: showOnLoad,
        step: 0,
        hole: null,
        cardPos: { top: '50%', left: '50%', width: '360px', transform: 'translate(-50%, -50%)' },
        steps: steps,
        completeUrl: completeUrl,
        highlightedEl: null,
        get current() { return this.steps[this.step] ?? {}; },
        get isCenter() { return !this.current?.target; },
        get isLast() { return this.step >= this.steps.length - 1; },
        clearHighlight() {
            if (this.highlightedEl) {
                this.highlightedEl.classList.remove('admin-tutorial-nav-target');
                this.highlightedEl = null;
            }
            this.hole = null;
        },
        positionCard() {
            const cardW = 360;
            const cardH = Math.min(420, window.innerHeight - 32);
            const pad = 16;
            const sidebar = window.innerWidth >= 1024 ? 256 : 0;

            if (this.isCenter || !this.hole) {
                this.cardPos = {
                    top: '50%',
                    left: '50%',
                    width: cardW + 'px',
                    transform: 'translate(-50%, -50%)'
                };
                return;
            }

            const h = this.hole;
            const vh = window.innerHeight;
            const vw = window.innerWidth;

            let left = h.left + h.width + 20;
            let top = h.top + h.height / 2 - cardH / 2;

            if (left + cardW > vw - pad) {
                left = Math.max(pad, (vw - cardW) / 2);
                top = h.top + h.height + 14;
            }

            if (left < sidebar + pad) {
                left = sidebar + pad;
            }

            top = Math.max(pad, Math.min(top, vh - cardH - pad));
            left = Math.max(pad, Math.min(left, vw - cardW - pad));

            this.cardPos = {
                top: top + 'px',
                left: left + 'px',
                width: cardW + 'px',
                transform: 'none'
            };
        },
        measureHole() {
            if (!this.current?.target) {
                this.hole = null;
                this.positionCard();
                return;
            }
            const el = document.getElementById(this.current.target);
            if (!el) {
                if (this.step < this.steps.length - 1) {
                    this.step++;
                    this.measureHole();
                }
                return;
            }
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => {
                const r = el.getBoundingClientRect();
                const p = 6;
                this.hole = {
                    top: Math.max(0, r.top - p),
                    left: Math.max(0, r.left - p),
                    width: r.width + p * 2,
                    height: r.height + p * 2,
                    label: this.current.label || this.current.title
                };
                el.classList.add('admin-tutorial-nav-target');
                this.highlightedEl = el;
                this.positionCard();
            }, 280);
        },
        goStep(n) {
            this.clearHighlight();
            this.step = n;
            this.$nextTick(() => this.measureHole());
        },
        next() {
            if (this.isLast) this.finish();
            else this.goStep(this.step + 1);
        },
        prev() {
            if (this.step > 0) this.goStep(this.step - 1);
        },
        async persistComplete() {
            if (!this.completeUrl) return;
            try {
                await fetch(this.completeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '',
                        'Accept': 'application/json',
                    },
                });
            } catch (e) {
                /* ignore */
            }
        },
        async finish() {
            this.clearHighlight();
            await this.persistComplete();
            this.active = false;
        },
        skip() { this.finish(); },
        restart() {
            this.step = 0;
            this.active = true;
            this.$nextTick(() => this.measureHole());
        }
    };
}
</script>
@endonce

<div
    x-data="createAdminGuide(@js($steps), @js($showOnLoad), @js($completeUrl))"
    x-show="active"
    x-cloak
    x-init="if (active && steps.length) $nextTick(() => measureHole())"
    class="fixed inset-0 z-[9999]"
    @admin-tutorial-restart.window="restart()"
    @keydown.escape.window="if (active) skip()"
    @keydown.arrow-right.window.prevent="if (active) next()"
    @keydown.arrow-left.window.prevent="if (active) prev()"
>
    <template x-if="active && isCenter">
        <div class="absolute inset-0 bg-black/65 backdrop-blur-sm" @click="skip()"></div>
    </template>

    <template x-if="active && !isCenter && hole">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute left-0 right-0 top-0 bg-black/65 pointer-events-auto" :style="'height:' + hole.top + 'px'" @click="skip()"></div>
            <div class="absolute left-0 bg-black/65 pointer-events-auto"
                 :style="'top:' + hole.top + 'px;width:' + hole.left + 'px;height:' + hole.height + 'px'" @click="skip()"></div>
            <div class="absolute bg-black/65 pointer-events-auto"
                 :style="'top:' + hole.top + 'px;left:' + (hole.left + hole.width) + 'px;right:0;height:' + hole.height + 'px'" @click="skip()"></div>
            <div class="absolute left-0 right-0 bg-black/65 pointer-events-auto"
                 :style="'top:' + (hole.top + hole.height) + 'px;bottom:0'" @click="skip()"></div>
        </div>
    </template>

    <template x-if="hole && !isCenter">
        <div class="fixed z-[10003] pointer-events-none"
             :style="'top:' + Math.max(8, hole.top - 28) + 'px;left:' + hole.left + 'px'">
            <span class="inline-block px-2.5 py-0.5 rounded-full bg-[#F4C842] text-[#1A1A2E] text-[11px] font-bold shadow" x-text="hole.label"></span>
        </div>
    </template>

    <div
        class="fixed z-[10010] pointer-events-auto"
        :style="cardPos"
        @click.stop
    >
        <template x-if="!isCenter && hole">
            <div class="hidden lg:block w-3 h-3 bg-white rotate-45 border-l border-t border-[#F4C842] -mb-1.5 ml-8 relative z-10"></div>
        </template>

        <div class="bg-white rounded-2xl shadow-2xl border-2 border-[#F4C842] overflow-hidden w-full max-h-[min(420px,90vh)] flex flex-col">
            <div class="bg-gradient-to-r from-[#1A1A2E] to-[#2D6A4F] px-4 py-3 flex items-center justify-between shrink-0">
                <div>
                    <p class="text-[#F4C842] text-[10px] font-bold uppercase tracking-wider">Guide d'utilisation</p>
                    <p class="text-white/90 text-xs">
                        Étape <span x-text="step + 1"></span>/<span x-text="steps.length"></span>
                        @if($roleLabel)
                            <span class="text-white/50">· {{ $roleLabel }}</span>
                        @endif
                    </p>
                </div>
                <button type="button" @click="skip()" class="text-white/70 hover:text-white p-1" aria-label="Fermer">✕</button>
            </div>

            <div class="px-4 py-4 overflow-y-auto flex-1">
                <h3 class="font-display font-bold text-[#1A1A2E] text-base mb-1.5" x-text="current.title"></h3>
                <p class="text-gray-600 text-sm leading-relaxed mb-3" x-text="current.text"></p>
                <template x-if="current.actions && current.actions.length">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#2D6A4F] mb-2">Ce que vous pouvez faire</p>
                        <ul class="space-y-1.5">
                            <template x-for="(action, i) in current.actions" :key="i">
                                <li class="flex gap-2 text-sm text-gray-700 leading-snug">
                                    <span class="text-[#52B788] font-bold shrink-0 mt-0.5">•</span>
                                    <span x-text="action"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>

            <div class="px-4 pb-4 flex items-center justify-between gap-2 border-t border-gray-100 pt-3 shrink-0">
                <button type="button" @click="skip()" class="text-xs text-gray-400 hover:text-gray-600">Passer</button>
                <div class="flex gap-2">
                    <button type="button" x-show="step > 0" @click="prev()"
                        class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">
                        Retour
                    </button>
                    <button type="button" x-show="!isLast" @click="next()"
                        style="background-color:#2D6A4F;color:#ffffff;"
                        class="px-4 py-2 text-xs font-bold rounded-lg hover:opacity-90">
                        Suivant →
                    </button>
                    <button type="button" x-show="isLast" @click="finish()"
                        style="background-color:#E85D04;color:#ffffff;"
                        class="px-4 py-2 text-xs font-bold rounded-lg hover:opacity-90">
                        Terminer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
