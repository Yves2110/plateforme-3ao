<x-app-layout>
    <x-slot name="title">Galerie Multimédia 3AO</x-slot>

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#2D6A4F] to-[#40916C] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-3xl font-bold text-white mb-2">Galerie Multimédia</h1>
            <p class="text-white/80">Photos, vidéos, podcasts et galeries sur l'agroécologie en Afrique de l'Ouest</p>
        </div>
    </div>

    {{-- Onglets type avec compteurs --}}
    <div class="bg-white border-b border-gray-100 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex gap-1 overflow-x-auto">
                @foreach([
                    '' => ['label' => 'Tout', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', 'count' => $counts['all']],
                    'photo'   => ['label' => 'Photos',  'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'count' => $counts['photo']],
                    'video'   => ['label' => 'Vidéos',  'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'count' => $counts['video']],
                    'podcast' => ['label' => 'Podcasts','icon' => 'M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z', 'count' => $counts['podcast']],
                    'gallery' => ['label' => 'Galeries','icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'count' => $counts['gallery']],
                ] as $typeKey => $tab)
                    <a href="{{ route('multimedia.index', $typeKey ? ['type' => $typeKey] : []) }}"
                       class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 -mb-px whitespace-nowrap transition-colors
                              {{ $type === $typeKey || ($typeKey === '' && !$type)
                                 ? 'border-[#2D6A4F] text-[#2D6A4F]'
                                 : 'border-transparent text-gray-500 hover:text-[#2D6A4F] hover:border-gray-200' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $tab['icon'] }}"/>
                        </svg>
                        {{ $tab['label'] }}
                        <span class="px-1.5 py-0.5 text-xs rounded-full {{ $type === $typeKey || ($typeKey === '' && !$type) ? 'bg-[#2D6A4F] text-white' : 'bg-gray-100 text-gray-500' }}">
                            {{ $tab['count'] }}
                        </span>
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <x-public-manage-bar
            label="Multimédia"
            :permissions="['contribuer-multimedia', 'administrer-utilisateurs']"
            :create-route="route('admin.medias.create')"
            :list-route="route('admin.medias.index')"
        />

        <x-media-gallery-slider :slides="$gallerySlides ?? []" />

        @if($media->isEmpty())
            <div class="py-20 text-center text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-medium text-lg">Aucun contenu disponible</p>
                <p class="text-sm mt-1">Revenez bientôt ou explorez une autre catégorie.</p>
            </div>
        @else
            {{-- Grille adaptative selon le type --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($media as $item)
                    <div class="relative">
                    @if(!empty($canManage))
                        <x-public-manage-card-actions
                            :item="$item"
                            :toggle-route="route('contenu.medias.toggle', $item)"
                            published-key="is_published"
                            :edit-route="route('admin.medias.edit', $item)"
                        />
                    @endif
                    <a href="{{ route('multimedia.show', $item->slug) }}"
                       class="group card overflow-hidden block">

                        @php $mediaMode = $item->cardDisplayMode(); @endphp
                        <x-cover-visual
                            :src="$item->coverImageUrl()"
                            :alt="$item->title"
                            :mode="$mediaMode"
                            :height-class="$item->type === 'podcast' ? 'h-32' : 'h-48'"
                            class="group"
                        >
                            @if($item->type === 'video')
                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/30 transition-colors">
                                    <div class="w-14 h-14 bg-white/90 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-[#2D6A4F] ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>
                                @if($item->duration)
                                    <span class="absolute bottom-2 right-2 px-2 py-0.5 bg-black/70 text-white text-xs rounded font-mono">{{ $item->duration }}</span>
                                @endif
                            @elseif($item->type === 'gallery')
                                <div class="absolute top-2 right-2 flex items-center gap-1 px-2 py-0.5 bg-black/60 text-white text-xs rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    Galerie
                                </div>
                            @endif

                            {{-- Badge type --}}
                            <span class="absolute top-2 left-2 px-2 py-0.5 text-xs font-semibold rounded-full
                                         {{ $item->type === 'video' ? 'bg-red-600 text-white' :
                                           ($item->type === 'podcast' ? 'bg-purple-600 text-white' :
                                           ($item->type === 'gallery' ? 'bg-blue-600 text-white' : 'bg-[#2D6A4F] text-white')) }}">
                                {{ ucfirst($item->type) }}
                            </span>
                        </x-cover-visual>

                        {{-- Infos --}}
                        <div class="p-4">
                            <h3 class="font-semibold text-sm text-gray-900 group-hover:text-[#2D6A4F] transition-colors line-clamp-2 mb-1.5">
                                {{ $item->title }}
                            </h3>
                            @if($item->description)
                                <p class="text-xs text-gray-500 line-clamp-2 mb-2">{{ $item->description }}</p>
                            @endif
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span>{{ $item->published_at?->translatedFormat('d M Y') }}</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7"/></svg>
                                    {{ $item->views }}
                                </span>
                            </div>
                        </div>
                    </a>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($media->hasPages())
                <div class="mt-10">{{ $media->links() }}</div>
            @endif
        @endif
    </div>
</x-app-layout>
