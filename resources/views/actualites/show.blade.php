<x-app-layout>
    <x-slot name="title">{{ $actualite->title }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-4 flex flex-wrap gap-2 items-center">
            <span class="badge badge-{{ strtolower(str_replace(' ', '', $actualite->category ?? 'actualite')) }}">
                {{ $actualite->category ?? 'Actualité' }}
            </span>
            <span class="text-xs text-gray-400">{{ $actualite->published_at?->translatedFormat('d F Y') }}</span>
        </div>
        <h1 class="font-display text-2xl sm:text-3xl font-bold text-[#1A1A2E] leading-tight mb-6">
            {{ $actualite->title }}
        </h1>
        @if($actualite->thumbnail)
            <img src="{{ asset('storage/'.$actualite->thumbnail) }}" alt="{{ $actualite->title }}"
                 class="w-full h-64 object-cover rounded-2xl mb-8">
        @endif
        <div class="prose prose-green max-w-none text-gray-700 leading-relaxed">
            {!! nl2br(e($actualite->content)) !!}
        </div>

        <div class="mt-10 pt-6 border-t border-gray-100">
            <a href="{{ route('actualites.index') }}" class="text-sm text-[#2D6A4F] hover:underline font-medium">
                ← Retour aux actualités
            </a>
        </div>
    </div>
</x-app-layout>
