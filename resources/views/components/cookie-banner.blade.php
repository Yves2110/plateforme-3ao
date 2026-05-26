<div
    x-data="{
        show: !localStorage.getItem('cookie_consent'),
        accept() { localStorage.setItem('cookie_consent', 'accepted'); this.show = false; },
        decline() { localStorage.setItem('cookie_consent', 'declined'); this.show = false; }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-0 inset-x-0 z-50 p-4"
    style="display: none;"
    role="dialog"
    aria-label="Consentement aux cookies"
>
    <div class="max-w-4xl mx-auto bg-[#1A1A2E] text-white rounded-2xl shadow-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <div class="flex-1 text-sm">
            <p class="font-semibold text-[#F4C842] mb-1">🍪 Cookies &amp; Confidentialité</p>
            <p class="text-white/80 leading-relaxed">
                Nous utilisons des cookies essentiels au fonctionnement du site et des cookies d'analyse anonymisés pour améliorer votre expérience.
                En continuant, vous acceptez notre
                <a href="{{ route('mentions-legales') }}" class="underline text-[#52B788] hover:text-[#40916C]">politique de confidentialité</a>.
            </p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <button @click="decline()"
                class="px-4 py-2 text-sm font-medium text-white/70 hover:text-white border border-white/20 rounded-xl transition-colors">
                Refuser
            </button>
            <button @click="accept()"
                class="px-5 py-2 text-sm font-semibold bg-[#2D6A4F] hover:bg-[#40916C] text-white rounded-xl transition-colors">
                Accepter
            </button>
        </div>
    </div>
</div>
