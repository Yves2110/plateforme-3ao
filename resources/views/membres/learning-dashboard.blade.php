<x-app-layout>
    <x-slot name="title">Mon apprentissage · Plateforme 3AO</x-slot>
    <x-slot name="description">Suivez vos formations et progressez dans l'agroécologie</x-slot>

    {{-- Hero --}}
    <div class="bg-gradient-to-br from-[#1A1A2E] to-[#2D6A4F] text-white py-12 px-4">
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('membre.dashboard') }}" class="text-white/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <span class="text-white/40">/</span>
                <span class="text-white/75 text-sm">Mon apprentissage</span>
            </div>
            <h1 class="text-3xl font-display font-bold mb-3">{{ __('membres.learning_title') }}</h1>
            <p class="text-white/75 max-w-xl">Suivez votre progression, accédez à vos formations et continuez votre parcours dans l'agroécologie.</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-8">

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
                <p class="text-3xl font-display font-bold text-[#2D6A4F]">{{ $stats['in_progress'] }}</p>
                <p class="text-sm text-gray-500">Formations en cours</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
                <p class="text-3xl font-display font-bold text-green-600">{{ $stats['completed'] }}</p>
                <p class="text-sm text-gray-500">Formations terminées</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
                <p class="text-3xl font-display font-bold text-[#D4A017]">{{ $stats['total_hours'] }}h</p>
                <p class="text-sm text-gray-500">Temps d'apprentissage</p>
            </div>
        </div>

        {{-- Formations en cours --}}
        @if($activeEnrollments->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-display font-semibold text-[#1A1A2E] mb-4">Formations en cours</h2>
                <div class="space-y-4">
                    @foreach($activeEnrollments as $enrollment)
                        @php $progressPercent = (int) $enrollment->progress_percentage; @endphp
                        <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow">
                            <div class="flex flex-col md:flex-row gap-4">
                                @if($enrollment->formation->thumbnail)
                                    <img src="{{ asset('storage/'.$enrollment->formation->thumbnail) }}" class="w-full md:w-48 h-32 rounded-xl object-cover" alt="">
                                @else
                                    <x-formation-cover-placeholder class="w-full md:w-48 h-32 rounded-xl" size="md" />
                                @endif
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                {{ match($enrollment->formation->type) {
                                                    'atelier' => 'bg-orange-100 text-orange-700',
                                                    'cours' => 'bg-blue-100 text-blue-700',
                                                    'webinaire' => 'bg-purple-100 text-purple-700',
                                                    'certification' => 'bg-green-100 text-green-700',
                                                    default => 'bg-gray-100 text-gray-700'
                                                } }}">
                                                {{ ucfirst($enrollment->formation->type) }}
                                            </span>
                                            <h3 class="font-display font-semibold text-lg text-[#1A1A2E] mt-2">{{ $enrollment->formation->title }}</h3>
                                            <p class="text-sm text-gray-500 mt-1">{{ $enrollment->formation->organizer }}</p>
                                        </div>
                                        <a href="{{ route('learning.show', $enrollment->formation->slug) }}"
                                           class="px-4 py-2 bg-[#2D6A4F] text-white text-sm font-medium rounded-xl hover:bg-[#40916C] transition-colors">
                                            Continuer
                                        </a>
                                    </div>
                                    <div class="mt-4">
                                        <div class="flex items-center justify-between text-sm mb-2">
                                            <span class="text-gray-600">Progression</span>
                                            <span class="font-medium text-[#2D6A4F]">{{ $progressPercent }}%</span>
                                        </div>
                                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-[#2D6A4F] rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Formations terminées --}}
        @if($completedEnrollments->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-display font-semibold text-[#1A1A2E] mb-4">Formations terminées</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($completedEnrollments as $enrollment)
                        <div class="bg-white rounded-2xl border border-gray-100 p-4 hover:shadow-md transition-shadow">
                            @if($enrollment->formation->thumbnail)
                                <img src="{{ asset('storage/'.$enrollment->formation->thumbnail) }}" class="w-full h-40 rounded-xl object-cover mb-4" alt="">
                            @else
                                <x-formation-cover-placeholder class="w-full h-40 rounded-xl mb-4" />
                            @endif
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <x-icon name="check-circle" class="w-3.5 h-3.5" /> Terminée
                            </span>
                            <h3 class="font-display font-semibold text-[#1A1A2E] mt-2">{{ $enrollment->formation->title }}</h3>
                            <p class="text-xs text-gray-500 mt-1">Complétée le {{ $enrollment->completed_at?->format('d/m/Y') }}</p>
                            @if($enrollment->certificate)
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <a href="{{ route('learning.certificate.download', $enrollment->formation->slug) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-[#2D6A4F] text-white rounded-lg hover:bg-[#40916C]">
                                        Certificat PDF
                                    </a>
                                    <a href="{{ route('learning.show', $enrollment->formation->slug) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-[#2D6A4F] border border-[#2D6A4F] rounded-lg hover:bg-[#F8F5F0]">
                                        Voir le parcours
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Découvrir de nouvelles formations --}}
        @if($availableFormations->isNotEmpty())
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-display font-semibold text-[#1A1A2E]">Découvrir de nouvelles formations</h2>
                    <a href="{{ route('formation.index') }}" class="text-sm text-[#2D6A4F] hover:underline">Voir toutes les formations</a>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($availableFormations as $formation)
                        <a href="{{ route('formation.show', $formation->slug) }}" class="bg-white rounded-2xl border border-gray-100 p-4 hover:shadow-md transition-shadow group">
                            @if($formation->thumbnail)
                                <img src="{{ asset('storage/'.$formation->thumbnail) }}" class="w-full h-40 rounded-xl object-cover mb-4 group-hover:scale-105 transition-transform" alt="">
                            @else
                                <x-formation-cover-placeholder class="w-full h-40 rounded-xl mb-4" />
                            @endif
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ match($formation->type) {
                                    'atelier' => 'bg-orange-100 text-orange-700',
                                    'cours' => 'bg-blue-100 text-blue-700',
                                    'webinaire' => 'bg-purple-100 text-purple-700',
                                    'certification' => 'bg-green-100 text-green-700',
                                    default => 'bg-gray-100 text-gray-700'
                                } }}">
                                {{ ucfirst($formation->type) }}
                            </span>
                            <h3 class="font-display font-semibold text-[#1A1A2E] mt-2 group-hover:text-[#2D6A4F] transition-colors">{{ $formation->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $formation->organizer ?? 'Organisateur non spécifié' }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- État vide --}}
        @if($activeEnrollments->isEmpty() && $completedEnrollments->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl border border-gray-100">
                <x-icon name="book" class="w-14 h-14 mx-auto mb-4 text-[#2D6A4F]/40" />
                <h2 class="text-xl font-display font-semibold text-[#1A1A2E] mb-2">Aucune formation en cours</h2>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">Découvrez nos formations en agroécologie et commencez votre parcours d'apprentissage dès aujourd'hui.</p>
                <a href="{{ route('formation.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#2D6A4F] text-white font-medium rounded-xl hover:bg-[#40916C] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18c1.141 0 2.544.313 3.5 1.11V8.253m0 9.747c.956-.797 2.359-1.11 3.5-1.11 1.246 0 2.832.477 4 1.253V6.253C16.832 5.477 15.246 5 14 5c-1.141 0-2.544.313-3.5 1.11"/></svg>
                    Explorer les formations
                </a>
            </div>
        @endif

    </div>
</x-app-layout>
