<x-app-layout>
    <x-slot name="title">{{ $ressource->title }}</x-slot>

    {{-- Fil d'Ariane --}}
    <div class="bg-[#F8F5F0] border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-[#2D6A4F]">Accueil</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('bibliotheque.index') }}" class="hover:text-[#2D6A4F]">Bibliothèque</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#2D6A4F] font-medium line-clamp-1">{{ Str::limit($ressource->title, 50) }}</span>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('bibliotheque._admin-bar', ['ressource' => $ressource])

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- ===== Colonne gauche : métadonnées + actions ===== --}}
            <div class="lg:col-span-1 order-2 lg:order-1">

                {{-- Carte métadonnées --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                    {{-- Thumbnail --}}
                    <div class="h-48 bg-gradient-to-br from-[#52B788] to-[#2D6A4F] relative overflow-hidden">
                        @if($ressource->thumbnail)
                            <img src="{{ asset('storage/'.$ressource->thumbnail) }}" alt="{{ $ressource->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-20 h-20 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @endif
                        <span class="absolute top-3 left-3 badge badge-publication">{{ $ressource->type }}</span>
                    </div>

                    {{-- Infos --}}
                    <div class="p-5 space-y-3">
                        <dl class="space-y-3 text-sm">
                            @if($ressource->author)
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-[#52B788] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <div>
                                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Auteur(s)</dt>
                                        <dd class="font-medium text-gray-800">{{ $ressource->author }}</dd>
                                    </div>
                                </div>
                            @endif
                            @if($ressource->country)
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-[#52B788] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <div>
                                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Pays / Zone</dt>
                                        <dd class="font-medium text-gray-800">{{ $ressource->country }}</dd>
                                    </div>
                                </div>
                            @endif
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-[#52B788] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                </svg>
                                <div>
                                    <dt class="text-xs text-gray-400 uppercase tracking-wide">Langue</dt>
                                    <dd class="font-medium text-gray-800">{{ $ressource->language === 'fr' ? 'Français' : 'English' }}</dd>
                                </div>
                            </div>
                            @if($ressource->published_at)
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-[#52B788] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Date de publication</dt>
                                        <dd class="font-medium text-gray-800">{{ $ressource->published_at->translatedFormat('d F Y') }}</dd>
                                    </div>
                                </div>
                            @endif
                        </dl>

                        {{-- Tags --}}
                        @if($ressource->tags->count())
                            <div class="pt-2 border-t border-gray-100">
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Thématiques</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($ressource->tags as $tag)
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full"
                                              style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="pt-3 border-t border-gray-100 space-y-2">
                            @if($ressource->file_path)
                                <a href="{{ asset('storage/'.$ressource->file_path) }}" target="_blank"
                                   class="btn-primary w-full justify-center text-sm py-2.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Télécharger le PDF
                                </a>
                            @endif
                            <button onclick="navigator.share ? navigator.share({title: '{{ addslashes($ressource->title) }}', url: window.location.href}) : copyUrl()"
                                    class="btn-outline w-full justify-center text-sm py-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                Partager
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Colonne droite : contenu + PDF viewer ===== --}}
            <div class="lg:col-span-2 order-1 lg:order-2">

                {{-- Titre --}}
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-[#1A1A2E] leading-tight mb-4">
                    {{ $ressource->title }}
                </h1>

                {{-- Résumé --}}
                @if($ressource->abstract)
                    <div class="bg-[#F8F5F0] border-l-4 border-[#52B788] rounded-r-xl p-5 mb-8">
                        <p class="text-sm font-semibold text-[#2D6A4F] uppercase tracking-wide mb-1">Résumé</p>
                        <p class="text-gray-700 leading-relaxed">{{ $ressource->abstract }}</p>
                    </div>
                @endif

                {{-- Vidéo intégrée (YouTube / Vimeo) --}}
                @if($ressource->isVideoType() && $ressource->embed_url)
                    <div class="mb-8">
                        <h2 class="font-display font-semibold text-lg text-gray-800 mb-3">Vidéo</h2>
                        <div class="relative w-full rounded-2xl overflow-hidden bg-black shadow-sm" style="padding-top:56.25%">
                            <iframe class="absolute inset-0 w-full h-full"
                                    src="{{ $ressource->embed_url }}"
                                    title="{{ $ressource->title }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen></iframe>
                        </div>
                    </div>
                @elseif($ressource->isVideoType() && $ressource->video_url)
                    <div class="mb-8 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                        L’URL vidéo n’a pas pu être intégrée. Vérifiez le lien YouTube ou Vimeo dans l’administration.
                    </div>
                @endif

                {{-- ===== Liseuse PDF (PDF.js) ===== --}}
                @if($ressource->file_path && pathinfo($ressource->file_path, PATHINFO_EXTENSION) === 'pdf')
                    <div class="mb-8" id="pdf-viewer-root"
                         data-pdf-url="{{ asset('storage/'.$ressource->file_path) }}">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="font-display font-semibold text-lg text-gray-800">Aperçu du document</h2>
                            <div class="flex items-center gap-2" id="pdf-controls" style="display:none;">
                                <button type="button" id="pdf-prev"
                                        class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-600 disabled:opacity-40">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <span class="text-sm text-gray-600">Page <span id="pdf-page-num">1</span> / <span id="pdf-page-total">1</span></span>
                                <button type="button" id="pdf-next"
                                        class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-600 disabled:opacity-40">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="bg-gray-100 rounded-2xl overflow-auto border border-gray-200 flex justify-center items-start min-h-[400px]"
                             id="pdf-container"
                             style="height: 600px;">
                            <canvas id="pdf-canvas" class="max-w-full shadow-sm"></canvas>
                        </div>
                        <p id="pdf-error" class="hidden mt-2 text-sm text-red-600"></p>
                    </div>
                @endif

                {{-- Ressources similaires --}}
                @if($related->count())
                    <div>
                        <h2 class="font-display font-semibold text-lg text-gray-800 mb-4">Ressources similaires</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($related as $r)
                                <a href="{{ route('bibliotheque.show', $r->slug) }}"
                                   class="flex gap-3 p-3 bg-[#F8F5F0] hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-xl transition-all group">
                                    <div class="w-14 h-14 rounded-lg overflow-hidden bg-gradient-to-br from-[#52B788] to-[#2D6A4F] shrink-0 flex items-center justify-center">
                                        @if($r->thumbnail)
                                            <img src="{{ asset('storage/'.$r->thumbnail) }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-[#2D6A4F] mb-0.5">{{ $r->type }}</p>
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

    @if($ressource->file_path && pathinfo($ressource->file_path, PATHINFO_EXTENSION) === 'pdf')
    @push('before-livewire')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  @endpush
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof pdfjsLib === 'undefined') return;

            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            const root = document.getElementById('pdf-viewer-root');
            if (! root) return;

            const pdfUrl = root.dataset.pdfUrl;
            const canvas = document.getElementById('pdf-canvas');
            const ctx = canvas.getContext('2d');
            const container = document.getElementById('pdf-container');
            const pageNumEl = document.getElementById('pdf-page-num');
            const pageTotalEl = document.getElementById('pdf-page-total');
            const controls = document.getElementById('pdf-controls');
            const errorEl = document.getElementById('pdf-error');
            const btnPrev = document.getElementById('pdf-prev');
            const btnNext = document.getElementById('pdf-next');

            let pdfDoc = null;
            let currentPage = 1;

            async function renderPage() {
                const page = await pdfDoc.getPage(currentPage);
                const baseViewport = page.getViewport({ scale: 1 });
                const containerWidth = container.clientWidth || 800;
                const scale = Math.min(containerWidth / baseViewport.width, 1.5);
                const viewport = page.getViewport({ scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                await page.render({ canvasContext: ctx, viewport }).promise;
                pageNumEl.textContent = currentPage;
                btnPrev.disabled = currentPage <= 1;
                btnNext.disabled = currentPage >= pdfDoc.numPages;
            }

            btnPrev.addEventListener('click', async () => {
                if (currentPage <= 1) return;
                currentPage--;
                await renderPage();
            });

            btnNext.addEventListener('click', async () => {
                if (currentPage >= pdfDoc.numPages) return;
                currentPage++;
                await renderPage();
            });

            pdfjsLib.getDocument(pdfUrl).promise.then(async (doc) => {
                pdfDoc = doc;
                pageTotalEl.textContent = doc.numPages;
                controls.style.display = 'flex';
                await renderPage();
            }).catch((err) => {
                console.warn('PDF non disponible :', err);
                errorEl.textContent = 'Aperçu non disponible. Utilisez le bouton « Télécharger le PDF ».';
                errorEl.classList.remove('hidden');
            });
        });
    </script>
    @endpush
    @endif
</x-app-layout>
