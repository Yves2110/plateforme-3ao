@extends('admin.layouts.admin')

@section('title', $action === 'create' ? 'Nouvelle ressource' : 'Éditer la ressource')
@section('page-title', $action === 'create' ? 'Nouvelle ressource' : 'Éditer : ' . $ressource->title)

@section('content')
@php
    $types = config('bibliotheque.types');
    $languages = config('bibliotheque.languages');
    $themes = config('bibliotheque.themes');
    $currentType = old('type', $ressource->type ?: $types[0]);
    $currentTheme = old('theme', $ressource->relationLoaded('tags') ? $ressource->tags->first()?->name : null);
@endphp
<div class="py-6 max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <p class="text-xs text-gray-500 mb-4 pb-4 border-b border-gray-100">
            Les champs correspondent aux filtres de la bibliothèque publique (type, langue, thématique).
        </p>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
        @endif

        <form action="{{ $action === 'create' ? route('admin.ressources.store') : route('admin.ressources.update', $ressource) }}"
              method="POST" enctype="multipart/form-data" class="space-y-5"
              x-data="{ docType: @js($currentType) }">
            @csrf
            @if($action === 'edit') @method('PUT') @endif

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $ressource->title) }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type de document <span class="text-red-500">*</span></label>
                    <select name="type" required x-model="docType"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white">
                        @foreach($types as $t)
                            <option value="{{ $t }}" @selected($currentType === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Langue <span class="text-red-500">*</span></label>
                    <select name="language" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white">
                        @foreach($languages as $code => $label)
                            <option value="{{ $code }}" {{ old('language', $ressource->language ?: 'fr') === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Thématique</label>
                <select name="theme" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white">
                    <option value="">  Aucune  </option>
                    @foreach($themes as $theme)
                        <option value="{{ $theme }}" {{ $currentTheme === $theme ? 'selected' : '' }}>{{ $theme }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Utilisée pour le filtre « Thématique » sur le site public.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Auteur(s)</label>
                    <input type="text" name="author" value="{{ old('author', $ressource->author) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pays / zone</label>
                    <input type="text" name="country" value="{{ old('country', $ressource->country) }}"
                           placeholder="ex. Burkina Faso, Régional"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Résumé</label>
                <textarea name="abstract" rows="4" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] resize-y">{{ old('abstract', $ressource->abstract) }}</textarea>
            </div>

            {{-- Vidéo YouTube / Vimeo --}}
            <div x-show="docType === 'Vidéo'" x-cloak class="p-4 bg-red-50/50 border border-red-100 rounded-xl space-y-2">
                <label class="block text-xs font-semibold text-gray-700">URL vidéo (YouTube ou Vimeo) <span class="text-red-500">*</span></label>
                <input type="url" name="video_url" value="{{ old('video_url', $ressource->video_url) }}"
                       placeholder="https://www.youtube.com/watch?v=… ou https://youtu.be/…"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                <p class="text-xs text-gray-500">La vidéo sera intégrée sur la fiche ressource (iframe). Pas de PDF requis pour ce type.</p>
            </div>

            {{-- PDF --}}
            <div x-show="docType !== 'Vidéo'" class="p-4 bg-gray-50 border border-gray-100 rounded-xl space-y-2">
                <label class="block text-xs font-semibold text-gray-700">Fichier PDF</label>
                @if($action === 'edit' && $ressource->file_path)
                    <a href="{{ asset('storage/'.$ressource->file_path) }}" target="_blank" class="text-xs text-[#2D6A4F] underline block">Voir le PDF actuel</a>
                @endif
                <input type="file" name="file_path" accept=".pdf,application/pdf"
                       class="w-full text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#F8F5F0] file:text-[#2D6A4F] file:font-semibold hover:file:bg-green-50">
                <p class="text-xs text-gray-400">PDF max 20 Mo   affiché dans la liseuse sur le site.</p>
            </div>

            {{-- Image de couverture --}}
            <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl space-y-2">
                <label class="block text-xs font-semibold text-gray-700">Image de couverture</label>
                @if($action === 'edit' && $ressource->thumbnail)
                    <img src="{{ asset('storage/'.$ressource->thumbnail) }}" class="h-24 rounded-lg mb-2 object-cover" alt="">
                @endif
                <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp,image/gif"
                       class="w-full text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#F8F5F0] file:text-[#2D6A4F] file:font-semibold hover:file:bg-green-50">
                <p class="text-xs text-gray-400">JPG, PNG ou WebP   max 4 Mo. Visible sur les cartes et la fiche ressource.</p>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_validated" value="0">
                <input type="checkbox" id="is_validated" name="is_validated" value="1"
                       {{ old('is_validated', $ressource->is_validated) ? 'checked' : '' }}
                       class="w-4 h-4 text-[#2D6A4F] rounded">
                <label for="is_validated" class="text-sm font-medium text-gray-700">Validée (visible publiquement)</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                    {{ $action === 'create' ? 'Créer la ressource' : 'Enregistrer' }}
                </button>
                <a href="{{ route('admin.ressources.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
