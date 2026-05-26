@extends('admin.layouts.admin')

@section('title', $action === 'create' ? 'Nouvelle ressource' : 'Éditer la ressource')
@section('page-title', $action === 'create' ? 'Nouvelle ressource' : 'Éditer : ' . $ressource->title)

@section('content')
<div class="py-6 max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
        @endif

        <form action="{{ $action === 'create' ? route('admin.ressources.store') : route('admin.ressources.update', $ressource) }}"
              method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if($action === 'edit') @method('PUT') @endif

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $ressource->title) }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white">
                        @foreach(['pdf','video','article','guide','rapport'] as $t)
                            <option value="{{ $t }}" {{ old('type', $ressource->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Langue <span class="text-red-500">*</span></label>
                    <select name="language" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white">
                        @foreach(['fr','en','pt','ar'] as $lang)
                            <option value="{{ $lang }}" {{ old('language', $ressource->language) === $lang ? 'selected' : '' }}>{{ strtoupper($lang) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Résumé</label>
                <textarea name="abstract" rows="4" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] resize-y">{{ old('abstract', $ressource->abstract) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fichier PDF</label>
                    @if($action === 'edit' && $ressource->file_path)
                        <a href="{{ asset('storage/'.$ressource->file_path) }}" target="_blank" class="text-xs text-[#2D6A4F] underline block mb-1">Fichier actuel</a>
                    @endif
                    <input type="file" name="file_path" accept=".pdf"
                           class="w-full text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#F8F5F0] file:text-[#2D6A4F] file:font-semibold hover:file:bg-green-50">
                    <p class="text-xs text-gray-400 mt-1">PDF max 20 Mo</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Image de couverture</label>
                    @if($action === 'edit' && $ressource->thumbnail)
                        <img src="{{ asset('storage/'.$ressource->thumbnail) }}" class="h-16 rounded mb-1 object-cover" alt="thumbnail">
                    @endif
                    <input type="file" name="thumbnail" accept="image/*"
                           class="w-full text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#F8F5F0] file:text-[#2D6A4F] file:font-semibold hover:file:bg-green-50">
                    <p class="text-xs text-gray-400 mt-1">Image max 4 Mo → WebP</p>
                </div>
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
