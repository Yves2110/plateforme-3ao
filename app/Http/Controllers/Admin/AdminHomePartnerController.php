<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePartner;
use App\Services\UploadService;
use Illuminate\Http\Request;

class AdminHomePartnerController extends Controller
{
    public function create()
    {
        return view('admin.home-partners.form', [
            'partner' => new HomePartner(),
            'action'  => 'create',
        ]);
    }

    public function store(Request $request, UploadService $uploader)
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $uploader->storeImage($request->file('logo'), 'home-partners', 800);
        }

        $data['sort_order'] = $data['sort_order'] ?? ((int) HomePartner::max('sort_order') + 1);
        $data['is_active']  = $request->boolean('is_active', true);

        HomePartner::create($data);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Partenaire ajouté.');
    }

    public function edit(HomePartner $homePartner)
    {
        return view('admin.home-partners.form', [
            'partner' => $homePartner,
            'action'  => 'edit',
        ]);
    }

    public function update(Request $request, HomePartner $homePartner, UploadService $uploader)
    {
        $data = $this->validated($request, false);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            if ($homePartner->logo_path && ! str_starts_with($homePartner->logo_path, 'http')) {
                $uploader->delete($homePartner->logo_path);
            }
            $data['logo_path'] = $uploader->storeImage($request->file('logo'), 'home-partners', 800);
        }

        $homePartner->update($data);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Partenaire mis à jour.');
    }

    public function destroy(HomePartner $homePartner, UploadService $uploader)
    {
        if ($homePartner->logo_path && ! str_starts_with($homePartner->logo_path, 'http')) {
            $uploader->delete($homePartner->logo_path);
        }
        $homePartner->delete();

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Partenaire supprimé.');
    }

    protected function validated(Request $request, bool $requireLogo): array
    {
        return $request->validate([
            'name'         => 'required|string|max:255',
            'website_url'  => 'nullable|url|max:500',
            'sort_order'   => 'nullable|integer|min:0|max:99',
            'is_active'    => 'boolean',
            'logo'         => ($requireLogo ? 'required|' : 'nullable|').'image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'name.required'  => 'Indiquez le nom du partenaire.',
            'logo.required'  => 'Téléversez le logo du partenaire.',
            'website_url.url' => 'L’URL du site doit être valide (https://…).',
        ]);
    }
}
