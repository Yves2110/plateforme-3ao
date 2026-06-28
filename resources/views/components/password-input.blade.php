@props(['id' => null, 'name' => 'password', 'value' => '', 'autocomplete' => 'current-password', 'required' => false, 'placeholder' => ''])

@php
    $hasWireModel = $attributes->has('wire:model') || $attributes->has('wire:model.live') || $attributes->has('wire:model.defer');
    $inputId = $id ?? $name;
@endphp

<div x-data="{ show: false }" class="relative">
    <input :type="show ? 'text' : 'password'"
           id="{{ $inputId }}"
           name="{{ $name }}"
           @if(! $hasWireModel) value="{{ $value }}" @endif
           @if($placeholder) placeholder="{{ $placeholder }}" @endif
           autocomplete="{{ $autocomplete }}"
           {{ $required ? 'required' : '' }}
           {!! $attributes->merge(['class' => 'block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pr-10']) !!}>

    <button type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none"
            :aria-label="show ? '{{ __('auth.toggle_password_hide') }}' : '{{ __('auth.toggle_password_show') }}'">
        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.575-3.175M9 9l3 3m-3 3l6-6"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.94 17.94A10.05 10.05 0 0121.54 12c-1.274-4.057-5.064-7-9.542-7-1.06 0-2.08.177-3.04.5M3 3l18 18"/>
        </svg>
    </button>
</div>
