@extends('admin.layouts.admin')

@section('title', isset($quiz) ? 'Modifier le quiz' : 'Nouveau quiz')
@section('page-title', isset($quiz) ? 'Modifier le quiz' : 'Nouveau quiz')
@section('page-subtitle', $formation->title)

@section('content')
<div class="py-6 max-w-4xl mx-auto">

    {{-- Navigation --}}
    <div class="flex items-center gap-2 text-sm mb-6">
        <a href="{{ route('admin.formations.index') }}" class="text-gray-500 hover:text-gray-700">Formations</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('admin.formations.quizzes.index', $formation) }}" class="text-gray-500 hover:text-gray-700">Quiz</a>
        <span class="text-gray-400">/</span>
        <span class="text-[#2D6A4F] font-medium">{{ isset($quiz) ? 'Modifier' : 'Nouveau' }}</span>
    </div>

    <form method="POST" action="{{ isset($quiz) ? route('admin.formations.quizzes.update', [$formation, $quiz]) : route('admin.formations.quizzes.store', $formation) }}"
          class="bg-white rounded-2xl border border-gray-100 p-6 space-y-6">
        @csrf
        @if(isset($quiz))
            @method('PUT')
        @endif

        {{-- Informations du quiz --}}
        <div class="border-b border-gray-100 pb-6">
            <h3 class="font-semibold text-gray-800 mb-4">Informations du quiz</h3>

            <div class="grid grid-cols-2 gap-4">
                {{-- Module --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Module *</label>
                    <select name="module_id" id="module_id" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                        <option value="">Sélectionner un module</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod->id }}" {{ old('module_id', $quiz->lesson->module_id ?? '') == $mod->id ? 'selected' : '' }}>
                                {{ $mod->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Leçon --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Leçon *</label>
                    <select name="lesson_id" id="lesson_id" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                        <option value="">Sélectionner une leçon</option>
                        @if(isset($quiz))
                            @foreach($modules->firstWhere('id', $quiz->lesson->module_id)->lessons ?? [] as $lesson)
                                <option value="{{ $lesson->id }}" {{ $quiz->lesson_id == $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->title }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            {{-- Titre --}}
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre du quiz *</label>
                <input type="text" name="title" value="{{ old('title', $quiz->title ?? '') }}" required
                       class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                       placeholder="Ex: Quiz de validation - Module 1">
            </div>

            {{-- Description --}}
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2"
                          class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                          placeholder="Instructions ou description du quiz">{{ old('description', $quiz->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-4">
                {{-- Score minimum --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Score minimum (%)</label>
                    <input type="number" name="passing_score" value="{{ old('passing_score', $quiz->passing_score ?? 70) }}" min="0" max="100" required
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>

                {{-- Temps limite --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Temps limite (min)</label>
                    <input type="number" name="time_limit_minutes" value="{{ old('time_limit_minutes', $quiz->time_limit_minutes ?? '') }}" min="1"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]"
                           placeholder="Sans limite">
                </div>

                {{-- Max tentatives --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max tentatives</label>
                    <input type="number" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts ?? 3) }}" min="1" required
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
            </div>

            {{-- Options --}}
            <div class="flex gap-6 mt-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $quiz->is_published ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 text-[#2D6A4F] rounded focus:ring-[#52B788]">
                    <span class="text-sm text-gray-700">Publier le quiz</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="show_correct_answers" value="1" {{ old('show_correct_answers', $quiz->show_correct_answers ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 text-[#2D6A4F] rounded focus:ring-[#52B788]">
                    <span class="text-sm text-gray-700">Afficher les réponses correctes après</span>
                </label>
            </div>
        </div>

        {{-- Questions --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Questions</h3>
                <button type="button" id="add-question" class="px-3 py-2 bg-[#2D6A4F] text-white text-sm rounded-lg hover:bg-[#40916C] transition-colors">
                    + Ajouter une question
                </button>
            </div>

            <div id="questions-container" class="space-y-4">
                @if(isset($quiz) && $quiz->questions)
                    @foreach($quiz->questions as $qIndex => $question)
                    <div class="question-card p-4 bg-gray-50 rounded-xl" data-index="{{ $qIndex }}">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-medium text-gray-700">Question {{ $qIndex + 1 }}</span>
                            <button type="button" class="remove-question text-red-500 hover:text-red-700">Supprimer</button>
                        </div>

                        <div class="space-y-3">
                            <input type="text" name="questions[{{ $qIndex }}][question]" value="{{ $question->question }}" required
                                   class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl" placeholder="Texte de la question">

                            <div class="grid grid-cols-3 gap-3">
                                <select name="questions[{{ $qIndex }}][type]" class="px-4 py-2 bg-white border border-gray-200 rounded-xl question-type">
                                    <option value="single_choice" {{ $question->type === 'single_choice' ? 'selected' : '' }}>Choix unique</option>
                                    <option value="multiple_choice" {{ $question->type === 'multiple_choice' ? 'selected' : '' }}>Choix multiple</option>
                                    <option value="true_false" {{ $question->type === 'true_false' ? 'selected' : '' }}>Vrai/Faux</option>
                                    <option value="text" {{ $question->type === 'text' ? 'selected' : '' }}>Texte libre</option>
                                </select>
                                <input type="number" name="questions[{{ $qIndex }}][points]" value="{{ $question->points }}" min="1"
                                       class="px-4 py-2 bg-white border border-gray-200 rounded-xl" placeholder="Points">
                                <input type="text" name="questions[{{ $qIndex }}][explanation]" value="{{ $question->explanation }}"
                                       class="px-4 py-2 bg-white border border-gray-200 rounded-xl" placeholder="Explication (optionnel)">
                            </div>

                            <div class="answers-container space-y-2">
                                @foreach($question->answers as $aIndex => $answer)
                                <div class="flex items-center gap-2 answer-row">
                                    <input type="text" name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][answer]" value="{{ $answer->answer }}" required
                                           class="flex-1 px-3 py-2 bg-white border border-gray-200 rounded-lg" placeholder="Réponse">
                                    <label class="flex items-center gap-1">
                                        <input type="checkbox" name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][is_correct]" value="1" {{ $answer->is_correct ? 'checked' : '' }}
                                               class="w-4 h-4 text-green-600 rounded">
                                        <span class="text-sm">Correcte</span>
                                    </label>
                                    <button type="button" class="remove-answer text-red-500 hover:text-red-700">×</button>
                                </div>
                                @endforeach
                            </div>

                            <button type="button" class="add-answer text-sm text-[#2D6A4F] hover:underline">+ Ajouter une réponse</button>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Boutons --}}
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-[#2D6A4F] text-white font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                {{ isset($quiz) ? 'Enregistrer les modifications' : 'Créer le quiz' }}
            </button>
            <a href="{{ route('admin.formations.quizzes.index', $formation) }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                Annuler
            </a>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    let questionIndex = {{ isset($quiz) ? $quiz->questions->count() : 0 }};

    // Template pour une nouvelle question
    function createQuestionTemplate(index) {
        return `
            <div class="question-card p-4 bg-gray-50 rounded-xl" data-index="${index}">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-medium text-gray-700">Question ${index + 1}</span>
                    <button type="button" class="remove-question text-red-500 hover:text-red-700">Supprimer</button>
                </div>

                <div class="space-y-3">
                    <input type="text" name="questions[${index}][question]" required
                           class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl" placeholder="Texte de la question">

                    <div class="grid grid-cols-3 gap-3">
                        <select name="questions[${index}][type]" class="px-4 py-2 bg-white border border-gray-200 rounded-xl question-type">
                            <option value="single_choice">Choix unique</option>
                            <option value="multiple_choice">Choix multiple</option>
                            <option value="true_false">Vrai/Faux</option>
                            <option value="text">Texte libre</option>
                        </select>
                        <input type="number" name="questions[${index}][points]" value="1" min="1"
                               class="px-4 py-2 bg-white border border-gray-200 rounded-xl" placeholder="Points">
                        <input type="text" name="questions[${index}][explanation]"
                               class="px-4 py-2 bg-white border border-gray-200 rounded-xl" placeholder="Explication (optionnel)">
                    </div>

                    <div class="answers-container space-y-2">
                        <div class="flex items-center gap-2 answer-row">
                            <input type="text" name="questions[${index}][answers][0][answer]" required
                                   class="flex-1 px-3 py-2 bg-white border border-gray-200 rounded-lg" placeholder="Réponse">
                            <label class="flex items-center gap-1">
                                <input type="checkbox" name="questions[${index}][answers][0][is_correct]" value="1"
                                       class="w-4 h-4 text-green-600 rounded">
                                <span class="text-sm">Correcte</span>
                            </label>
                            <button type="button" class="remove-answer text-red-500 hover:text-red-700">×</button>
                        </div>
                        <div class="flex items-center gap-2 answer-row">
                            <input type="text" name="questions[${index}][answers][1][answer]" required
                                   class="flex-1 px-3 py-2 bg-white border border-gray-200 rounded-lg" placeholder="Réponse">
                            <label class="flex items-center gap-1">
                                <input type="checkbox" name="questions[${index}][answers][1][is_correct]" value="1"
                                       class="w-4 h-4 text-green-600 rounded">
                                <span class="text-sm">Correcte</span>
                            </label>
                            <button type="button" class="remove-answer text-red-500 hover:text-red-700">×</button>
                        </div>
                    </div>

                    <button type="button" class="add-answer text-sm text-[#2D6A4F] hover:underline">+ Ajouter une réponse</button>
                </div>
            </div>
        `;
    }

    // Ajouter une question
    document.getElementById('add-question').addEventListener('click', function() {
        const container = document.getElementById('questions-container');
        container.insertAdjacentHTML('beforeend', createQuestionTemplate(questionIndex));
        questionIndex++;
        updateQuestionNumbers();
    });

    // Supprimer une question
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-question')) {
            e.target.closest('.question-card').remove();
            updateQuestionNumbers();
        }
    });

    // Ajouter une réponse
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-answer')) {
            const container = e.target.closest('.question-card').querySelector('.answers-container');
            const qIndex = e.target.closest('.question-card').dataset.index;
            const aIndex = container.querySelectorAll('.answer-row').length;

            container.insertAdjacentHTML('beforeend', `
                <div class="flex items-center gap-2 answer-row">
                    <input type="text" name="questions[${qIndex}][answers][${aIndex}][answer]" required
                           class="flex-1 px-3 py-2 bg-white border border-gray-200 rounded-lg" placeholder="Réponse">
                    <label class="flex items-center gap-1">
                        <input type="checkbox" name="questions[${qIndex}][answers][${aIndex}][is_correct]" value="1"
                               class="w-4 h-4 text-green-600 rounded">
                        <span class="text-sm">Correcte</span>
                    </label>
                    <button type="button" class="remove-answer text-red-500 hover:text-red-700">×</button>
                </div>
            `);
        }
    });

    // Supprimer une réponse
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-answer')) {
            e.target.closest('.answer-row').remove();
        }
    });

    // Mettre à jour les numéros de questions
    function updateQuestionNumbers() {
        document.querySelectorAll('.question-card').forEach((card, index) => {
            card.dataset.index = index;
            card.querySelector('span.font-medium').textContent = `Question ${index + 1}`;

            // Mettre à jour les names des inputs
            card.querySelectorAll('[name^="questions["]').forEach(input => {
                const newName = input.name.replace(/questions\[\d+\]/, `questions[${index}]`);
                input.name = newName;
            });
        });
        questionIndex = document.querySelectorAll('.question-card').length;
    }

    // Chargement dynamique des leçons selon le module
    document.getElementById('module_id').addEventListener('change', function() {
        const moduleId = this.value;
        const lessonSelect = document.getElementById('lesson_id');

        lessonSelect.innerHTML = '<option value="">Chargement...</option>';

        fetch(`{{ route('admin.formations.quizzes.get-lessons', $formation) }}?module_id=${moduleId}`)
            .then(r => r.json())
            .then(lessons => {
                lessonSelect.innerHTML = '<option value="">Sélectionner une leçon</option>';
                lessons.forEach(lesson => {
                    lessonSelect.innerHTML += `<option value="${lesson.id}">${lesson.title}</option>`;
                });
            });
    });

    // Ajouter une question initiale si vide
    @if(!isset($quiz) || $quiz->questions->isEmpty())
        document.getElementById('add-question').click();
    @endif
</script>
@endpush
