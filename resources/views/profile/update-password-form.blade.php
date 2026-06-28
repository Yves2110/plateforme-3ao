<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('profile.password_title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('profile.password_description') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="{{ __('profile.current_password') }}" />
            <x-password-input id="current_password" name="current_password" class="mt-1 block w-full" wire:model="state.current_password" autocomplete="current-password" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="{{ __('profile.new_password') }}" />
            <x-password-input id="password" name="password" class="mt-1 block w-full" wire:model="state.password" autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('profile.confirm_password') }}" />
            <x-password-input id="password_confirmation" name="password_confirmation" class="mt-1 block w-full" wire:model="state.password_confirmation" autocomplete="new-password" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('profile.saved') }}
        </x-action-message>

        <x-button>
            {{ __('profile.save') }}
        </x-button>
    </x-slot>
</x-form-section>
