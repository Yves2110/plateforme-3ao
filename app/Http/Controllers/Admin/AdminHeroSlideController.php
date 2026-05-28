<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\HomePartner;
use App\Services\UploadService;
use Illuminate\Http\Request;

class AdminHeroSlideController extends Controller
{
    public function index()
    {
        $slides   = HeroSlide::orderBy('sort_order')->get();
        $partners = HomePartner::orderBy('sort_order')->get();

        return view('admin.hero-slides.index', compact('slides', 'partners'));
    }

    public function create()
    {
        return view('admin.hero-slides.form', [
            'slide'  => new HeroSlide(),
            'action' => 'create',
        ]);
    }

    public function store(Request $request, UploadService $uploader)
    {
        $data = $this->validated($request, true);

        $data['image_path'] = $uploader->storeImage(
            $request->file('image'),
            'hero-slides'
        );
        $data['sort_order'] = $data['sort_order'] ?? ((int) HeroSlide::max('sort_order') + 1);
        $data['is_active']  = $request->boolean('is_active', true);

        HeroSlide::create($data);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Image du slider ajoutée.');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.hero-slides.form', [
            'slide'  => $heroSlide,
            'action' => 'edit',
        ]);
    }

    public function update(Request $request, HeroSlide $heroSlide, UploadService $uploader)
    {
        $data = $this->validated($request, false);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if (! str_starts_with($heroSlide->image_path, 'http')) {
                $uploader->delete($heroSlide->image_path);
            }
            $data['image_path'] = $uploader->storeImage($request->file('image'), 'hero-slides');
        }

        $heroSlide->update($data);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Slide mise à jour.');
    }

    public function destroy(HeroSlide $heroSlide, UploadService $uploader)
    {
        if (! str_starts_with($heroSlide->image_path, 'http')) {
            $uploader->delete($heroSlide->image_path);
        }
        $heroSlide->delete();

        return back()->with('success', 'Slide supprimée.');
    }

    protected function validated(Request $request, bool $requireImage): array
    {
        return $request->validate([
            'title'      => 'nullable|string|max:255',
            'alt_text'   => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0|max:99',
            'is_active'  => 'boolean',
            'image'      => ($requireImage ? 'required|' : 'nullable|').'image|mimes:jpg,jpeg,png,webp|max:8192',
        ], [
            'image.required' => 'Téléversez une image pour le slider.',
        ]);
    }
}
