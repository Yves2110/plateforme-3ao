<x-app-layout>
    <x-slot name="title">{{ $formation->title }}   Mon apprentissage</x-slot>
    <x-slot name="description">{{ Str::limit(strip_tags($formation->description), 150) }}</x-slot>

    {{-- Header avec progression --}}
    <div class="bg-gradient-to-br from-[#1A1A2E] to-[#2D6A4F] text-white py-8 px-4">
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('learning.dashboard') }}" class="text-white/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <span class="text-white/40">/</span>
                <span class="text-white/75 text-sm">Mon apprentissage</span>
            </div>
            <div class="flex flex-col md:flex-row md:items-start gap-6">
                @if($formation->thumbnail)
                    <img src="{{ asset('storage/'.$formation->thumbnail) }}" class="w-full md:w-64 h-48 rounded-2xl object-cover" alt="">
                @else
                    <div class="w-full md:w-64 h-48 rounded-2xl overflow-hidden border border-white/20">
                        <x-formation-cover-placeholder class="w-full h-full" size="lg" />
                    </div>
                @endif
                <div class="flex-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white">
                        {{ ucfirst($formation->type) }}
                    </span>
                    <h1 class="text-2xl md:text-3xl font-display font-bold mt-3">{{ $formation->title }}</h1>
                    <p class="text-white/75 mt-2">{{ $formation->organizer }}</p>

                    <div class="mt-6">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-white/75">Progression</span>
                            <span class="font-semibold">{{ $progressPercent }}% ({{ $completedLessons }}/{{ $totalLessons }} leçons)</span>
                        </div>
                        <div class="h-3 bg-white/20 rounded-full overflow-hidden">
                            <div class="h-full bg-white rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Sidebar avec modules --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 p-4 sticky top-4">
                    <h2 class="font-display font-semibold text-[#1A1A2E] mb-4">Contenu du cours</h2>

                    @forelse($modules as $moduleIndex => $module)
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-full bg-[#2D6A4F] text-white text-xs flex items-center justify-center font-medium">{{ $moduleIndex + 1 }}</span>
                                <h3 class="font-medium text-sm text-gray-800">{{ $module->title }}</h3>
                            </div>

                            <div class="space-y-1 ml-8">
                                @foreach($module->publishedLessons as $lessonIndex => $lesson)
                                    @php
                                        $isCompleted = $lesson->progresses->where('user_id', auth()->id())->whereNotNull('completed_at')->isNotEmpty();
                                    @endphp
                                    <a href="{{ route($lesson->learningRouteName(), [$formation->slug, $lesson]) }}"
                                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->is('mon-apprentissage/formations/'.$formation->slug.'/lecons/'.$lesson->id) ? 'bg-[#2D6A4F] text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                                        @if($isCompleted)
                                            <svg class="w-4 h-4 text-green-500 {{ request()->is('mon-apprentissage/formations/'.$formation->slug.'/lecons/'.$lesson->id) ? 'text-white' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <span class="w-4 h-4 rounded-full border-2 border-gray-300 {{ request()->is('mon-apprentissage/formations/'.$formation->slug.'/lecons/'.$lesson->id) ? 'border-white' : '' }}"></span>
                                        @endif
                                        <span class="line-clamp-1">{{ $lesson->title }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Aucun contenu disponible pour le moment.</p>
                    @endforelse
                </div>
            </div>

            {{-- Contenu principal --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-xl font-display font-semibold text-[#1A1A2E] mb-4">À propos de cette formation</h2>

                    @if($formation->description)
                        <div class="prose prose-green max-w-none mb-6">
                            {!! nl2br(e($formation->description)) !!}
                        </div>
                    @endif

                    @if($formation->objectives)
                        <div class="mb-6">
                            <h3 class="font-semibold text-[#1A1A2E] mb-2">Objectifs pédagogiques</h3>
                            <div class="bg-green-50 rounded-xl p-4">
                                <ul class="space-y-2">
                                    @foreach(explode("\n", $formation->objectives) as $objective)
                                        @if(trim($objective))
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-[#2D6A4F] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span class="text-gray-700">{{ ltrim(trim($objective), '•- ') }}</span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="grid sm:grid-cols-2 gap-4 mb-6">
                        @if($formation->audience)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-medium text-gray-800 mb-1">Public cible</h4>
                                <p class="text-sm text-gray-600">{{ $formation->audience }}</p>
                            </div>
                        @endif
                        @if($formation->duration)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-medium text-gray-800 mb-1">Durée</h4>
                                <p class="text-sm text-gray-600">{{ $formation->duration }}</p>
                            </div>
                        @endif
                        @if($formation->start_date)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-medium text-gray-800 mb-1">Dates</h4>
                                <p class="text-sm text-gray-600">
                                    Du {{ $formation->start_date->format('d/m/Y') }}
                                    @if($formation->end_date)
                                        au {{ $formation->end_date->format('d/m/Y') }}
                                    @endif
                                </p>
                            </div>
                        @endif
                        @if($formation->country || $formation->location)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-medium text-gray-800 mb-1">Lieu</h4>
                                <p class="text-sm text-gray-600">
                                    {{ $formation->location }}
                                    @if($formation->location && $formation->country), @endif
                                    {{ $formation->country }}
                                    @if($formation->is_online)
                                        <span class="inline-flex items-center gap-1 text-[#2D6A4F] ml-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            En ligne
                                        </span>
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>

                    @if($certificate)
                        <div class="bg-gradient-to-br from-[#F8F5F0] to-[#d4e8dc] border-2 border-[#F4C842] rounded-2xl p-6 mb-6 text-center">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-[#2D6A4F] text-white flex items-center justify-center">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            </div>
                            <h3 class="text-xl font-display font-bold text-[#2D6A4F] mb-1">Formation terminée</h3>
                            <p class="text-sm text-gray-600 mb-1">Votre certificat a été délivré automatiquement.</p>
                            <p class="text-xs text-gray-500 mb-4">N° {{ $certificate->certificate_number }} · {{ $certificate->issued_at->format('d/m/Y') }}</p>
                            <div class="flex flex-wrap justify-center gap-3">
                                <a href="{{ route('learning.certificate', $formation->slug) }}" target="_blank"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-[#2D6A4F] text-[#2D6A4F] text-sm font-semibold rounded-xl hover:bg-[#F8F5F0]">
                                    Voir le certificat
                                </a>
                                <a href="{{ route('learning.certificate.download', $formation->slug) }}"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C]">
                                    Télécharger le PDF
                                </a>
                            </div>
                        </div>
                    @elseif($progressPercent === 100)
                        <div class="bg-green-50 rounded-xl p-4 mb-6 text-sm text-green-800">
                            Parcours complété — votre certificat est en cours de génération. Rechargez cette page dans quelques instants.
                        </div>
                    @endif

                    @if($formation->price)
                        <div class="bg-amber-50 rounded-xl p-4 mb-6">
                            <p class="text-amber-800">
                                <span class="font-semibold">Tarif:</span> {{ number_format($formation->price, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                    @else
                        <div class="bg-green-50 rounded-xl p-4 mb-6">
                            <p class="text-green-800">
                                <span class="font-semibold">Gratuit</span>   Cette formation est accessible sans frais.
                            </p>
                        </div>
                    @endif

                    @if(! $certificate && $modules->isNotEmpty() && $modules->first()->publishedLessons->isNotEmpty())
                        @php $firstLesson = $modules->first()->publishedLessons->first(); @endphp
                        <a href="{{ route($firstLesson->learningRouteName(), [$formation->slug, $firstLesson]) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-[#2D6A4F] text-white font-medium rounded-xl hover:bg-[#40916C] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $progressPercent > 0 ? 'Continuer la formation' : 'Commencer la formation' }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
