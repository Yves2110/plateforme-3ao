@extends('admin.layouts.admin')

@section('title', $action === 'create' ? 'Nouveau média' : 'Éditer le média')
@section('page-title', $action === 'create' ? 'Nouveau média' : 'Éditer : ' . $media->title)

@section('content')
<div class="py-6 max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
        @endif

        <form action="{{ $action === 'create' ? route('admin.medias.store') : route('admin.medias.update', $media) }}"
              method="POST" class="space-y-4">
            @csrf
            @if($action === 'edit') @method('PUT') @endif

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $media->title) }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white">
                        @foreach(['photo','video','podcast','gallery'] as $t)
                            <option value="{{ $t }}" {{ old('type', $media->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Durée</label>
                    <input type="text" name="duration" value="{{ old('duration', $media->duration) }}" placeholder="ex: 24:15"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">URL (YouTube, Vimeo, fichier audio…)</label>
                    <input type="url" name="url" value="{{ old('url', $media->url) }}"
                           placeholder="https://…"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Source</label>
                    <input type="text" name="source" value="{{ old('source', $media->source) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] resize-y">{{ old('description', $media->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" id="is_published" name="is_published" value="1"
                       {{ old('is_published', $media->is_published) ? 'checked' : '' }}
                       class="w-4 h-4 text-[#2D6A4F] rounded">
                <label for="is_published" class="text-sm font-medium text-gray-700">Publier immédiatement</label>
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
