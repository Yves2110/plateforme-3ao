<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\FormationLesson;
use App\Models\FormationQuiz;
use App\Models\FormationQuestion;
use App\Models\FormationAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFormationQuizController extends Controller
{
    public function index(Formation $formation)
    {
        $quizzes = FormationQuiz::whereHas('lesson.module', function ($q) use ($formation) {
            $q->where('formation_id', $formation->id);
        })
        ->with(['lesson', 'questions'])
        ->latest()
        ->get();

        return view('admin.formations.quizzes.index', compact('formation', 'quizzes'));
    }

    public function create(Formation $formation)
    {
        $modules = $formation->modules()
            ->with(['lessons' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return view('admin.formations.quizzes.form', compact('formation', 'modules'));
    }

    public function store(Request $request, Formation $formation)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:formation_lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'is_published' => 'boolean',
            'show_correct_answers' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:single_choice,multiple_choice,true_false,text',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*.answer' => 'required|string',
            'questions.*.answers.*.is_correct' => 'boolean',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Créer le quiz
            $quiz = FormationQuiz::create([
                'lesson_id' => $validated['lesson_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'passing_score' => $validated['passing_score'],
                'time_limit_minutes' => $validated['time_limit_minutes'],
                'max_attempts' => $validated['max_attempts'],
                'is_published' => $request->boolean('is_published'),
                'show_correct_answers' => $request->boolean('show_correct_answers'),
            ]);

            // Créer les questions et réponses
            foreach ($validated['questions'] as $index => $questionData) {
                $question = FormationQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question' => $questionData['question'],
                    'type' => $questionData['type'],
                    'points' => $questionData['points'],
                    'explanation' => $questionData['explanation'] ?? null,
                    'order' => $index + 1,
                ]);

                foreach ($questionData['answers'] as $answerIndex => $answerData) {
                    FormationAnswer::create([
                        'question_id' => $question->id,
                        'answer' => $answerData['answer'],
                        'is_correct' => $answerData['is_correct'] ?? false,
                        'order' => $answerIndex + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.formations.quizzes.index', $formation)
            ->with('success', 'Quiz créé avec succès.');
    }

    public function edit(Formation $formation, FormationQuiz $quiz)
    {
        $modules = $formation->modules()
            ->with(['lessons' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        $quiz->load('questions.answers');

        return view('admin.formations.quizzes.form', compact('formation', 'quiz', 'modules'));
    }

    public function update(Request $request, Formation $formation, FormationQuiz $quiz)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:formation_lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'is_published' => 'boolean',
            'show_correct_answers' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:single_choice,multiple_choice,true_false,text',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*.answer' => 'required|string',
            'questions.*.answers.*.is_correct' => 'boolean',
        ]);

        DB::transaction(function () use ($validated, $request, $quiz) {
            // Mettre à jour le quiz
            $quiz->update([
                'lesson_id' => $validated['lesson_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'passing_score' => $validated['passing_score'],
                'time_limit_minutes' => $validated['time_limit_minutes'],
                'max_attempts' => $validated['max_attempts'],
                'is_published' => $request->boolean('is_published'),
                'show_correct_answers' => $request->boolean('show_correct_answers'),
            ]);

            // Supprimer les anciennes questions et réponses
            $quiz->questions()->delete();

            // Recréer les questions et réponses
            foreach ($validated['questions'] as $index => $questionData) {
                $question = FormationQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question' => $questionData['question'],
                    'type' => $questionData['type'],
                    'points' => $questionData['points'],
                    'explanation' => $questionData['explanation'] ?? null,
                    'order' => $index + 1,
                ]);

                foreach ($questionData['answers'] as $answerIndex => $answerData) {
                    FormationAnswer::create([
                        'question_id' => $question->id,
                        'answer' => $answerData['answer'],
                        'is_correct' => $answerData['is_correct'] ?? false,
                        'order' => $answerIndex + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.formations.quizzes.index', $formation)
            ->with('success', 'Quiz mis à jour avec succès.');
    }

    public function destroy(Formation $formation, FormationQuiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.formations.quizzes.index', $formation)
            ->with('success', 'Quiz supprimé avec succès.');
    }

    public function togglePublish(Formation $formation, FormationQuiz $quiz)
    {
        $quiz->update(['is_published' => !$quiz->is_published]);

        $status = $quiz->is_published ? 'publié' : 'dépublié';
        return redirect()->back()->with('success', "Quiz {$status} avec succès.");
    }

    public function getLessons(Request $request, Formation $formation)
    {
        $moduleId = $request->input('module_id');

        $lessons = FormationLesson::where('module_id', $moduleId)
            ->orderBy('order')
            ->get(['id', 'title']);

        return response()->json($lessons);
    }
}
