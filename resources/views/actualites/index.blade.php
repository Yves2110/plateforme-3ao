<x-app-layout>
    <x-slot name="title">Actualités</x-slot>

    <div class="bg-gradient-to-r from-[#2D6A4F] to-[#40916C] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-3xl font-bold text-white mb-2">Actualités 3AO</h1>
            <p class="text-white/80">Suivez les dernières nouvelles de l'agroécologie en Afrique de l'Ouest</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <x-public-manage-bar
            label="Actualités"
            :permissions="['publier-actualites', 'administrer-utilisateurs']"
            :create-route="route('admin.actualites.create')"
            :list-route="route('admin.actualites.index')"
        />

        <x-actualite-category-filters :selected="$categoryFilter ?? []" mode="public" />

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($actualites as $actu)
                <div class="relative">
                @if(!empty($canManage))
                    <x-public-manage-card-actions
                        :item="$actu"
                        :toggle-route="route('contenu.actualites.toggle', $actu)"
                        published-key="is_published"
                        :edit-route="route('admin.actualites.edit', $actu)"
                    />
                @endif
                <a href="{{ route('actualites.show', $actu->slug) }}" class="card group block">
                    <div class="h-44 bg-gradient-to-br from-[#52B788] to-[#2D6A4F] overflow-hidden relative">
                        @if($actu->thumbnail)
                            <img src="{{ asset('storage/'.$actu->thumbnail) }}" alt="{{ $actu->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        @endif
                        <div class="absolute top-2 left-2 right-2 flex flex-wrap gap-1.5">
                            <x-actualite-category-badge :actualite="$actu" />
                            <x-syndicated-notice :actualite="$actu" size="xs" />
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-900 group-hover:text-[#2D6A4F] transition-colors line-clamp-2 text-sm mb-2">
                            {{ $actu->title }}
                        </h3>
                        <p class="text-xs text-gray-400">{{ $actu->published_at?->translatedFormat('d F Y') }}</p>
                        @if($actu->isSyndicated() && $actu->source_url)
                            <p class="text-[10px] text-amber-700 mt-1">{{ __('actualites.read_on_source') }}</p>
                        @endif
                    </div>
                </a>
                </div>
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
