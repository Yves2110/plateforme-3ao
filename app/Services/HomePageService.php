<?php

namespace App\Services;

use App\Models\Actor;
use App\Models\Event;
use App\Models\HeroSlide;
use App\Models\HomePartner;
use App\Models\Resource;
class HomePageService
{
    public function heroSlides()
    {
        $slides = HeroSlide::active()->get();

        if ($slides->isNotEmpty()) {
            return $slides;
        }

        return collect($this->defaultHeroSlides());
    }

    public function homePartners()
    {
        $partners = HomePartner::active()->get();

        if ($partners->isNotEmpty()) {
            return $partners;
        }

        return collect($this->defaultHomePartners());
    }

    public function platformStats(): array
    {
        $actorsQuery = Actor::where('is_validated', true);

        $countries = (clone $actorsQuery)
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->count('country');

        return [
            'pays'       => $countries,
            'orgs'       => (clone $actorsQuery)->count(),
            'ressources' => Resource::where('is_validated', true)->count(),
            'events'     => Event::where('is_validated', true)
                ->whereYear('start_date', now()->year)
                ->count(),
        ];
    }

    public function statsLinks(): array
    {
        return [
            'pays'       => route('carte.index'),
            'orgs'       => route('carte.index'),
            'ressources' => route('bibliotheque.index'),
            'events'     => route('evenements.index'),
        ];
    }

    public function featuredUpcomingEvent(): ?Event
    {
        $soon = $this->eventsStartingWithinDays(7)->first();

        if ($soon) {
            return $soon;
        }

        return Event::where('is_validated', true)
            ->where('start_date', '>=', now()->startOfDay())
            ->orderBy('start_date')
            ->first();
    }

    /** Événements dont le début est dans les N prochains jours (rappel hero). */
    public function eventsStartingWithinDays(int $days = 7)
    {
        $today = now()->startOfDay();
        $limit = $today->copy()->addDays($days)->endOfDay();

        return Event::where('is_validated', true)
            ->whereBetween('start_date', [$today, $limit])
            ->orderBy('start_date');
    }

    protected function defaultHeroSlides(): array
    {
        $defaults = [
            [
                'title'      => 'Champs agroécologiques',
                'image_path' => 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?auto=format&fit=crop&w=1600&q=80',
                'alt_text'   => 'Agriculture durable en Afrique de l\'Ouest',
                'sort_order' => 0,
                'is_active'  => true,
            ],
            [
                'title'      => 'Récolte',
                'image_path' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1600&q=80',
                'alt_text'   => 'Récolte de légumes',
                'sort_order' => 1,
                'is_active'  => true,
            ],
            [
                'title'      => 'Maraîchage',
                'image_path' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1600&q=80',
                'alt_text'   => 'Maraîchage agroécologique',
                'sort_order' => 2,
                'is_active'  => true,
            ],
            [
                'title'      => 'Communauté rurale',
                'image_path' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=1600&q=80',
                'alt_text'   => 'Acteurs de l\'agroécologie',
                'sort_order' => 3,
                'is_active'  => true,
            ],
        ];

        return array_map(fn ($row) => new HeroSlide($row), $defaults);
    }

    protected function defaultHomePartners(): array
    {
        $names = ['ROPPA', 'CIRAD', 'FAO', 'GIZ', 'ARAA', 'CEDEAO', 'ENDA-PRONAT'];

        return array_map(
            fn ($name, $order) => new HomePartner([
                'name'       => $name,
                'sort_order' => $order,
                'is_active'  => true,
            ]),
            $names,
            array_keys($names)
        );
    }
}
