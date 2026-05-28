@extends('admin.layouts.admin')

@section('title', $action === 'create' ? 'Nouveau partenaire' : 'Modifier le partenaire')
@section('page-title', $action === 'create' ? 'Nouveau logo partenaire' : 'Modifier le partenaire')

@section('content')
<div class="py-6 max-w-xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
        @endif

        <form action="{{ $action === 'create' ? route('admin.home-partners.store') : route('admin.home-partners.update', $partner) }}"
              method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if($action === 'edit') @method('PUT') @endif

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nom du partenaire *</label>
                <input type="text" name="name" value="{{ old('name', $partner->name) }}" required
                       placeholder="ex. CIRAD"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Site web (optionnel)</label>
                <input type="url" name="website_url" value="{{ old('website_url', $partner->website_url) }}"
                       placeholder="https://www.exemple.org"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Ordre d’affichage</label>
                <input type="number" name="sort_order" min="0" max="99"
                       value="{{ old('sort_order', $partner->sort_order ?? 0) }}"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Logo
                </label>
                @if($action === 'edit' && $partner->logoUrl())
                    <div class="flex items-center justify-center h-24 mb-2 bg-gray-50 rounded-xl border border-gray-100">
                        <img src="{{ $partner->logoUrl() }}" class="max-h-16 max-w-full object-contain px-4" alt="{{ $partner->name }}">
                    </div>
                @endif
                <input type="file" name="logo" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#F8F5F0] file:text-[#2D6A4F] file:font-semibold">
                <p class="text-xs text-gray-400 mt-1">PNG ou WebP avec fond transparent de préférence — max 4 Mo</p>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $partner->is_active ?? true) ? 'checked' : '' }}
                       class="w-4 h-4 text-[#2D6A4F] rounded">
                <label for="is_active" class="text-sm text-gray-700">Actif (visible sur l’accueil)</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C]">Enregistrer</button>
                <a href="{{ route('admin.hero-slides.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl">Retour</a>
            </div>
        </form>
    </div>
</div>
@endsection
