@extends('admin.layouts.admin')

@section('title', isset($module) ? 'Modifier le module' : 'Nouveau module')
@section('page-title', isset($module) ? 'Modifier le module' : 'Nouveau module')
@section('page-subtitle', $formation->title)

@section('content')
<div class="py-6 max-w-3xl mx-auto">

    {{-- Navigation --}}
    <div class="flex items-center gap-2 text-sm mb-6">
        <a href="{{ route('admin.formations.index') }}" class="text-gray-500 hover:text-gray-700">Formations</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('admin.formations.modules.index', $formation) }}" class="text-gray-500 hover:text-gray-700">Modules</a>
        <span class="text-gray-400">/</span>
        <span class="text-[#2D6A4F] font-medium">{{ isset($module) ? 'Modifier' : 'Nouveau' }}</span>
    </div>

    <form method="POST" action="{{ isset($module) ? route('admin.formations.modules.update', [$formation, $module]) : route('admin.formations.modules.store', $formation) }}" class="bg-white rounded-2xl border border-gray-100 p-6 space-y-6">
        @csrf
        @if(isset($module))
            @method('PUT')
        @endif

        {{-- Titre --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Titre du module *</label>
            <input type="text" name="title" value="{{ old('title', $module->title ?? '') }}" required
                   class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                   placeholder="Ex: Module 1: Introduction à l'agroécologie">
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                      placeholder="Description du module et de son contenu">{{ old('description', $module->description ?? '') }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Ordre --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
            <input type="number" name="order" value="{{ old('order', $module->order ?? ($nextOrder ?? 1)) }}" min="0"
                   class="w-32 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            <p class="text-xs text-gray-500 mt-1">Position du module dans la liste (0 = premier)</p>
            @error('order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Publication --}}
        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
            <input type="checkbox" name="is_published" value="1" id="is_published"
                   {{ old('is_published', $module->is_published ?? false) ? 'checked' : '' }}
                   class="w-5 h-5 text-[#2D6A4F] rounded border-gray-300 focus:ring-[#52B788]">
            <label for="is_published" class="text-gray-700 cursor-pointer">
                <span class="font-medium">Publier le module</span>
                <p class="text-sm text-gray-500">Rendre ce module visible par les apprenants</p>
            </label>
        </div>

        {{-- Boutons --}}
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-[#2D6A4F] text-white font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                {{ isset($module) ? 'Enregistrer les modifications' : 'Créer le module' }}
            </button>
            <a href="{{ route('admin.formations.modules.index', $formation) }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                Annuler
            </a>
        </div>
    </form>

</div>
@endsection
