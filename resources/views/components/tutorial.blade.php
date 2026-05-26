<div
    x-data="{
        active: !localStorage.getItem('tutorial_done'),
        step: 0,
        steps: [
            {
                title: 'Bienvenue sur la Plateforme 3AO ! 🌱',
                text: 'Cette plateforme collaborative rassemble les acteurs de l\'agroécologie en Afrique de l\'Ouest. Faisons un rapide tour ensemble — cela ne prend que 30 secondes !',
                target: null,
                position: 'center'
            },
            {
                title: '📚 La Bibliothèque',
                text: 'Accédez à des centaines de guides, études de cas et publications scientifiques sur l\'agroécologie. Filtrez par type, pays et langue.',
                target: 'nav-bibliotheque',
                position: 'bottom'
            },
            {
                title: '📰 Les Actualités',
                text: 'Restez informé des dernières nouvelles : appels à projets, financements, politiques et publications de la communauté 3AO.',
                target: 'nav-actualites',
                position: 'bottom'
            },
            {
                title: '💬 La Communauté (Forum)',
                text: 'Posez vos questions, partagez vos pratiques et échangez avec 500+ membres actifs sur des sujets comme les semences, le sol, les marchés...',
                target: 'nav-forum',
                position: 'bottom'
            },
            {
                title: '📅 Les Événements',
                text: 'Découvrez les formations, webinaires, ateliers et conférences à venir. Inscrivez-vous en un clic depuis la plateforme.',
                target: 'nav-evenements',
                position: 'bottom'
            },
            {
                title: '🗺️ La Carte des Acteurs',
                text: 'Visualisez sur une carte interactive les organisations, ONG et réseaux membres de l\'Alliance 3AO en Afrique de l\'Ouest.',
                target: 'nav-carte',
                position: 'bottom'
            },
            {
                title: '🎓 Le Hub Formation',
                text: 'Trouvez des formations agroécologiques adaptées à votre profil — ateliers terrain, cours en ligne, certifications reconnues régionalement.',
                target: 'nav-formation',
                position: 'bottom'
            },
            {
                title: 'Vous êtes prêt(e) ! 🚀',
                text: 'La plateforme est entièrement gratuite. Créez un compte pour contribuer au forum, partager des ressources et vous inscrire aux événements. Bonne découverte !',
                target: null,
                position: 'center'
            }
        ],
        get current() { return this.steps[this.step]; },
        get isCenter() { return this.current.position === \'center\'; },
        get isLast() { return this.step === this.steps.length - 1; },
        next() { if (!this.isLast) this.step++; else this.finish(); },
        prev() { if (this.step > 0) this.step--; },
        finish() { localStorage.setItem(\'tutorial_done\', \'1\'); this.active = false; },
        skip() { this.finish(); }
    }"
    x-show="active"
    style="display:none"
    class="fixed inset-0 z-[9999]"
    @keydown.escape.window="skip()"
    @keydown.arrow-right.window="next()"
    @keydown.arrow-left.window="prev()"
>
    {{-- Overlay sombre --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="skip()"></div>

    {{-- Popup tutoriel --}}
    <div
        class="absolute z-10 w-[320px] sm:w-[380px]"
        :class="isCenter
            ? 'top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2'
            : 'top-[72px] left-1/2 -translate-x-1/2 sm:left-auto sm:translate-x-0'"
        :style="!isCenter && current.target && (() => {
            const el = document.getElementById(current.target);
            if (!el) return '';
            const r = el.getBoundingClientRect();
            return 'left:' + Math.max(8, Math.min(r.left - 8, window.innerWidth - 400)) + 'px';
        })()"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
    >
        {{-- Flèche vers le haut quand ciblé --}}
        <template x-if="!isCenter">
            <div class="w-4 h-4 bg-white rotate-45 ml-8 -mb-2 shadow"></div>
        </template>

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            {{-- Header vert --}}
            <div class="bg-gradient-to-r from-[#2D6A4F] to-[#40916C] px-5 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-white/60 text-xs font-medium">Étape</span>
                    <div class="flex gap-1">
                        <template x-for="(s, i) in steps" :key="i">
                            <div class="w-2 h-2 rounded-full transition-colors"
                                 :class="i === step ? 'bg-white' : (i < step ? 'bg-white/60' : 'bg-white/25')">
                            </div>
                        </template>
                    </div>
                    <span class="text-white/60 text-xs" x-text="(step + 1) + ' / ' + steps.length"></span>
                </div>
                <button @click="skip()" class="text-white/60 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Contenu --}}
            <div class="px-5 py-5">
                <h3 class="font-display font-bold text-[#1A1A2E] text-lg leading-tight mb-2" x-text="current.title"></h3>
                <p class="text-gray-600 text-sm leading-relaxed" x-text="current.text"></p>
            </div>

            {{-- Footer boutons --}}
            <div class="px-5 pb-5 flex items-center justify-between gap-3">
                <button @click="skip()" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                    Passer le tutoriel
                </button>
                <div class="flex gap-2">
                    <button x-show="step > 0" @click="prev()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                        ← Retour
                    </button>
                    <button @click="next()"
                        class="px-5 py-2 text-sm font-semibold text-white bg-[#2D6A4F] hover:bg-[#40916C] rounded-xl transition-colors">
                        <span x-text="isLast ? '✓ Commencer !' : 'Suivant →'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Highlight de l'élément ciblé --}}
    <template x-if="!isCenter && current.target">
        <div
            class="absolute pointer-events-none rounded-xl ring-4 ring-[#F4C842] ring-offset-2 transition-all duration-300"
            :style="(() => {
                const el = document.getElementById(current.target);
                if (!el) return 'display:none';
                const r = el.getBoundingClientRect();
                return 'top:' + (r.top - 4) + 'px; left:' + (r.left - 4) + 'px; width:' + (r.width + 8) + 'px; height:' + (r.height + 8) + 'px';
            })()"
        ></div>
    </template>
</div>
