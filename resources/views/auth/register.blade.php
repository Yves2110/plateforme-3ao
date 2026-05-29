<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <x-spam-protection />

            <div>
                <x-label for="name" value="Nom complet" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <p class="mt-1 text-xs text-gray-500">Exemple : Abdoul Rahim Kaboré</p>
            </div>

            <div class="mt-4">
                <x-label for="email" value="Adresse e-mail" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <p class="mt-1 text-xs text-gray-500">Exemple : abdoulrahimkabore187@gmail.com</p>
            </div>

            <div class="mt-4">
                <x-label for="organization" value="Organisation" />
                <x-input id="organization" class="block mt-1 w-full" type="text" name="organization" :value="old('organization')" required autocomplete="organization" />
                <p class="mt-1 text-xs text-gray-500">Structure, association ou institution que vous représentez.</p>
            </div>

            <div class="mt-4">
                <x-label for="registration_reason" value="Motif de la demande" />
                <textarea id="registration_reason" name="registration_reason" rows="4" required
                          class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                          placeholder="Décrivez brièvement pourquoi vous souhaitez rejoindre la plateforme 3AO…">{{ old('registration_reason') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Minimum 20 caractères. Exemple : je souhaite partager des ressources agroécologiques avec mon réseau au Burkina Faso.</p>
            </div>

            <div class="mt-4">
                <x-label for="password" value="Mot de passe" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <p class="mt-1 text-xs text-gray-500">Minimum 10 caractères avec majuscule, minuscule, chiffre et symbole. Exemple : Rahim@2026!</p>
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="Confirmer le mot de passe" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <p class="mt-1 text-xs text-gray-500">Ressaisissez exactement le même mot de passe.</p>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('J\'accepte les :terms_of_service et la :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('conditions d\'utilisation').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('politique de confidentialité').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    Déjà inscrit(e) ?
                </a>

                <x-button class="ms-4">
                    Créer mon compte
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
