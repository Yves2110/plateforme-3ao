<x-app-layout>
    <x-slot name="title">{{ $formation->title }}</x-slot>
    <x-slot name="description">{{ Str::limit($formation->description, 155) }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 py-10">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700">{{ session('info') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">{{ session('error') }}</div>
        @endif

        <x-public-manage-bar
            label="Formations"
            :permissions="['gerer-formations', 'administrer-utilisateurs']"
            :create-route="route('admin.formations.create')"
            :list-route="route('admin.formations.index')"
            :item="$formation"
            :edit-route="route('admin.formations.edit', $formation)"
            :toggle-route="route('contenu.formations.toggle', $formation)"
            published-key="is_validated"
        />

        <nav class="text-xs text-gray-400 mb-6 flex items-center gap-1.5">
            <a href="{{ route('formation.index') }}" class="hover:text-[#2D6A4F] transition-colors">Hub Formation</a>
            <span>/</span>
            <span class="text-gray-600">{{ Str::limit($formation->title, 40) }}</span>
        </nav>

        <div class="grid lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2">
                @if($formation->thumbnail)
                    <img src="{{ asset('storage/'.$formation->thumbnail) }}" class="w-full h-56 object-cover rounded-2xl mb-6" alt="{{ $formation->title }}">
                @else
                    <x-formation-cover-placeholder class="w-full h-56 rounded-2xl mb-6" />
                @endif

                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#F8F5F0] text-[#2D6A4F]">{{ ucfirst($formation->type) }}</span>
                    @if($formation->is_online)
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700">
                            <x-icon name="globe" class="w-3.5 h-3.5" /> En ligne
                        </span>
                    @endif
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">{{ strtoupper($formation->language) }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-display font-bold text-[#1A1A2E] mb-4">{{ $formation->title }}</h1>

                @if($formation->description)
                <div class="prose prose-green max-w-none text-gray-700 text-sm leading-relaxed mb-6">
                    {!! nl2br(e($formation->description)) !!}
                </div>
                @endif

                @if($formation->objectives)
                <div class="bg-[#F8F5F0] rounded-2xl p-4 mb-6">
                    <h2 class="font-semibold text-[#2D6A4F] text-sm mb-2 flex items-center gap-2">
                        <x-icon name="target" class="w-4 h-4" /> Objectifs pédagogiques
                    </h2>
                    <div class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($formation->objectives)) !!}</div>
                </div>
                @endif

                @if($formation->audience)
                <div class="border border-gray-100 rounded-2xl p-4 mb-6">
                    <h2 class="font-semibold text-gray-700 text-sm mb-1 flex items-center gap-2">
                        <x-icon name="users" class="w-4 h-4 text-[#2D6A4F]" /> Public cible
                    </h2>
                    <p class="text-sm text-gray-600">{{ $formation->audience }}</p>
                </div>
                @endif

                @if($hasLmsContent && $lmsModules->isNotEmpty())
                <div class="border border-gray-100 rounded-2xl p-5 mb-6">
                    <h2 class="font-semibold text-[#2D6A4F] text-sm mb-3 flex items-center gap-2">
                        <x-icon name="book" class="w-4 h-4" /> Programme du parcours en ligne
                    </h2>
                    <p class="text-xs text-gray-500 mb-4">{{ $lmsStats['modules'] }} module(s) · {{ $lmsStats['lessons'] }} leçon(s) · {{ $lmsStats['duration'] }}</p>
                    <div class="space-y-4">
                        @foreach($lmsModules as $modIndex => $mod)
                            <div>
                                <p class="text-sm font-medium text-gray-800 mb-1">{{ $modIndex + 1 }}. {{ $mod->title }}</p>
                                <ul class="ml-4 space-y-1">
                                    @foreach($mod->publishedLessons as $les)
                                        <li class="text-xs text-gray-600 flex items-center gap-2">
                                            @if($les->type === 'quiz')
                                                <x-icon name="target" class="w-3.5 h-3.5 text-[#2D6A4F]" />
                                            @elseif($les->type === 'video')
                                                <x-icon name="film" class="w-3.5 h-3.5 text-blue-600" />
                                            @else
                                                <x-icon name="book" class="w-3.5 h-3.5 text-gray-400" />
                                            @endif
                                            {{ $les->title }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="space-y-4">
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 space-y-3 sticky top-4">
                    @if($formation->price)
                        <div class="text-2xl font-bold text-[#2D6A4F]">{{ number_format($formation->price, 0, ',', ' ') }} FCFA</div>
                    @else
                        <div class="text-2xl font-bold text-green-600">Gratuit</div>
                    @endif

                    @auth
                        @if($enrollment?->isActive() || $enrollment?->isCompleted())
                            @if($hasLmsContent)
                                <a href="{{ $courseEntryUrl }}"
                                   class="block w-full text-center px-5 py-2.5 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                                    {{ $enrollment->isCompleted() ? 'Revoir le parcours' : ($enrollment->progress_percentage > 0 ? 'Continuer le cours' : 'Commencer le cours') }}
                                </a>
                                <p class="text-xs text-gray-500 text-center">Parcours en ligne avec suivi de progression</p>
                            @else
                                <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800">
                                    Vous êtes inscrit(e) à cette formation sur la plateforme.
                                </div>
                            @endif

                            @if($formation->registration_url)
                                <a href="{{ $formation->registration_url }}" target="_blank" rel="noopener"
                                   class="block w-full text-center px-5 py-2.5 border border-[#2D6A4F] text-[#2D6A4F] bg-white text-sm font-semibold rounded-xl hover:bg-[#40916C] hover:text-white transition-colors">
                                    {{ $formation->is_online ? 'Rejoindre la session en ligne (lien externe)' : 'Compléter l\'inscription (lien partenaire)' }} →
                                </a>
                                <p class="text-xs text-gray-500 text-center">Lien complémentaire après inscription sur 3AO</p>
                            @elseif(! $hasLmsContent)
                                <p class="text-xs text-gray-500 text-center">Le contenu en ligne sera bientôt disponible sur la plateforme.</p>
                            @endif
                        @elseif($enrollment?->isPending())
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                                Inscription en attente de validation{{ $formation->price ? ' (paiement)' : '' }}.
                            </div>
                        @else
                            <form method="POST" action="{{ route('formation.enroll', $formation->slug) }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-center px-5 py-2.5 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                                    S'inscrire
                                </button>
                            </form>
                            <p class="text-xs text-gray-500 text-center">
                                @if($hasLmsContent)
                                    Inscription sur la plateforme, puis accès au parcours en ligne
                                @else
                                    Inscription sur la plateforme 3AO (obligatoire avant tout suivi)
                                @endif
                            </p>
                        @endif
                    @else
                        <a href="{{ route('login', ['redirect' => route('formation.show', [$formation->slug, 'inscrire' => 1])]) }}"
                           class="block w-full text-center px-5 py-2.5 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                            Se connecter pour s'inscrire
                        </a>
                        @if($formation->registration_url)
                            <p class="text-xs text-gray-500 text-center">Connectez-vous pour vous inscrire sur la plateforme avant d'accéder aux liens externes.</p>
                        @endif
                    @endauth

                    <div class="border-t border-gray-100 pt-3 space-y-2 text-sm text-gray-600">
                        @if($formation->organizer)
                        <div class="flex items-start gap-2">
                            <x-icon name="building" class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                            <span>{{ $formation->organizer }}</span>
                        </div>
                        @endif
                        @if($formation->start_date)
                        <div class="flex items-start gap-2">
                            <x-icon name="calendar" class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                            <span>{{ $formation->start_date->translatedFormat('d F Y') }}
                            @if($formation->end_date && $formation->end_date != $formation->start_date)
                                → {{ $formation->end_date->translatedFormat('d F Y') }}
                            @endif</span>
                        </div>
                        @endif
                        @if($formation->duration)
                        <div class="flex items-start gap-2">
                            <x-icon name="clock" class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                            <span>{{ $formation->duration }}</span>
                        </div>
                        @endif
                        @if($formation->is_online)
                        <div class="flex items-start gap-2">
                            <x-icon name="globe" class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                            <span>Formation en ligne</span>
                        </div>
                        @elseif($formation->location || $formation->country)
                        <div class="flex items-start gap-2">
                            <x-icon name="location" class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                            <span>{{ $formation->location }}{{ $formation->location && $formation->country ? ', ' : '' }}{{ $formation->country }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($related->count())
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Formations similaires</h3>
                    @foreach($related as $r)
                    <a href="{{ route('formation.show', $r->slug) }}"
                       class="block py-2 border-b border-gray-50 last:border-0 text-xs text-gray-700 hover:text-[#2D6A4F] transition-colors">
                        {{ Str::limit($r->title, 55) }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
