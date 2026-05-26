<x-guest-layout>
    <div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-[#2D6A4F]/10 flex items-center justify-center">
                <svg class="w-7 h-7 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="font-display text-2xl font-bold text-gray-900 mb-2">Désinscription confirmée</h1>
            <p class="text-gray-600 text-sm leading-relaxed">
                L'adresse <strong>{{ $subscriber->email }}</strong> ne recevra plus nos newsletters.
            </p>
            <a href="{{ route('home') }}"
               class="inline-block mt-6 px-5 py-2.5 bg-[#2D6A4F] text-white text-sm font-semibold rounded-full hover:bg-[#40916C] transition-colors">
                Retour à l'accueil
            </a>
        </div>
    </div>
</x-guest-layout>
