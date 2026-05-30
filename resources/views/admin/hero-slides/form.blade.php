@extends('admin.layouts.admin')

@section('title', $action === 'create' ? 'Nouvelle slide' : 'Modifier la slide')
@section('page-title', $action === 'create' ? 'Nouvelle image slider' : 'Modifier la slide')

@section('content')
<div class="py-6 max-w-xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
        @endif

        <form action="{{ $action === 'create' ? route('admin.hero-slides.store') : route('admin.hero-slides.update', $slide) }}"
              method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if($action === 'edit') @method('PUT') @endif

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Libellé interne</label>
                <input type="text" name="title" value="{{ old('title', $slide->title) }}"
                       placeholder="ex. Champs au Sahel"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Ordre d’affichage</label>
                <input type="number" name="sort_order" min="0" max="99"
                       value="{{ old('sort_order', $slide->sort_order ?? 0) }}"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                <p class="text-xs text-gray-400 mt-1">Mettez <strong>0</strong> pour la slide avec le titre et les boutons.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Texte alternatif (accessibilité)</label>
                <input type="text" name="alt_text" value="{{ old('alt_text', $slide->alt_text) }}"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Image {{ $action === 'create' ? '*' : '' }}
                </label>
                @if($action === 'edit')
                    <img src="{{ $slide->imageUrl() }}" class="w-full max-h-48 object-cover rounded-xl mb-2" alt="">
                @endif
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#F8F5F0] file:text-[#2D6A4F] file:font-semibold">
                <p class="text-xs text-gray-400 mt-1">Format paysage recommandé (16:9) · JPG ou WebP, max 8 Mo</p>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $slide->is_active ?? true) ? 'checked' : '' }}
                       class="w-4 h-4 text-[#2D6A4F] rounded">
                <label for="is_active" class="text-sm text-gray-700">Active (visible sur l’accueil)</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C]">Enregistrer</button>
                <a href="{{ route('admin.hero-slides.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
