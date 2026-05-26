<x-app-layout>
    <x-slot name="title">Actualités</x-slot>

    <div class="bg-gradient-to-r from-[#2D6A4F] to-[#40916C] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-3xl font-bold text-white mb-2">Actualités 3AO</h1>
            <p class="text-white/80">Suivez les dernières nouvelles de l'agroécologie en Afrique de l'Ouest</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($actualites as $actu)
                <a href="{{ route('actualites.show', $actu->slug) }}" class="card group">
                    <div class="h-44 bg-gradient-to-br from-[#52B788] to-[#2D6A4F] overflow-hidden relative">
                        @if($actu->thumbnail)
                            <img src="{{ asset('storage/'.$actu->thumbnail) }}" alt="{{ $actu->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        @endif
                        <span class="absolute top-2 left-2 badge badge-{{ strtolower(str_replace(' ', '', $actu->category ?? 'actualite')) }}">
                            {{ $actu->category ?? 'Actualité' }}
                        </span>
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-900 group-hover:text-[#2D6A4F] transition-colors line-clamp-2 text-sm mb-2">
                            {{ $actu->title }}
                        </h3>
                        <p class="text-xs text-gray-400">{{ $actu->published_at?->translatedFormat('d F Y') }}</p>
                    </div>
                </a>
            @empty
                <div class="col-span-3 py-16 text-center text-gray-400">
                    <p class="font-medium">Aucune actualité disponible</p>
                </div>
            @endforelse
        </div>
        @if($actualites->hasPages())
            <div class="mt-10">{{ $actualites->links() }}</div>
        @endif
    </div>
</x-app-layout>
