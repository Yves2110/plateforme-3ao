@extends('admin.layouts.admin')

@section('title', "Quiz de {$formation->title}")
@section('page-title', 'Quiz de la formation')
@section('page-subtitle', $formation->title)

@section('header-actions')
    <a href="{{ route('admin.formations.quizzes.create', $formation) }}" class="flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white rounded-xl hover:bg-[#40916C] transition-colors text-sm font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouveau quiz
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
        <span class="text-[#2D6A4F] font-medium">Quiz</span>
    </div>

    {{-- Liste des quiz --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        @if($quizzes->isNotEmpty())
            <div class="divide-y divide-gray-100">
                @foreach($quizzes as $quiz)
                <div class="p-5 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-800 truncate">{{ $quiz->title }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ $quiz->questions->count() }} question(s) •
                            Score min: {{ $quiz->passing_score }}% •
                            Max tentatives: {{ $quiz->max_attempts }}
                            @if($quiz->time_limit_minutes)
                                • {{ $quiz->time_limit_minutes }} min
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Leçon: {{ $quiz->lesson->title ?? 'N/A' }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($quiz->is_published)
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Publié</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Brouillon</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-1">
                        <form method="POST" action="{{ route('admin.formations.quizzes.toggle-publish', [$formation, $quiz]) }}" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="{{ $quiz->is_published ? 'Dépublier' : 'Publier' }}">
                                @if($quiz->is_published)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                @endif
                            </button>
                        </form>

                        <a href="{{ route('admin.formations.quizzes.edit', [$formation, $quiz]) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Modifier">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>

                        <form method="POST" action="{{ route('admin.formations.quizzes.destroy', [$formation, $quiz]) }}" class="inline" onsubmit="return confirm('Supprimer ce quiz ?')">
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
            <div class="text-center py-16">
                <div class="text-6xl mb-4">❓</div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucun quiz</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">Cette formation n'a pas encore de quiz. Créez des quiz pour évaluer les connaissances des apprenants.</p>
                <a href="{{ route('admin.formations.quizzes.create', $formation) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white rounded-xl hover:bg-[#40916C] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Créer un quiz
                </a>
            </div>
        @endif
    </div>

    {{-- Navigation secondaire --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.formations.modules.index', $formation) }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Gérer les modules
        </a>
        <a href="{{ route('admin.formations.lessons.index', $formation) }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18c1.141 0 2.544.313 3.5 1.11V8.253m0 9.747c.956-.797 2.359-1.11 3.5-1.11 1.246 0 2.832.477 4 1.253V6.253C16.832 5.477 15.246 5 14 5c-1.141 0-2.544.313-3.5 1.11"/></svg>
            Gérer les leçons
        </a>
    </div>

</div>
@endsection
