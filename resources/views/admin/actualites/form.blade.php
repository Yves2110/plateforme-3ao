@extends('admin.layouts.admin')

@section('title', $action === 'create' ? 'Nouvelle actualité' : 'Éditer l\'actualité')
@section('page-title', $action === 'create' ? 'Nouvelle actualité' : 'Éditer : ' . $actualite->title)

@section('content')
<div class="py-6 max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
        @endif

        <form action="{{ $action === 'create' ? route('admin.actualites.store') : route('admin.actualites.update', $actualite) }}"
              method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if($action === 'edit') @method('PUT') @endif

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $actualite->title) }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            @php
                $currentCategory = \App\Support\ActualiteCategories::normalizeLabel(old('category', $actualite->category));
            @endphp
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Type d'actualité <span class="text-red-500">*</span></label>
                <select name="category" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white">
                    @foreach(\App\Support\ActualiteCategories::all() as $label => $meta)
                        <option value="{{ $label }}" {{ $currentCategory === $label ? 'selected' : '' }}>
                            {{ $label }}   {{ $meta['description'] }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Correspond au badge affiché sur le site (Actualité, Annonce, Événement, Financement, Publication).</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Contenu <span class="text-red-500">*</span></label>
                <textarea name="content" rows="12" required
                          class="w-full px-3 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] resize-y font-mono">{{ old('content', $actualite->content) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Image de couverture</label>
                @if($action === 'edit' && $actualite->thumbnail)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$actualite->thumbnail) }}" alt="Thumbnail" class="h-24 rounded-lg object-cover">
                    </div>
                @endif
                <input type="file" name="thumbnail" accept="image/*"
                       class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-[#F8F5F0] file:text-[#2D6A4F] file:font-semibold hover:file:bg-green-50">
                <p class="text-xs text-gray-400 mt-1">JPG, PNG ou WebP   max 4 Mo. Sera converti en WebP automatiquement.</p>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" id="is_published" name="is_published" value="1"
                       {{ old('is_published', $actualite->is_published) ? 'checked' : '' }}
                       class="w-4 h-4 text-[#2D6A4F] rounded">
                <label for="is_published" class="text-sm font-medium text-gray-700">Publier immédiatement</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                    {{ $action === 'create' ? 'Créer l\'actualité' : 'Enregistrer' }}
                </button>
                <a href="{{ route('admin.actualites.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
