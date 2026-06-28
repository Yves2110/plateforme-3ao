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
                <x-label for="name" value="{{ __('auth.name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <p class="mt-1 text-xs text-gray-500">{{ __('auth.name_example') }}</p>
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('auth.email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <p class="mt-1 text-xs text-gray-500">{{ __('auth.email_example') }}</p>
            </div>

            <div class="mt-4">
                <x-label for="organization" value="{{ __('auth.organization') }}" />
                <x-input id="organization" class="block mt-1 w-full" type="text" name="organization" :value="old('organization')" required autocomplete="organization" />
                <p class="mt-1 text-xs text-gray-500">{{ __('auth.organization_help') }}</p>
            </div>

            <div class="mt-4">
                <x-label for="registration_reason" value="{{ __('auth.registration_reason') }}" />
                <textarea id="registration_reason" name="registration_reason" rows="4" required
                          class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                          placeholder="{{ __('auth.registration_reason_placeholder') }}">{{ old('registration_reason') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">{{ __('auth.registration_reason_help') }}</p>
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('auth.password') }}" />
                <x-password-input id="password" name="password" class="mt-1" required autocomplete="new-password" />
                <p class="mt-1 text-xs text-gray-500">{{ __('auth.password_help') }}</p>
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('auth.password_confirm') }}" />
                <x-password-input id="password_confirmation" name="password_confirmation" class="mt-1" required autocomplete="new-password" />
                <p class="mt-1 text-xs text-gray-500">{{ __('auth.password_confirm_help') }}</p>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('auth.terms_accept', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('auth.terms_of_service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('auth.privacy_policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('auth.already_account') }}
                </a>

                <x-button class="ms-4">
                    {{ __('auth.register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
