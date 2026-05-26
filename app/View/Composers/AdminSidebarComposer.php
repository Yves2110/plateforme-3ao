<?php

namespace App\View\Composers;

use App\Services\AdminGuideService;
use App\Models\Actor;
use App\Models\Actualite;
use App\Models\Event;
use App\Models\Formation;
use App\Models\ForumThread;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AdminSidebarComposer
{
    public function __construct(
        private AdminGuideService $adminGuide,
    ) {}

    public function compose(View $view): void
    {
        $user = auth()->user();

        $counts = Cache::remember('admin.sidebar.counts', now()->addMinutes(2), function () {
            return [
                'acteurs_pending'      => Actor::where('is_validated', false)->count(),
                'ressources_pending'   => Resource::where('is_validated', false)->count(),
                'evenements_pending'   => Event::where('is_validated', false)->count(),
                'formations_pending'   => Formation::where('is_validated', false)->count(),
                'forum_pending'        => ForumThread::where('is_validated', false)->count(),
                'actualites_drafts'    => Actualite::where('is_published', false)->count(),
                'users_pending'        => User::where('approval_status', 'pending')->count(),
            ];
        });

        $view->with('adminCounts', $counts);

        if ($user) {
            $view->with([
                'adminGuideSteps'        => $this->adminGuide->stepsFor($user),
                'adminGuideRoleLabel'    => $this->adminGuide->roleLabel($user),
                'showAdminGuideOnLoad'   => $this->adminGuide->showOnLoad($user),
            ]);
        }
    }
}
