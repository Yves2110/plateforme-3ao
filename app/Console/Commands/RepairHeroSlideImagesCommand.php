<?php

namespace App\Console\Commands;

use App\Models\HeroSlide;
use Illuminate\Console\Command;

class RepairHeroSlideImagesCommand extends Command
{
    protected $signature = 'hero-slides:repair-images';

    protected $description = 'Remplace les URLs hero cassées (Récolte, Communauté) par des images Unsplash valides';

    /** @var array<string, string> ancien identifiant Unsplash => nouvelle URL */
    private const REPLACEMENTS = [
        '1592982537447' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1600&q=80',
        '1574943321697' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=1600&q=80',
    ];

    public function handle(): int
    {
        $updated = 0;

        foreach (self::REPLACEMENTS as $brokenId => $newUrl) {
            $slides = HeroSlide::where('image_path', 'like', '%'.$brokenId.'%')->get();

            foreach ($slides as $slide) {
                $slide->update(['image_path' => $newUrl]);
                $this->info("Slide « {$slide->title} » mis à jour.");
                $updated++;
            }
        }

        if ($updated === 0) {
            $this->comment('Aucun slide avec une ancienne URL à réparer.');
        } else {
            $this->info("{$updated} slide(s) corrigé(s).");
        }

        return self::SUCCESS;
    }
}
