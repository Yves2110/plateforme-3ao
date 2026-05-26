<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\FormationLesson;
use App\Models\FormationModule;
use App\Services\UploadService;
use Illuminate\Http\Request;

class AdminFormationLessonController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index(Formation $formation)
    {
        $modules = $formation->modules()
            ->with(['lessons' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return view('admin.formations.lessons.index', compact('formation', 'modules'));
    }

    public function create(Formation $formation)
    {
        $modules = $formation->modules()->orderBy('order')->get();
        $nextOrder = 0;

        if ($modules->isNotEmpty()) {
            $lastModule = $modules->first();
            $nextOrder = $lastModule->lessons()->count() + 1;
        }

        return view('admin.formations.lessons.form', compact('formation', 'modules', 'nextOrder'));
    }

    public function store(Request $request, Formation $formation)
    {
        $validated = $this->validateLesson($request);

        // Gestion du fichier uploadé
        if ($request->hasFile('file')) {
            $type = $request->input('type');
            $folder = match ($type) {
                'video' => 'lessons/videos',
                'pdf' => 'lessons/pdfs',
                'audio' => 'lessons/audios',
                default => 'lessons/files',
            };

            $validated['file_path'] = $this->uploadService->storeFile(
                $request->file('file'),
                $folder,
                $type
            );
        }

        $validated['is_published'] = $request->boolean('is_published');

        FormationLesson::create($validated);

        return redirect()->route('admin.formations.lessons.index', $formation)
            ->with('success', 'Leçon créée avec succès.');
    }

    public function edit(Formation $formation, FormationLesson $lesson)
    {
        $modules = $formation->modules()->orderBy('order')->get();
        return view('admin.formations.lessons.form', compact('formation', 'lesson', 'modules'));
    }

    public function update(Request $request, Formation $formation, FormationLesson $lesson)
    {
        $validated = $this->validateLesson($request);

        // Gestion du fichier uploadé
        if ($request->hasFile('file')) {
            // Supprimer l'ancien fichier
            if ($lesson->file_path) {
                $this->uploadService->delete($lesson->file_path);
            }

            $type = $request->input('type');
            $folder = match ($type) {
                'video' => 'lessons/videos',
                'pdf' => 'lessons/pdfs',
                'audio' => 'lessons/audios',
                default => 'lessons/files',
            };

            $validated['file_path'] = $this->uploadService->storeFile(
                $request->file('file'),
                $folder,
                $type
            );
        }

        $validated['is_published'] = $request->boolean('is_published');

        $lesson->update($validated);

        return redirect()->route('admin.formations.lessons.index', $formation)
            ->with('success', 'Leçon mise à jour avec succès.');
    }

    public function destroy(Formation $formation, FormationLesson $lesson)
    {
        if ($lesson->file_path) {
            $this->uploadService->delete($lesson->file_path);
        }

        $lesson->delete();

        return redirect()->route('admin.formations.lessons.index', $formation)
            ->with('success', 'Leçon supprimée avec succès.');
    }

    public function togglePublish(Formation $formation, FormationLesson $lesson)
    {
        $lesson->update(['is_published' => !$lesson->is_published]);

        $status = $lesson->is_published ? 'publiée' : 'dépubliée';
        return redirect()->back()->with('success', "Leçon {$status} avec succès.");
    }

    public function reorder(Request $request, Formation $formation)
    {
        $request->validate([
            'lessons' => 'required|array',
            'lessons.*' => 'integer|exists:formation_lessons,id',
        ]);

        foreach ($request->lessons as $index => $lessonId) {
            FormationLesson::where('id', $lessonId)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    private function validateLesson(Request $request): array
    {
        return $request->validate([
            'module_id' => 'required|exists:formation_modules,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,pdf,text,quiz,audio',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'duration_minutes' => 'nullable|integer|min:1',
            'order' => 'required|integer|min:0',
            'file' => 'nullable|file|max:50000', // 50MB max
        ]);
    }
}
