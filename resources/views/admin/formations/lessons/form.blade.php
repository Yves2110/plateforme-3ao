@extends('admin.layouts.admin')

@section('title', isset($lesson) ? 'Modifier la leçon' : 'Nouvelle leçon')
@section('page-title', isset($lesson) ? 'Modifier la leçon' : 'Nouvelle leçon')
@section('page-subtitle', $formation->title)

@section('content')
<div class="py-6 max-w-4xl mx-auto">

    {{-- Navigation --}}
    <div class="flex items-center gap-2 text-sm mb-6">
        <a href="{{ route('admin.formations.index') }}" class="text-gray-500 hover:text-gray-700">Formations</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('admin.formations.lessons.index', $formation) }}" class="text-gray-500 hover:text-gray-700">Leçons</a>
        <span class="text-gray-400">/</span>
        <span class="text-[#2D6A4F] font-medium">{{ isset($lesson) ? 'Modifier' : 'Nouveau' }}</span>
    </div>

    <form method="POST" action="{{ isset($lesson) ? route('admin.formations.lessons.update', [$formation, $lesson]) : route('admin.formations.lessons.store', $formation) }}"
          enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-100 p-6 space-y-6">
        @csrf
        @if(isset($lesson))
            @method('PUT')
        @endif

        {{-- Module --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Module *</label>
            <select name="module_id" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                <option value="">Sélectionner un module</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod->id }}" {{ old('module_id', $lesson->module_id ?? request('module')) == $mod->id ? 'selected' : '' }}>
                        {{ $mod->title }}
                    </option>
                @endforeach
            </select>
            @error('module_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Titre --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Titre de la leçon *</label>
            <input type="text" name="title" value="{{ old('title', $lesson->title ?? '') }}" required
                   class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                   placeholder="Ex: Introduction aux principes de l'agroécologie">
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Type de contenu --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type de contenu *</label>
            <div class="grid grid-cols-5 gap-2">
                @foreach(['video' => 'Vidéo', 'pdf' => 'PDF', 'text' => 'Texte', 'quiz' => 'Quiz', 'audio' => 'Audio'] as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="{{ $value }}" {{ old('type', $lesson->type ?? 'text') === $value ? 'checked' : '' }} class="peer sr-only">
                        <div class="p-3 text-center rounded-xl border-2 border-gray-200 peer-checked:border-[#2D6A4F] peer-checked:bg-[#2D6A4F]/5 hover:bg-gray-50 transition-colors">
                            <span class="text-sm font-medium {{ old('type', $lesson->type ?? 'text') === $value ? 'text-[#2D6A4F]' : 'text-gray-700' }}">{{ $label }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="2"
                      class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                      placeholder="Brève description de la leçon">{{ old('description', $lesson->description ?? '') }}</textarea>
        </div>

        {{-- Contenu selon le type --}}
        <div id="content-fields" class="space-y-4">
            {{-- Vidéo --}}
            <div id="video-fields" class="type-field hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">URL de la vidéo</label>
                <input type="url" name="video_url" value="{{ old('video_url', $lesson->video_url ?? '') }}"
                       class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                       placeholder="https://youtube.com/watch?v=... ou https://vimeo.com/...">
                <p class="text-xs text-gray-500 mt-1">YouTube, Vimeo ou lien direct</p>
            </div>

            {{-- Fichier upload --}}
            <div id="file-field" class="type-field hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fichier</label>
                <input type="file" name="file" id="file-input"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#2D6A4F] file:text-white hover:file:bg-[#40916C]">
                @if(isset($lesson) && $lesson->file_path)
                    <p class="text-sm text-gray-600 mt-2">
                        Fichier actuel: <a href="{{ asset('storage/' . $lesson->file_path) }}" target="_blank" class="text-[#2D6A4F] hover:underline">Voir</a>
                    </p>
                @endif
                <p class="text-xs text-gray-500 mt-1">Max 50MB. Types: MP4, PDF, MP3</p>
            </div>

            {{-- Contenu texte --}}
            <div id="text-fields" class="type-field hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenu texte</label>
                <textarea name="content" rows="8"
                          class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                          placeholder="Contenu de la leçon...">{{ old('content', $lesson->content ?? '') }}</textarea>
            </div>
        </div>

        {{-- Durée estimée --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Durée estimée (minutes)</label>
            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes ?? '') }}" min="1"
                   class="w-32 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                   placeholder="15">
        </div>

        {{-- Ordre --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ordre dans le module</label>
            <input type="number" name="order" value="{{ old('order', $lesson->order ?? ($nextOrder ?? 0)) }}" min="0"
                   class="w-32 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
        </div>

        {{-- Publication --}}
        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
            <input type="checkbox" name="is_published" value="1" id="is_published"
                   {{ old('is_published', $lesson->is_published ?? false) ? 'checked' : '' }}
                   class="w-5 h-5 text-[#2D6A4F] rounded border-gray-300 focus:ring-[#52B788]">
            <label for="is_published" class="text-gray-700 cursor-pointer">
                <span class="font-medium">Publier la leçon</span>
                <p class="text-sm text-gray-500">Rendre cette leçon visible par les apprenants</p>
            </label>
        </div>

        {{-- Boutons --}}
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-[#2D6A4F] text-white font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                {{ isset($lesson) ? 'Enregistrer les modifications' : 'Créer la leçon' }}
            </button>
            <a href="{{ route('admin.formations.lessons.index', $formation) }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                Annuler
            </a>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    function updateFields() {
        const type = document.querySelector('input[name="type"]:checked').value;

        document.querySelectorAll('.type-field').forEach(el => el.classList.add('hidden'));

        if (type === 'video') {
            document.getElementById('video-fields').classList.remove('hidden');
        } else if (['video', 'pdf', 'audio'].includes(type)) {
            document.getElementById('file-field').classList.remove('hidden');

            // Mettre à jour l'accept du file input
            const fileInput = document.getElementById('file-input');
            if (type === 'video') fileInput.accept = '.mp4,.webm,.ogg';
            if (type === 'pdf') fileInput.accept = '.pdf';
            if (type === 'audio') fileInput.accept = '.mp3,.wav,.ogg';
        } else if (type === 'text') {
            document.getElementById('text-fields').classList.remove('hidden');
        }
    }

    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', updateFields);
    });

    // Initialisation
    updateFields();
</script>
@endpush
