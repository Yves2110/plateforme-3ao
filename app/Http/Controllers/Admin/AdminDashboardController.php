<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Actualite;
use App\Models\Resource;
use App\Models\Event;
use App\Models\Actor;
use App\Models\Media;
use App\Models\ForumThread;
use App\Models\ForumReply;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Stats globales
        $stats = [
            'users'       => User::count(),
            'actualites'  => Actualite::count(),
            'ressources'  => Resource::count(),
            'evenements'  => Event::count(),
            'acteurs'     => Actor::count(),
            'medias'      => Media::count(),
            'threads'     => ForumThread::count(),
            'replies'     => ForumReply::count(),
        ];

        // Stats de modération
        $moderationStats = [
            'pending_actors'   => Actor::where('is_validated', false)->count(),
            'pending_events'   => Event::where('is_validated', false)->count(),
            'pending_resources'=> Resource::where('is_validated', false)->count(),
            'pending_threads'  => ForumThread::where('is_validated', false)->count(),
        ];

        // Données pour graphiques (30 derniers jours)
        $chartData = $this->getChartData();

        $recentUsers = User::latest()->take(5)->get();

        $recentThreads = ForumThread::with('author')
            ->latest()->take(5)->get();

        $pendingActors = Actor::where('is_validated', false)->take(5)->get();

        // Activité récente (simulée avec les dernières créations)
        $recentActivity = $this->getRecentActivity();

        return view('admin.dashboard', compact(
            'stats', 'moderationStats', 'chartData',
            'recentUsers', 'recentThreads', 'pendingActors', 'recentActivity'
        ));
    }

    private function getChartData(): array
    {
        $days = 30;
        $labels = [];
        $usersData = [];
        $contentData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');

            // Cumul users
            $usersData[] = User::whereDate('created_at', '<=', $date)->count();

            // Contenu créé ce jour
            $contentCount = Actualite::whereDate('created_at', $date)->count()
                + Resource::whereDate('created_at', $date)->count()
                + Event::whereDate('created_at', $date)->count()
                + ForumThread::whereDate('created_at', $date)->count();
            $contentData[] = $contentCount;
        }

        return [
            'labels' => $labels,
            'users' => $usersData,
            'content' => $contentData,
        ];
    }

    private function getRecentActivity(int $perPage = 5): LengthAwarePaginator
    {
        $activities = collect();

        User::latest()->take(20)->get()->each(fn ($u) =>
            $activities->push([
                'type' => 'user',
                'icon' => 'user',
                'color' => 'blue',
                'text' => "Nouvel utilisateur : {$u->name}",
                'time' => $u->created_at,
                'url' => null,
            ])
        );

        Actualite::latest()->take(20)->get()->each(fn ($a) =>
            $activities->push([
                'type' => 'news',
                'icon' => 'document',
                'color' => 'green',
                'text' => "Actualité publiée : {$a->title}",
                'time' => $a->created_at,
                'url' => route('actualites.show', $a->slug),
            ])
        );

        Event::latest()->take(20)->get()->each(fn ($e) =>
            $activities->push([
                'type' => 'event',
                'icon' => 'calendar',
                'color' => 'orange',
                'text' => "Événement créé : {$e->title}",
                'time' => $e->created_at,
                'url' => route('evenements.show', $e->slug),
            ])
        );

        $sorted = $activities->sortByDesc('time')->values();
        $page = max(1, (int) request()->query('activity_page', 1));

        return new LengthAwarePaginator(
            $sorted->forPage($page, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            [
                'path' => route('admin.dashboard'),
                'pageName' => 'activity_page',
            ]
        );
    }
}
