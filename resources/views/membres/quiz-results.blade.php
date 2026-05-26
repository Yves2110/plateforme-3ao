<x-app-layout>
    <x-slot name="title">Résultats — {{ $quiz->title }}</x-slot>
    <x-slot name="description">Résultats du quiz de validation</x-slot>

    {{-- Header --}}
    <div class="bg-[#1A1A2E] text-white py-4 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="{{ route('learning.quiz', [$formation->slug, $lesson]) }}" class="text-white/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div class="flex-1">
                    <p class="text-white/50 text-sm">{{ $formation->title }} — {{ $lesson->title }}</p>
                    <h1 class="font-display font-semibold">Résultats: {{ $quiz->title }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- Score global --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-6 text-center">
            @if($attempt->is_passed)
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-3xl font-display font-bold text-green-700 mb-2">Quiz réussi !</h2>
                <p class="text-green-600 mb-4">Félicitations, vous avez validé ce quiz.</p>
            @else
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-3xl font-display font-bold text-red-700 mb-2">Quiz non validé</h2>
                <p class="text-red-600 mb-4">Vous n'avez pas atteint le score minimum requis.</p>
            @endif

            <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-2xl font-display font-bold {{ $attempt->is_passed ? 'text-green-600' : 'text-red-600' }}">{{ $attempt->percentage }}%</p>
                    <p class="text-sm text-gray-500">Score</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-2xl font-display font-bold text-[#1A1A2E]">{{ $attempt->score }}</p>
                    <p class="text-sm text-gray-500">Points obtenus</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-2xl font-display font-bold text-[#1A1A2E]">{{ $attempt->max_score }}</p>
                    <p class="text-sm text-gray-500">Points total</p>
                </div>
            </div>

            <div class="mt-6 text-sm text-gray-500">
                <p>Tentative #{{ $attempt->attempt_number }} — Complétée le {{ $attempt->completed_at?->format('d/m/Y à H:i') }}</p>
            </div>
        </div>

        {{-- Détail des questions --}}
        @if($quiz->show_correct_answers)
            <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
                <h2 class="text-xl font-display font-semibold text-[#1A1A2E] mb-6">Détail des réponses</h2>

                <div class="space-y-6">
                    @foreach($quiz->questions as $index => $question)
                        @php
                            $userAnswers = $attempt->answers[$question->id] ?? [];
                            $userAnswers = is_array($userAnswers) ? $userAnswers : [$userAnswers];
                            $correctAnswers = $question->correctAnswers->pluck('id')->toArray();

                            // Déterminer si la réponse est correcte
                            $isCorrect = false;
                            if ($question->isText()) {
                                $isCorrect = !empty($userAnswers[0]);
                            } elseif ($question->isMultipleChoice()) {
                                sort($userAnswers);
                                sort($correctAnswers);
                                $isCorrect = $userAnswers === $correctAnswers;
                            } else {
                                $isCorrect = in_array((int) ($userAnswers[0] ?? 0), $correctAnswers);
                            }
                        @endphp

                        <div class="border-b border-gray-100 last:border-0 pb-6 last:pb-0">
                            <div class="flex items-start gap-3 mb-3">
                                <span class="w-8 h-8 rounded-full {{ $isCorrect ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-sm font-medium flex items-center justify-center shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-1">
                                    <h3 class="font-medium text-[#1A1A2E]">{{ $question->question }}</h3>
                                    <span class="text-sm text-gray-500">{{ $question->points }} point(s)</span>
                                </div>
                            </div>

                            <div class="ml-11 space-y-2">
                                @if($question->isText())
                                    <div class="p-3 rounded-xl bg-gray-50">
                                        <p class="text-sm text-gray-600 mb-1">Votre réponse:</p>
                                        <p class="text-gray-800">{{ $userAnswers[0] ?? '(pas de réponse)' }}</p>
                                    </div>
                                @else
                                    @foreach($question->answers as $answer)
                                        @php
                                            $isUserAnswer = in_array($answer->id, $userAnswers);
                                            $isCorrectAnswer = $answer->is_correct;
                                        @endphp
                                        <div class="flex items-center gap-3 p-3 rounded-xl
                                            {{ $isUserAnswer && $isCorrectAnswer ? 'bg-green-100 border border-green-200' : '' }}
                                            {{ $isUserAnswer && !$isCorrectAnswer ? 'bg-red-100 border border-red-200' : '' }}
                                            {{ !$isUserAnswer && $isCorrectAnswer ? 'bg-green-50 border border-green-100' : '' }}
                                            {{ !$isUserAnswer && !$isCorrectAnswer ? 'bg-gray-50' : '' }}">
                                            @if($isUserAnswer && $isCorrectAnswer)
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            @elseif($isUserAnswer && !$isCorrectAnswer)
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            @elseif(!$isUserAnswer && $isCorrectAnswer)
                                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <span class="w-5 h-5 rounded-full border-2 border-gray-300"></span>
                                            @endif
                                            <span class="{{ $isCorrectAnswer ? 'font-medium text-green-800' : ($isUserAnswer ? 'text-red-700' : 'text-gray-600') }}">
                                                {{ $answer->answer }}
                                            </span>
                                            @if($isCorrectAnswer)
                                                <span class="ml-auto text-xs px-2 py-1 bg-green-200 text-green-800 rounded-full">Bonne réponse</span>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif

                                @if($question->explanation)
                                    <div class="mt-3 p-3 bg-blue-50 rounded-xl">
                                        <p class="text-sm text-blue-800">
                                            <span class="font-medium">Explication:</span> {{ $question->explanation }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex items-center justify-center gap-4">
            <a href="{{ route('learning.quiz', [$formation->slug, $lesson]) }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                Retour au quiz
            </a>
            <a href="{{ route('learning.lesson', [$formation->slug, $lesson]) }}" class="px-6 py-3 bg-[#2D6A4F] text-white font-medium rounded-xl hover:bg-[#40916C] transition-colors">
                Retour à la leçon
            </a>
        </div>

    </div>
</x-app-layout>
