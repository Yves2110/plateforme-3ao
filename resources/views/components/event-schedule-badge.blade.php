@props(['event'])

@php
    $schedule = $event->schedule();
    $status = $schedule->status();
@endphp

@if($status === 'expired')
    <span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-full uppercase tracking-wide bg-gray-200 text-gray-600']) }}>
        {{ $schedule->label() }}
    </span>
@elseif($status === 'soon')
    <span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-full uppercase tracking-wide bg-[#F4C842] text-[#1A1A2E]']) }}>
        {{ $schedule->label() }}
    </span>
@endif
