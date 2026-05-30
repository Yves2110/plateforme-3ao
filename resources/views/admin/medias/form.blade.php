@extends('admin.layouts.admin')

@section('title', $action === 'create' ? 'Nouveau média' : 'Éditer le média')
@section('page-title', $action === 'create' ? 'Nouveau média' : 'Éditer : ' . $media->title)

@section('content')
@php
    $currentType = old('type', $media->type ?: 'photo');
    $galleryPhotos = $action === 'edit' && $media->relationLoaded('photos') ? $media->photos : collect();
@endphp
<div class="py-6 max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
        @endif

        <form action="{{ $action === 'create' ? route('admin.medias.store') : route('admin.medias.update', $media) }}"
              method="POST" enctype="multipart/form-data" class="space-y-5"
              x-data="{ mediaType: @js($currentType) }">
            @csrf
            @if($action === 'edit') @method('PUT') @endif

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $media->title) }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required x-model="mediaType"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white">
                        @foreach(['photo' => 'Photo (une image)', 'gallery' => 'Galerie (plusieurs images)', 'video' => 'Vidéo', 'podcast' => 'Podcast'] as $value => $label)
                            <option value="{{ $value }}" @selected($currentType === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Durée</label>
                    <input type="text" name="duration" value="{{ old('duration', $media->duration) }}" placeholder="ex. 24:15"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Source / crédit</label>
                <input type="text" name="source" value="{{ old('source', $media->source) }}"
                       placeholder="ex. ROPPA, 3AO"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] resize-y">{{ old('description', $media->description) }}</textarea>
            </div>

            {{-- URL externe (vidéo / podcast) --}}
            <div x-show="mediaType === 'video' || mediaType === 'podcast'" x-cloak
                 class="p-4 rounded-xl space-y-2"
                 :class="mediaType === 'video' ? 'bg-red-50/50 border border-red-100' : 'bg-purple-50/50 border border-purple-100'">
                <label class="block text-xs font-semibold text-gray-700">
                    <span x-text="mediaType === 'video' ? 'URL vidéo (YouTube ou Vimeo)' : 'Lien externe (optionnel)'"></span>
                </label>
                <input type="url" name="url" value="{{ old('url', $media->url) }}"
                       :placeholder="mediaType === 'video' ? 'https://www.youtube.com/watch?v=…' : 'https://…'"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                <p class="text-xs text-gray-500" x-show="mediaType === 'video'">
                    Affichée en iframe sur le site. Vous pouvez aussi téléverser un MP4 ci-dessous.
                </p>
            </div>

            {{-- Photo unique --}}
            <div x-show="mediaType === 'photo'" x-cloak class="p-4 bg-gray-50 border border-gray-100 rounded-xl space-y-2">
                <label class="block text-xs font-semibold text-gray-700">Image <span class="text-red-500">*</span></label>
                @if($action === 'edit' && $media->file_path && $media->type === 'photo')
                    <a href="{{ asset('storage/'.$media->file_path) }}" target="_blank" class="block mb-2">
                        <img src="{{ asset('storage/'.$media->file_path) }}" class="h-32 rounded-lg object-cover" alt="">
                    </a>
                @endif
                <input type="file" name="file_path" accept="image/jpeg,image/png,image/webp,image/gif"
                       class="w-full text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#F8F5F0] file:text-[#2D6A4F] file:font-semibold hover:file:bg-green-50">
                <p class="text-xs text-gray-400">JPG, PNG, WebP ou GIF · max 50 Mo par image</p>
            </div>

            {{-- Galerie : plusieurs images --}}
            <div x-show="mediaType === 'gallery'" x-cloak class="p-4 bg-blue-50/50 border border-blue-100 rounded-xl space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Images de la galerie <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-2">
                        Sélectionnez plusieurs fichiers en une fois (Ctrl+clic ou Maj+clic). Elles s’affichent en grille sur le site avec visionneuse.
                    </p>
                    <input type="file" name="gallery_images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif"
                           class="w-full text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-white file:text-[#2D6A4F] file:font-semibold hover:file:bg-blue-50">
                    <p class="text-xs text-gray-400 mt-1">Jusqu’à 30 images · max 50 Mo chacune</p>
                </div>

                @if($action === 'edit' && $galleryPhotos->count())
                    <div>
                        <p class="text-xs font-semibold text-gray-600 mb-2">
                            Images actuelles ({{ $galleryPhotos->count() }}) · cochez pour retirer
                        </p>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                            @foreach($galleryPhotos as $photo)
                                <label class="relative group cursor-pointer rounded-lg overflow-hidden border-2 border-transparent has-[:checked]:border-red-500">
                                    <input type="checkbox" name="remove_gallery_photos[]" value="{{ $photo->id }}"
                                           class="absolute top-1 right-1 z-10 w-4 h-4 accent-red-600">
                                    <img src="{{ asset('storage/'.$photo->file_path) }}" alt=""
                                         class="w-full aspect-square object-cover">
                                    <span class="absolute inset-0 bg-red-600/0 group-has-[:checked]:bg-red-600/40 transition-colors"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Vidéo / podcast : fichier principal --}}
            <div x-show="mediaType === 'video' || mediaType === 'podcast'" x-cloak
                 class="p-4 bg-gray-50 border border-gray-100 rounded-xl space-y-2">
                <label class="block text-xs font-semibold text-gray-700"
                       x-text="mediaType === 'podcast' ? 'Fichier audio (optionnel)' : 'Fichier vidéo MP4 (optionnel)'"></label>
                @if($action === 'edit' && $media->file_path)
                    <p class="text-xs text-[#2D6A4F] mb-1">
                        Fichier actuel :
                        <a href="{{ asset('storage/'.$media->file_path) }}" target="_blank" class="underline">ouvrir</a>
                    </p>
                @endif
                <input type="file" name="file_path"
                       :accept="mediaType === 'podcast' ? 'audio/mpeg,audio/mp4,audio/ogg' : 'video/mp4,video/webm'"
                       class="w-full text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#F8F5F0] file:text-[#2D6A4F] file:font-semibold hover:file:bg-green-50">
            </div>

            {{-- Vignette (cartes liste) --}}
            <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl space-y-2">
                <label class="block text-xs font-semibold text-gray-700">Vignette pour les listes (optionnel)</label>
                @if($action === 'edit' && $media->thumbnail)
                    <img src="{{ asset('storage/'.$media->thumbnail) }}" class="h-20 rounded-lg mb-2 object-cover" alt="">
                @endif
                <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#F8F5F0] file:text-[#2D6A4F] file:font-semibold hover:file:bg-green-50">
                <p class="text-xs text-gray-400" x-show="mediaType === 'gallery'">
                    Si vide, la première image de la galerie sera utilisée automatiquement.
                </p>
                <p class="text-xs text-gray-400" x-show="mediaType !== 'gallery'">
                    Utilisée sur les cartes du hub Multimédia · max 4 Mo
                </p>
            </div>

            <div class="flex flex-col gap-3 p-4 bg-[#F8F5F0] border border-green-100 rounded-xl">
                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" id="is_published" name="is_published" value="1"
                           {{ old('is_published', $media->is_published) ? 'checked' : '' }}
                           class="w-4 h-4 text-[#2D6A4F] rounded">
                    <label for="is_published" class="text-sm font-medium text-gray-700">Publier immédiatement</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="hidden" name="featured_in_gallery" value="0">
                    <input type="checkbox" id="featured_in_gallery" name="featured_in_gallery" value="1"
                           {{ old('featured_in_gallery', $media->featured_in_gallery) ? 'checked' : '' }}
                           class="w-4 h-4 text-[#2D6A4F] rounded">
                    <label for="featured_in_gallery" class="text-sm font-medium text-gray-700">Mettre en avant dans le slider de la galerie</label>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ordre dans le slider</label>
                    <input type="number" name="gallery_sort_order" min="0" max="999"
                           value="{{ old('gallery_sort_order', $media->gallery_sort_order ?? 0) }}"
                           class="w-24 px-3 py-2 text-sm border border-gray-200 rounded-xl">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                    {{ $action === 'create' ? 'Créer le média' : 'Enregistrer' }}
                </button>
                <a href="{{ route('admin.medias.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
