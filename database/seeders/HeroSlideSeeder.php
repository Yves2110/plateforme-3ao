<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        if (HeroSlide::exists()) {
            return;
        }

        $slides = [
            [
                'title'      => 'Agroécologie — slide principal',
                'image_path' => 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?auto=format&fit=crop&w=1600&q=80',
                'alt_text'   => 'Agriculture durable en Afrique de l\'Ouest',
                'sort_order' => 0,
            ],
            [
                'title'      => 'Récolte',
                'image_path' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1600&q=80',
                'alt_text'   => 'Récolte de légumes',
                'sort_order' => 1,
            ],
            [
                'title'      => 'Maraîchage',
                'image_path' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1600&q=80',
                'alt_text'   => 'Maraîchage agroécologique',
                'sort_order' => 2,
            ],
            [
                'title'      => 'Communauté',
                'image_path' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=1600&q=80',
                'alt_text'   => 'Acteurs de l\'agroécologie',
                'sort_order' => 3,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::create(array_merge($slide, ['is_active' => true]));
        }
    }
}
