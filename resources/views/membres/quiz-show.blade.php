<x-app-layout>
    <x-slot name="title">{{ $quiz->title }}   {{ $formation->title }}</x-slot>
    <x-slot name="description">Quiz de validation des connaissances</x-slot>

    {{-- Header --}}
    <div class="bg-[#1A1A2E] text-white py-4 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="{{ route('learning.lesson', [$formation->slug, $lesson]) }}" class="text-white/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div class="flex-1">
                    <p class="text-white/50 text-sm">{{ $formation->title }}   {{ $lesson->title }}</p>
                    <h1 class="font-display font-semibold">{{ $quiz->title }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- Info du quiz --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
            <div class="flex flex-wrap items-center gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $quiz->questions->count() }} questions</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Score à atteindre: {{ $quiz->passing_score }}%</span>
                </div>
                @if($quiz->hasTimeLimit())
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Temps limité: {{ $quiz->time_limit_minutes }} min</span>
                    </div>
                @endif
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Tentatives restantes: {{ $remainingAttempts }}</span>
                </div>
            </div>

            @if($quiz->description)
                <p class="mt-4 text-gray-600">{{ $quiz->description }}</p>
            @endif
        </div>

        {{-- Historique des tentatives --}}
        @if($attempts->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
                <h2 class="font-display font-semibold text-[#1A1A2E] mb-4">Historique des tentatives</h2>
                <div class="space-y-3">
                    @foreach($attempts as $attempt)
                        <div class="flex items-center justify-between p-3 rounded-xl {{ $attempt->is_passed ? 'bg-green-50' : 'bg-gray-50' }}">
                            <div class="flex items-center gap-3">
                                @if($attempt->is_passed)
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="font-medium text-green-800">Réussi</span>
                                @else
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="font-medium text-red-700">Échoué</span>
                                @endif
                                <span class="text-sm text-gray-500">Tentative #{{ $attempt->attempt_number }}</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-medium {{ $attempt->is_passed ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $attempt->score }}/{{ $attempt->max_score }} ({{ $attempt->percentage }}%)
                                </span>
                                <span class="text-sm text-gray-400">
                                    {{ $attempt->completed_at?->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Quiz ou résultat final --}}
        @if($bestAttempt)
            {{-- Quiz déjà réussi --}}
            <div class="bg-green-50 rounded-2xl border border-green-100 p-8 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-2xl font-display font-semibold text-green-800 mb-2">Quiz réussi !</h2>
                <p class="text-green-700 mb-6">
                    Vous avez obtenu {{ $bestAttempt->percentage }}% ({{ $bestAttempt->score }}/{{ $bestAttempt->max_score }} points)
                </p>
                <div class="flex items-center justify-center gap-4">
                    <a href="{{ route('learning.quiz.results', [$formation->slug, $lesson, $quiz, $bestAttempt]) }}" class="px-6 py-3 bg-white text-green-700 font-medium rounded-xl hover:bg-green-50 transition-colors">
                        Voir les résultats
                    </a>
                    <a href="{{ route('learning.lesson', [$formation->slug, $lesson]) }}" class="px-6 py-3 bg-green-600 text-white font-medium rounded-xl hover:bg-green-700 transition-colors">
                        Retour à la leçon
                    </a>
                </div>
            </div>
        @elseif($canAttempt)
            {{-- Quiz à passer --}}
            <div id="quizContainer" data-quiz-id="{{ $quiz->id }}">
                <form id="quizForm" class="space-y-6">
                    @foreach($quiz->questions as $index => $question)
                        <div class="bg-white rounded-2xl border border-gray-100 p-6" data-question-id="{{ $question->id }}">
                            <div class="flex items-start gap-3 mb-4">
                                <span class="w-8 h-8 rounded-full bg-[#2D6A4F] text-white text-sm font-medium flex items-center justify-center shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h3 class="font-medium text-[#1A1A2E]">{{ $question->question }}</h3>
                                    <span class="text-sm text-gray-500">{{ $question->points }} point(s)</span>
                                </div>
                            </div>

                            <div class="ml-11 space-y-2">
                                @if($question->isText())
                                    <textarea name="answers[{{ $question->id }}]" rows="4" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]" placeholder="Votre réponse..."></textarea>
                                @elseif($question->isMultipleChoice())
                                    @foreach($question->answers as $answer)
                                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                                            <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $answer->id }}" class="w-5 h-5 text-[#2D6A4F] rounded focus:ring-[#52B788]">
                                            <span>{{ $answer->answer }}</span>
                                        </label>
                                    @endforeach
                                @else
                                    @foreach($question->answers as $answer)
                                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" class="w-5 h-5 text-[#2D6A4F] focus:ring-[#52B788]">
                                            <span>{{ $answer->answer }}</span>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if($quiz->hasTimeLimit())
                        <div class="fixed bottom-4 right-4 bg-white rounded-xl border border-gray-200 p-4 shadow-lg z-50">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#2D6A4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-mono text-lg" id="timer">{{ $quiz->time_limit_minutes }}:00</span>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-4">
                        <p class="text-sm text-gray-500">Assurez-vous d'avoir répondu à toutes les questions</p>
                        <button type="submit" class="px-8 py-4 bg-[#2D6A4F] text-white font-semibold rounded-xl hover:bg-[#40916C] transition-colors flex items-center gap-2">
                            <span>Soumettre le quiz</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        @else
            {{-- Plus de tentatives --}}
            <div class="bg-red-50 rounded-2xl border border-red-100 p-8 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-xl font-display font-semibold text-red-800 mb-2">Nombre maximum de tentatives atteint</h2>
                <p class="text-red-700 mb-6">
                    Vous avez utilisé vos {{ $quiz->max_attempts }} tentatives. Veuillez contacter le formateur pour plus d'informations.
                </p>
                <a href="{{ route('learning.lesson', [$formation->slug, $lesson]) }}" class="inline-block px-6 py-3 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors">
                    Retour à la leçon
                </a>
            </div>
        @endif

    </div>

    @push('scripts')
    <script>
        // Timer
        @if($quiz->hasTimeLimit() && $canAttempt)
            let timeLeft = {{ $quiz->time_limit_minutes }} * 60;
            const timerEl = document.getElementById('timer');

            const timer = setInterval(() => {
                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    document.getElementById('quizForm').submit();
                }
            }, 1000);
        @endif

        // Soumission du quiz
        document.getElementById('quizForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const answers = {};

            // Traiter les réponses
            for (const [key, value] of formData.entries()) {
                const match = key.match(/answers\[(\d+)\](\[\])?/);
                if (match) {
                    const questionId = match[1];
                    const isMultiple = match[2] !== undefined;

                    if (isMultiple) {
                        if (!answers[questionId]) answers[questionId] = [];
                        answers[questionId].push(parseInt(value));
                    } else {
                        answers[questionId] = parseInt(value);
                    }
                }
            }

            try {
                // Démarrer la tentative si pas encore fait
                const startResponse = await fetch('{{ route('learning.quiz.start', [$formation->slug, $lesson, $quiz]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const startData = await startResponse.json();

                if (!startData.success) {
                    throw new Error('Impossible de démarrer le quiz');
                }

                // Soumettre les réponses
                const submitResponse = await fetch('{{ route('learning.quiz.submit', [$formation->slug, $lesson, $quiz]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ answers })
                });

                const submitData = await submitResponse.json();

                if (submitData.success) {
                    window.location.href = '{{ route('learning.quiz.results', [$formation->slug, $lesson, $quiz, 'ATTEMPT_ID']) }}'.replace('ATTEMPT_ID', startData.attempt_id);
                } else {
                    alert(submitData.error || 'Une erreur est survenue');
                }
            } catch (error) {
                console.error(error);
                alert('Une erreur est survenue lors de la soumission');
            }
        });
    </script>
    @endpush
</x-app-layout>
