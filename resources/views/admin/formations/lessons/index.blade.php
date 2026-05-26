@extends('admin.layouts.admin')

@section('title', "Leçons de {$formation->title}")
@section('page-title', 'Leçons de la formation')
@section('page-subtitle', $formation->title)

@section('header-actions')
    <a href="{{ route('admin.formations.lessons.create', $formation) }}" class="flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white rounded-xl hover:bg-[#40916C] transition-colors text-sm font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvelle leçon
    </a>
@endsection

@section('content')
<div class="py-6 space-y-6">

    {{-- Navigation --}}
    <div class="flex items-center gap-2 text-sm">
        <a href="{{ route('admin.formations.index') }}" class="text-gray-500 hover:text-gray-700">Formations</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-700">{{ Str::limit($formation->title, 40) }}</span>
        <span class="text-gray-400">/</span>
        <span class="text-[#2D6A4F] font-medium">Leçons</span>
    </div>

    {{-- Liste des leçons par module --}}
    <div class="space-y-6">
        @forelse($modules as $module)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-[#2D6A4F] text-white flex items-center justify-center font-bold text-sm">
                            {{ $loop->iteration }}
                        </span>
                        <h3 class="font-semibold text-gray-800">{{ $module->title }}</h3>
                        @if($module->is_published)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Publié</span>
                        @else
                            <span class="px-2 py-0.5 bg-gray-200 text-gray-600 text-xs rounded-full">Brouillon</span>
                        @endif
                    </div>
                    <span class="text-sm text-gray-500">{{ $module->lessons->count() }} leçon(s)</span>
                </div>

                @if($module->lessons->isNotEmpty())
                    <div class="divide-y divide-gray-100 lessons-list" data-module-id="{{ $module->id }}">
                        @foreach($module->lessons as $lesson)
                        <div class="p-4 flex items-center gap-4 hover:bg-gray-50 transition-colors lesson-item" data-id="{{ $lesson->id }}">
                            <div class="cursor-move text-gray-400 hover:text-gray-600 handle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                            </div>

                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                @switch($lesson->type)
                                    @case('video')
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @break
                                    @case('pdf')
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @break
                                    @case('audio')
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                        @break
                                    @case('quiz')
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @break
                                    @default
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @endswitch
                            </div>

                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-800 truncate">{{ $lesson->title }}</h4>
                                <p class="text-sm text-gray-500">
                                    {{ ucfirst($lesson->type) }}
                                    @if($lesson->duration_minutes)
                                        • {{ $lesson->duration_minutes }} min
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($lesson->is_published)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Publiée</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Brouillon</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1">
                                <form method="POST" action="{{ route('admin.formations.lessons.toggle-publish', [$formation, $lesson]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="{{ $lesson->is_published ? 'Dépublier' : 'Publier' }}">
                                        @if($lesson->is_published)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        @endif
                                    </button>
                                </form>

                                <a href="{{ route('admin.formations.lessons.edit', [$formation, $lesson]) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                <form method="POST" action="{{ route('admin.formations.lessons.destroy', [$formation, $lesson]) }}" class="inline" onsubmit="return confirm('Supprimer cette leçon ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <p>Aucune leçon dans ce module</p>
                        <a href="{{ route('admin.formations.lessons.create', $formation) }}?module={{ $module->id }}" class="inline-flex items-center gap-2 mt-2 text-[#2D6A4F] hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Ajouter une leçon
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                <div class="text-6xl mb-4">📚</div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucun module</h3>
                <p class="text-gray-500 mb-4 max-w-md mx-auto">Vous devez d'abord créer des modules avant d'ajouter des leçons.</p>
                <a href="{{ route('admin.formations.modules.index', $formation) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white rounded-xl hover:bg-[#40916C] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Gérer les modules
                </a>
            </div>
        @endforelse
    </div>

    {{-- Navigation secondaire --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.formations.modules.index', $formation) }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Gérer les modules
        </a>
        <a href="{{ route('admin.formations.quizzes.index', $formation) }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Gérer les quiz
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.querySelectorAll('.lessons-list').forEach(list => {
        new Sortable(list, {
            handle: '.handle',
            animation: 150,
            group: 'lessons',
            onEnd: function() {
                const lessons = [];
                document.querySelectorAll('.lesson-item').forEach(item => {
                    lessons.push(item.dataset.id);
                });

                fetch('{{ route('admin.formations.lessons.reorder', $formation) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ lessons })
                });
            }
        });
    });
</script>
@endpush
