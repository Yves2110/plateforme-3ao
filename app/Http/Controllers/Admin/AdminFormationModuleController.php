<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\FormationModule;
use Illuminate\Http\Request;

class AdminFormationModuleController extends Controller
{
    public function index(Formation $formation)
    {
        $modules = $formation->modules()
            ->withCount('lessons')
            ->orderBy('order')
            ->get();

        return view('admin.formations.modules.index', compact('formation', 'modules'));
    }

    public function create(Formation $formation)
    {
        $nextOrder = $formation->modules()->count() + 1;
        return view('admin.formations.modules.form', compact('formation', 'nextOrder'));
    }

    public function store(Request $request, Formation $formation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['formation_id'] = $formation->id;
        $validated['is_published'] = $request->boolean('is_published');

        FormationModule::create($validated);

        return redirect()->route('admin.formations.modules.index', $formation)
            ->with('success', 'Module créé avec succès.');
    }

    public function edit(Formation $formation, FormationModule $module)
    {
        return view('admin.formations.modules.form', compact('formation', 'module'));
    }

    public function update(Request $request, Formation $formation, FormationModule $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        $module->update($validated);

        return redirect()->route('admin.formations.modules.index', $formation)
            ->with('success', 'Module mis à jour avec succès.');
    }

    public function destroy(Formation $formation, FormationModule $module)
    {
        $module->delete();

        return redirect()->route('admin.formations.modules.index', $formation)
            ->with('success', 'Module supprimé avec succès.');
    }

    public function togglePublish(Formation $formation, FormationModule $module)
    {
        $module->update(['is_published' => !$module->is_published]);

        $status = $module->is_published ? 'publié' : 'dépublié';
        return redirect()->back()->with('success', "Module {$status} avec succès.");
    }

    public function reorder(Request $request, Formation $formation)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*' => 'integer|exists:formation_modules,id',
        ]);

        foreach ($request->modules as $index => $moduleId) {
            FormationModule::where('id', $moduleId)
                ->where('formation_id', $formation->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
