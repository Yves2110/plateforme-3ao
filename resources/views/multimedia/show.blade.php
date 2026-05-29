<x-app-layout>
    <x-slot name="title">{{ $item->title }}</x-slot>

    @push('styles')
    <style>
        .lightbox-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.92); z-index:9999; align-items:center; justify-content:center; }
        .lightbox-overlay.active { display:flex; }
        .lightbox-overlay img { max-height:90vh; max-width:90vw; border-radius:8px; }
    </style>
    @endpush

    {{-- Fil d'Ariane --}}
    <div class="bg-[#F8F5F0] border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-wrap items-center gap-1.5 text-sm text-gray-500">
            <a href="{{ route('multimedia.index') }}" class="hover:text-[#2D6A4F]">Multimédia</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('multimedia.index', ['type' => $item->type]) }}" class="hover:text-[#2D6A4F]">{{ ucfirst($item->type) }}s</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#2D6A4F] font-medium">{{ Str::limit($item->title, 50) }}</span>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <x-public-manage-bar
            label="Multimédia"
            :permissions="['contribuer-multimedia', 'administrer-utilisateurs']"
            :create-route="route('admin.medias.create')"
            :list-route="route('admin.medias.index')"
            :item="$item"
            :edit-route="route('admin.medias.edit', $item)"
            :toggle-route="route('contenu.medias.toggle', $item)"
            published-key="is_published"
        />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- ===== Contenu principal ===== --}}
            <div class="lg:col-span-2">

                {{-- Badge --}}
                <span class="inline-block mb-3 px-3 py-1 text-xs font-semibold rounded-full
                             {{ $item->type === 'video' ? 'bg-red-100 text-red-700' :
                               ($item->type === 'podcast' ? 'bg-purple-100 text-purple-700' :
                               ($item->type === 'gallery' ? 'bg-blue-100 text-blue-700' : 'bg-[#E8F5E9] text-[#2D6A4F]')) }}">
                    {{ ucfirst($item->type) }}
                </span>

                <h1 class="font-display text-2xl sm:text-3xl font-bold text-[#1A1A2E] leading-tight mb-6">
                    {{ $item->title }}
                </h1>

                {{-- ===== VIDÉO ===== --}}
                @if($item->type === 'video')
                    @if($item->embed_url)
                        <div class="relative w-full rounded-2xl overflow-hidden bg-black mb-6" style="padding-top:56.25%">
                            <iframe class="absolute inset-0 w-full h-full"
                                    src="{{ $item->embed_url }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    @elseif($item->file_path)
                        <video controls class="w-full rounded-2xl mb-6 bg-black"
                               poster="{{ $item->coverImageUrl() }}">
                            <source src="{{ asset('storage/'.$item->file_path) }}" type="video/mp4">
                            Votre navigateur ne supporte pas la lecture vidéo.
                        </video>
                    @endif

                {{-- ===== PODCAST ===== --}}
                @elseif($item->type === 'podcast')
                    <div class="bg-gradient-to-br from-[#1A1A2E] to-[#2D6A4F] rounded-2xl p-8 mb-6 text-white">
                        <div class="flex items-center gap-5 mb-6">
                            <div class="w-20 h-20 rounded-2xl overflow-hidden bg-white/10 shrink-0 flex items-center justify-center p-2">
                                <img src="{{ $item->coverImageUrl() }}" alt="{{ $item->title }}"
                                     class="max-w-full max-h-full object-contain">
                            </div>
                            <div>
                                <p class="font-display font-bold text-lg leading-snug">{{ $item->title }}</p>
                                @if($item->source) <p class="text-white/60 text-sm mt-1">{{ $item->source }}</p> @endif
                                @if($item->duration) <p class="text-white/60 text-xs mt-0.5">⏱ {{ $item->duration }}</p> @endif
                            </div>
                        </div>
                        @if($item->file_path)
                            <audio controls class="w-full rounded-xl" style="filter:invert(0.85)">
                                <source src="{{ asset('storage/'.$item->file_path) }}" type="audio/mpeg">
                            </audio>
                        @elseif($item->url)
                            <audio controls class="w-full rounded-xl" style="filter:invert(0.85)">
                                <source src="{{ $item->url }}" type="audio/mpeg">
                            </audio>
                        @endif
                    </div>

                {{-- ===== GALERIE ===== --}}
                @elseif($item->type === 'gallery')
                    @php $photos = $item->photos ?? collect(); @endphp
                    @if($photos->isEmpty())
                        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                            Cette galerie ne contient pas encore d’images. Ajoutez-en depuis l’administration (type Galerie, upload multiple).
                        </div>
                    @elseif($photos->count())
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-6" id="gallery-grid">
                            @foreach($photos as $idx => $photo)
                                <button onclick="openLightbox({{ $idx }})"
                                        data-url="{{ asset('storage/'.$photo->file_path) }}"
                                        class="relative overflow-hidden rounded-xl bg-gray-100 aspect-square group">
                                    <img src="{{ asset('storage/'.$photo->file_path) }}" alt="{{ $photo->caption }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                    @if($photo->caption)
                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 p-2 translate-y-full group-hover:translate-y-0 transition-transform">
                                            <p class="text-white text-xs">{{ $photo->caption }}</p>
                                        </div>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif

                {{-- ===== PHOTO ===== --}}
                @else
                    <div class="mb-6">
                        <x-cover-visual
                            :src="$item->coverImageUrl()"
                            :alt="$item->title"
                            :mode="$item->cardDisplayMode()"
                            height-class="max-h-[500px] min-h-[280px]"
                            rounded-class="rounded-2xl"
                        />
                    </div>
                @endif

                {{-- Description --}}
                @if($item->description)
                    <div class="prose prose-green max-w-none text-gray-700 leading-relaxed">
                        {!! nl2br(e($item->description)) !!}
                    </div>
                @endif
            </div>

            {{-- ===== Sidebar ===== --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-24 space-y-4">
                    <h3 class="font-display font-semibold text-gray-800 border-b border-gray-100 pb-3">Informations</h3>

                    <dl class="space-y-3 text-sm">
                        @if($item->published_at)
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Date</dt>
                                <dd class="font-medium text-gray-800">{{ $item->published_at->translatedFormat('d F Y') }}</dd>
                            </div>
                        @endif
                        @if($item->source)
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Source</dt>
                                <dd class="font-medium text-gray-800">{{ $item->source }}</dd>
                            </div>
                        @endif
                        @if($item->duration)
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Durée</dt>
                                <dd class="font-medium text-gray-800">{{ $item->duration }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Vues</dt>
                            <dd class="font-medium text-gray-800">{{ $item->views }}</dd>
                        </div>
                    </dl>

                    <div class="pt-3 border-t border-gray-100">
                        <button onclick="navigator.share ? navigator.share({title:'{{ addslashes($item->title) }}',url:window.location.href}) : null"
                                class="btn-outline w-full justify-center text-sm py-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            Partager
                        </button>
                    </div>
                </div>

                {{-- Contenus similaires --}}
                @if($related->count())
                    <div class="mt-6">
                        <h3 class="font-display font-semibold text-gray-800 mb-3">Contenus similaires</h3>
                        <div class="space-y-3">
                            @foreach($related as $r)
                                <a href="{{ route('multimedia.show', $r->slug) }}"
                                   class="flex gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:border-[#52B788] hover:shadow-sm transition-all group">
                                    <div class="w-14 h-14 rounded-lg overflow-hidden bg-gradient-to-br from-[#52B788] to-[#2D6A4F] shrink-0 flex items-center justify-center p-1">
                                        <img src="{{ $r->coverImageUrl() }}" alt="" class="max-w-full max-h-full {{ $r->cardDisplayMode() === 'logo' ? 'object-contain' : 'object-cover w-full h-full' }}">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-[#2D6A4F]">{{ ucfirst($r->type) }}</p>
                                        <p class="text-sm text-gray-700 group-hover:text-[#2D6A4F] font-medium line-clamp-2 transition-colors">{{ $r->title }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Lightbox pour galeries --}}
    @if($item->type === 'gallery' && isset($photos) && $photos->count())
        <div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
            <button onclick="event.stopPropagation(); prevPhoto()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <img id="lightbox-img" src="" alt="" onclick="event.stopPropagation()">
            <button onclick="event.stopPropagation(); nextPhoto()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            <button onclick="closeLightbox()" class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @push('scripts')
        <script>
            const photos = Array.from(document.querySelectorAll('#gallery-grid button[data-url]'))
                                .map(btn => btn.dataset.url);
            let current = 0;
            function openLightbox(i) { current = i; document.getElementById('lightbox-img').src = photos[i]; document.getElementById('lightbox').classList.add('active'); }
            function closeLightbox() { document.getElementById('lightbox').classList.remove('active'); }
            function prevPhoto() { current = (current - 1 + photos.length) % photos.length; document.getElementById('lightbox-img').src = photos[current]; }
            function nextPhoto() { current = (current + 1) % photos.length; document.getElementById('lightbox-img').src = photos[current]; }
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); if (e.key === 'ArrowLeft') prevPhoto(); if (e.key === 'ArrowRight') nextPhoto(); });
        </script>
        @endpush
    @endif
</x-app-layout>
