<x-app-layout>
    <x-slot name="title">{{ $lesson->title }} — {{ $formation->title }}</x-slot>
    <x-slot name="description">{{ Str::limit(strip_tags($lesson->description), 150) }}</x-slot>

    {{-- Header compact --}}
    <div class="bg-[#1A1A2E] text-white py-4 px-4">
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="{{ route('learning.show', $formation->slug) }}" class="text-white/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <p class="text-white/50 text-sm truncate">{{ $formation->title }}</p>
                    <h1 class="font-display font-semibold truncate">{{ $lesson->title }}</h1>
                </div>
                <div class="hidden sm:flex items-center gap-2">
                    @if($prevLesson)
                        <a href="{{ route('learning.lesson', [$formation->slug, $prevLesson]) }}" class="p-2 text-white/60 hover:text-white hover:bg-white/10 rounded-lg transition-colors" title="Leçon précédente">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    @endif
                    @if($nextLesson)
                        <a href="{{ route('learning.lesson', [$formation->slug, $nextLesson]) }}" class="p-2 text-white/60 hover:text-white hover:bg-white/10 rounded-lg transition-colors" title="Leçon suivante">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-6">
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Contenu principal --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Lecteur selon le type --}}
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    @if($lesson->isVideo())
                        @if($lesson->video_url)
                            <div class="aspect-video bg-black">
                                <iframe src="{{ $lesson->video_url }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @elseif($lesson->file_path)
                            <video class="w-full aspect-video" controls>
                                <source src="{{ $lesson->file_url }}" type="video/mp4">
                                Votre navigateur ne supporte pas la lecture vidéo.
                            </video>
                        @endif
                    @elseif($lesson->isPdf())
                        @if($lesson->file_path)
                            <div class="aspect-[4/3] bg-gray-100">
                                <iframe src="{{ $lesson->file_url }}" class="w-full h-full" type="application/pdf"></iframe>
                            </div>
                            <div class="p-4 bg-gray-50 border-t border-gray-100">
                                <a href="{{ $lesson->file_url }}" download class="inline-flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white text-sm font-medium rounded-xl hover:bg-[#40916C] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Télécharger le PDF
                                </a>
                            </div>
                        @endif
                    @elseif($lesson->isAudio())
                        @if($lesson->file_path)
                            <div class="p-8 bg-gradient-to-br from-[#F8F5F0] to-[#d4e8dc]">
                                <audio class="w-full" controls>
                                    <source src="{{ $lesson->file_url }}" type="audio/mpeg">
                                    Votre navigateur ne supporte pas la lecture audio.
                                </audio>
                            </div>
                        @endif
                    @endif

                    {{-- Description de la leçon --}}
                    <div class="p-6">
                        <h2 class="text-xl font-display font-semibold text-[#1A1A2E] mb-3">{{ $lesson->title }}</h2>
                        @if($lesson->description)
                            <div class="prose prose-green max-w-none text-gray-600">
                                {!! nl2br(e($lesson->description)) !!}
                            </div>
                        @endif

                        @if($lesson->duration_minutes)
                            <div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Durée estimée: {{ $lesson->duration_minutes }} minutes
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Navigation mobile --}}
                <div class="flex items-center justify-between sm:hidden">
                    @if($prevLesson)
                        <a href="{{ route('learning.lesson', [$formation->slug, $prevLesson]) }}" class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Précédent
                        </a>
                    @else
                        <span></span>
                    @endif
                    @if($nextLesson)
                        <a href="{{ route('learning.lesson', [$formation->slug, $nextLesson]) }}" class="flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white rounded-xl">
                            Suivant
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-[#1A1A2E]">Avancement dans la formation</h3>
                            <p class="text-sm text-gray-500">Marquez cette leçon comme terminée pour continuer</p>
                        </div>
                        <button id="markCompleteBtn"
                                data-lesson-id="{{ $lesson->id }}"
                                data-formation-id="{{ $formation->id }}"
                                class="px-6 py-3 {{ $progress->isCompleted() ? 'bg-green-100 text-green-700' : 'bg-[#2D6A4F] text-white hover:bg-[#40916C]' }} font-medium rounded-xl transition-colors flex items-center gap-2">
                            @if($progress->isCompleted())
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Leçon terminée
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Marquer comme terminée
                            @endif
                        </button>
                    </div>
                </div>

            </div>

            {{-- Sidebar avec sommaire --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 p-4 sticky top-4">
                    <h3 class="font-display font-semibold text-[#1A1A2E] mb-4">Sommaire du cours</h3>

                    <div class="space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        @foreach($formation->publishedModules as $moduleIndex => $mod)
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-5 h-5 rounded-full bg-gray-200 text-gray-600 text-xs flex items-center justify-center font-medium">{{ $moduleIndex + 1 }}</span>
                                    <h4 class="font-medium text-sm text-gray-700">{{ $mod->title }}</h4>
                                </div>

                                <div class="space-y-1 ml-7">
                                    @foreach($mod->publishedLessons as $les)
                                        @php
                                            $lesProgress = $les->progresses->where('user_id', auth()->id())->first();
                                            $lesCompleted = $lesProgress && $lesProgress->isCompleted();
                                            $isCurrent = $les->id === $lesson->id;
                                        @endphp
                                        <a href="{{ route('learning.lesson', [$formation->slug, $les]) }}"
                                           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ $isCurrent ? 'bg-[#2D6A4F] text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                                            @if($lesCompleted)
                                                <svg class="w-4 h-4 text-green-500 {{ $isCurrent ? 'text-white' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @else
                                                <span class="w-4 h-4 rounded-full border-2 border-gray-300 {{ $isCurrent ? 'border-white' : '' }}"></span>
                                            @endif
                                            <span class="line-clamp-1 {{ $lesCompleted && !$isCurrent ? 'line-through text-gray-400' : '' }}">{{ $les->title }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Suivi du temps passé
        let timeSpent = 0;
        setInterval(() => {
            timeSpent += 10;
            if (timeSpent % 60 === 0) { // Envoyer toutes les minutes
                fetch('{{ route('learning.track-time', $lesson) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ seconds: 60 })
                });
            }
        }, 10000); // Incrémenter toutes les 10 secondes

        // Marquer comme terminé
        document.getElementById('markCompleteBtn')?.addEventListener('click', function() {
            const btn = this;
            const lessonId = btn.dataset.lessonId;
            const formationId = btn.dataset.formationId;

            fetch(`/mon-apprentissage/formations/${formationId}/lecons/${lessonId}/completer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ time_spent: timeSpent })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.classList.remove('bg-[#2D6A4F]', 'text-white', 'hover:bg-[#40916C]');
                    btn.classList.add('bg-green-100', 'text-green-700');
                    btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Leçon terminée`;

                    if (data.completed) {
                        alert('🎉 Félicitations ! Vous avez terminé cette formation.');
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
