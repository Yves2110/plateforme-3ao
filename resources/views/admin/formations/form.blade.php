@extends('admin.layouts.admin')

@section('title', $formation->id ? 'Modifier la formation' : 'Nouvelle formation')
@section('page-title', $formation->id ? 'Modifier la formation' : 'Nouvelle formation')
@section('page-subtitle', $formation->id ? $formation->title : 'Créer une nouvelle formation')

@section('content')
<div class="py-6 max-w-4xl mx-auto">

    <form method="POST" action="{{ $formation->id ? route('admin.formations.update', $formation) : route('admin.formations.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if($formation->id)
            @method('PUT')
        @endif

        {{-- En-tête --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.formations.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-xl font-semibold text-gray-800">
                {{ $formation->id ? 'Modifier la formation' : 'Nouvelle formation' }}
            </h1>
        </div>

        {{-- Statut et validation --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Statut de publication
            </h2>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_validated" value="1" {{ old('is_validated', $formation->is_validated) ? 'checked' : '' }}
                       class="w-5 h-5 text-[#2D6A4F] rounded border-gray-300 focus:ring-[#52B788]">
                <span class="text-gray-700">Formation validée et visible publiquement</span>
            </label>
        </div>

        {{-- Informations de base --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Informations de base
            </h2>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre *</label>
                    <input type="text" name="title" value="{{ old('title', $formation->title) }}" required
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                        @foreach(['atelier' => 'Atelier pratique', 'cours' => 'Cours en ligne', 'webinaire' => 'Webinaire', 'certification' => 'Certification'] as $value => $label)
                            <option value="{{ $value }}" {{ old('type', $formation->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Organisateur</label>
                    <input type="text" name="organizer" value="{{ old('organizer', $formation->organizer) }}"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                    @error('organizer')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Langue</label>
                    <select name="language" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                        @foreach(['fr' => 'Français', 'en' => 'English', 'pt' => 'Português'] as $value => $label)
                            <option value="{{ $value }}" {{ old('language', $formation->language) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Public cible</label>
                    <input type="text" name="audience" value="{{ old('audience', $formation->audience) }}"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                           placeholder="Ex: Agriculteurs, techniciens agricoles...">
                </div>
            </div>
        </div>

        {{-- Localisation et dates --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Localisation et dates
            </h2>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <input type="checkbox" name="is_online" value="1" id="is_online" {{ old('is_online', $formation->is_online) ? 'checked' : '' }}
                           class="w-5 h-5 text-[#2D6A4F] rounded border-gray-300 focus:ring-[#52B788]">
                    <label for="is_online" class="text-gray-700 cursor-pointer">Formation en ligne (distanciel)</label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                    <input type="text" name="country" value="{{ old('country', $formation->country) }}"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                           placeholder="Ex: Burkina Faso">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lieu / Ville</label>
                    <input type="text" name="location" value="{{ old('location', $formation->location) }}"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                           placeholder="Ex: Ouagadougou">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $formation->start_date?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $formation->end_date?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durée</label>
                    <input type="text" name="duration" value="{{ old('duration', $formation->duration) }}"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                           placeholder="Ex: 3 jours, 6 semaines...">
                </div>
            </div>
        </div>

        {{-- Tarification et inscription --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Tarification et inscription
            </h2>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix (FCFA)</label>
                    <input type="number" name="price" value="{{ old('price', $formation->price) }}" min="0"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                           placeholder="Laissez vide pour gratuit">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour une formation gratuite</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lien externe (optionnel)</label>
                    <input type="url" name="registration_url" value="{{ old('registration_url', $formation->registration_url) }}"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                           placeholder="https://zoom.us/...">
                    <p class="text-xs text-gray-500 mt-1">Zoom, visioconférence ou site partenaire — affiché uniquement après inscription sur la plateforme. Le bouton « S'inscrire » inscrit toujours sur 3AO.</p>
                </div>
            </div>
        </div>

        {{-- Contenu --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Contenu
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">{{ old('description', $formation->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Objectifs pédagogiques</label>
                    <textarea name="objectives" rows="3"
                              class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                              placeholder="• Objectif 1&#10;• Objectif 2&#10;• Objectif 3">{{ old('objectives', $formation->objectives) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Média --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Image de couverture
            </h2>

            @if($formation->thumbnail)
                <div class="flex items-center gap-4">
                    <img src="{{ asset('storage/'.$formation->thumbnail) }}" class="w-32 h-24 rounded-lg object-cover" alt="">
                    <div>
                        <p class="text-sm text-gray-600">Image actuelle</p>
                        <p class="text-xs text-gray-400">Téléchargez une nouvelle image pour la remplacer</p>
                    </div>
                </div>
            @endif

            <div>
                <input type="file" name="thumbnail" accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#2D6A4F] file:text-white hover:file:bg-[#40916C]">
                <p class="text-xs text-gray-500 mt-1">Format recommandé: 800x600px, max 2MB</p>
                @error('thumbnail')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        @if($formation->id)
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h2 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Contenu pédagogique (LMS)
            </h2>
            <p class="text-sm text-gray-600">Organisez modules, leçons et quiz pour le parcours en ligne accessible via « Mon apprentissage ».</p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.formations.modules.index', $formation) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white text-sm font-medium rounded-xl hover:bg-[#40916C]">
                    Modules & leçons
                </a>
                <a href="{{ route('admin.formations.quizzes.index', $formation) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-xl hover:bg-purple-700">
                    Quiz
                </a>
                <a href="{{ route('admin.formations.enrollments.index', $formation) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700">
                    Inscriptions
                </a>
                @if($formation->is_validated && $formation->hasLmsContent())
                    <a href="{{ route('formation.show', $formation->slug) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50">
                        Voir la fiche publique
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Boutons d'action --}}
        <div class="flex items-center gap-4">
            <button type="submit" class="px-6 py-3 bg-[#2D6A4F] text-white font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                {{ $formation->id ? 'Enregistrer les modifications' : 'Créer la formation' }}
            </button>
            <a href="{{ route('admin.formations.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                Annuler
            </a>
        </div>

    </form>

</div>
@endsection
