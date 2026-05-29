<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Inscription en attente</h2>
            <p class="text-sm text-gray-600 leading-relaxed">
                Votre demande d'accès a bien été enregistrée. Un administrateur doit valider votre compte avant que vous puissiez utiliser la plateforme.
                Vous recevrez un e-mail de confirmation dès que votre inscription sera approuvée. Vous pourrez alors vous connecter avec l'adresse e-mail et le mot de passe que vous venez de définir.
            </p>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <x-button type="submit" class="w-full justify-center">
                Se déconnecter
            </x-button>
        </form>
    </x-authentication-card>
</x-guest-layout>
