<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\FormationEnrollment;
use App\Services\FormationEnrollmentService;
use App\Support\PublicContentGate;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function __construct(
        private FormationEnrollmentService $enrollmentService,
    ) {}

    public function index(Request $request)
    {
        $canManage = PublicContentGate::can(['gerer-formations', 'administrer-utilisateurs']);

        $listQuery = Formation::query()->when(! $canManage, fn ($q) => $q->validated());

        $formations = (clone $listQuery)
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->country, fn ($q) => $q->where('country', $request->country))
            ->when($request->language, fn ($q) => $q->where('language', $request->language))
            ->when($request->q, fn ($q) => $q->where('title', 'like', "%{$request->q}%")
                ->orWhere('organizer', 'like', "%{$request->q}%"))
            ->orderByRaw('start_date IS NULL, start_date ASC')
            ->paginate(12)
            ->withQueryString();

        $types     = (clone $listQuery)->distinct()->pluck('type');
        $countries = (clone $listQuery)->whereNotNull('country')->distinct()->pluck('country')->sort()->values();

        return view('formation.index', compact('formations', 'types', 'countries', 'canManage'));
    }

    public function show(string $slug)
    {
        $canManage = PublicContentGate::can(['gerer-formations', 'administrer-utilisateurs']);

        $formation = Formation::query()
            ->when(! $canManage, fn ($q) => $q->validated())
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Formation::query()
            ->when(! $canManage, fn ($q) => $q->validated())
            ->where('id', '!=', $formation->id)
            ->where(fn($q) => $q->where('type', $formation->type)
                                ->orWhere('country', $formation->country))
            ->limit(3)->get();

        $hasLmsContent = $formation->hasLmsContent();
        $lmsStats = null;
        $lmsModules = collect();

        if ($hasLmsContent) {
            $lmsModules = $formation->publishedModules()
                ->with(['publishedLessons' => fn ($q) => $q->select('id', 'module_id', 'title', 'type', 'duration_minutes', 'order')])
                ->get();

            $lmsStats = [
                'modules' => $lmsModules->count(),
                'lessons' => $lmsModules->sum(fn ($m) => $m->publishedLessons->count()),
                'duration' => $formation->formatted_duration,
            ];
        }

        $enrollment = null;
        if (auth()->check()) {
            $enrollment = FormationEnrollment::where('user_id', auth()->id())
                ->where('formation_id', $formation->id)
                ->first();

            if (request()->boolean('inscrire') && ! $enrollment) {
                $result = $this->enrollmentService->enroll(auth()->user(), $formation);

                return $this->enrollmentService->redirectAfterEnroll(
                    $formation,
                    $result['enrollment'],
                    auth()->user(),
                    $result['message'],
                    $result['created'] ? 'success' : 'info',
                );
            }
        }

        $courseEntryUrl = ($enrollment && $hasLmsContent && auth()->check())
            ? $this->enrollmentService->courseEntryUrl($formation, auth()->user())
            : null;

        return view('formation.show', compact(
            'formation',
            'related',
            'canManage',
            'hasLmsContent',
            'lmsStats',
            'lmsModules',
            'enrollment',
            'courseEntryUrl',
        ));
    }
}

