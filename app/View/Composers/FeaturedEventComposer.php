<?php

namespace App\View\Composers;

use App\Services\HomePageService;
use Illuminate\View\View;

class FeaturedEventComposer
{
    public function __construct(
        protected HomePageService $homePage
    ) {}

    public function compose(View $view): void
    {
        $view->with('featuredEvent', $this->homePage->featuredUpcomingEvent());
    }
}
