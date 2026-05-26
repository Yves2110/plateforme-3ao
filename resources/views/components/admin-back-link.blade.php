@props([
    'href',
    'label' => 'Retour',
])

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 text-sm font-semibold text-[#2D6A4F] hover:text-[#40916C] transition-colors group']) }}>
    <span class="flex items-center justify-center w-9 h-9 rounded-full bg-[#F8F5F0] group-hover:bg-[#E8F0EB] border border-[#2D6A4F]/20 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </span>
    <span>{{ $label }}</span>
</a>
