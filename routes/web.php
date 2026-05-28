<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BibliothequeController;
use App\Http\Controllers\CommunauteController;
use App\Http\Controllers\MultimediaController;
use App\Http\Controllers\CarteController;
use App\Http\Controllers\EvenementsController;
use App\Http\Controllers\ActualitesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminActualiteController;
use App\Http\Controllers\Admin\AdminResourceController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminMediaController;
use App\Http\Controllers\Admin\AdminForumController;
use App\Http\Controllers\Admin\AdminActorController;
use App\Http\Controllers\Admin\AdminFormationController;
use App\Http\Controllers\Admin\AdminFormationModuleController;
use App\Http\Controllers\Admin\AdminFormationLessonController;
use App\Http\Controllers\Admin\AdminFormationQuizController;
use App\Http\Controllers\Admin\AdminRssController;
use App\Http\Controllers\Admin\AdminNewsletterController;
use App\Http\Controllers\Admin\AdminGuideController;
use App\Http\Controllers\Admin\AdminHeroSlideController;
use App\Http\Controllers\Admin\AdminHomePartnerController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\RssController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\MyLearningController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PublicManageController;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', function () {
    return response(
        "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login\nSitemap: " . route('sitemap'),
        200,
        ['Content-Type' => 'text/plain']
    );
});
Route::get('/recherche', [SearchController::class, 'index'])->name('search')->middleware('throttle:search');
Route::get('/recherche/suggest', [SearchController::class, 'suggest'])->name('search.suggest')->middleware('throttle:search');
Route::get('/mentions-legales', fn() => view('legal'))->name('mentions-legales');

Route::post('/newsletter/inscription', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe')
    ->middleware(['throttle:10,1', 'spam.protect']);
Route::get('/newsletter/desinscription/{token}', [NewsletterController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe');

// Profils membres
Route::get('/membres/{user}', [UserProfileController::class, 'show'])->name('membre.show');
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'approved', 'verified'])->group(function () {
    Route::get('/mon-espace', [UserProfileController::class, 'dashboard'])->name('membre.dashboard');
    Route::delete('/mon-compte', [UserProfileController::class, 'deleteAccount'])->name('membre.delete');

    // Espace apprentissage (LMS)
    Route::prefix('mon-apprentissage')->name('learning.')->group(function () {
        Route::get('/', [MyLearningController::class, 'dashboard'])->name('dashboard');
        Route::get('/formations/{formation:slug}', [MyLearningController::class, 'show'])->name('show');
        Route::get('/formations/{formation:slug}/lecons/{lesson}', [MyLearningController::class, 'lesson'])->name('lesson');
        Route::post('/formations/{formation}/inscrire', [MyLearningController::class, 'enroll'])->name('enroll');
        Route::post('/formations/{formation}/lecons/{lesson}/completer', [MyLearningController::class, 'completeLesson'])->name('complete');
        Route::post('/lecons/{lesson}/temps', [MyLearningController::class, 'trackTime'])->name('track-time');

        // Quiz
        Route::get('/formations/{formation:slug}/lecons/{lesson}/quiz', [MyLearningController::class, 'quiz'])->name('quiz');
        Route::post('/formations/{formation:slug}/lecons/{lesson}/quiz/{quiz}/start', [MyLearningController::class, 'startQuiz'])->name('quiz.start');
        Route::post('/formations/{formation:slug}/lecons/{lesson}/quiz/{quiz}/submit', [MyLearningController::class, 'submitQuiz'])->name('quiz.submit');
        Route::get('/formations/{formation:slug}/lecons/{lesson}/quiz/{quiz}/results/{attempt}', [MyLearningController::class, 'quizResults'])->name('quiz.results');
    });
});

// Flux RSS
Route::prefix('flux')->name('rss.')->group(function () {
    Route::get('/actualites.xml',  [RssController::class, 'actualites'])->name('actualites');
    Route::get('/ressources.xml',  [RssController::class, 'ressources'])->name('ressources');
    Route::get('/evenements.xml',  [RssController::class, 'evenements'])->name('evenements');
    Route::get('/forum.xml',       [RssController::class, 'forum'])->name('forum');
});

// Actualités
Route::prefix('actualites')->name('actualites.')->group(function () {
    Route::get('/', [ActualitesController::class, 'index'])->name('index');
    Route::get('/{slug}', [ActualitesController::class, 'show'])->name('show');
});

// Bibliothèque
Route::prefix('bibliotheque')->name('bibliotheque.')->group(function () {
    Route::get('/', [BibliothequeController::class, 'index'])->name('index');
    Route::get('/{slug}', [BibliothequeController::class, 'show'])->name('show');
});

// Espace communautaire (forum)
Route::prefix('communaute')->name('communaute.')->group(function () {
    Route::get('/', [ForumController::class, 'index'])->name('index');
    Route::get('/creer', [ForumController::class, 'create'])->name('create')->middleware('auth');
    Route::post('/creer', [ForumController::class, 'store'])->name('store')->middleware('auth');
    Route::get('/{category}', [ForumController::class, 'category'])->name('category');
    Route::get('/{category}/{thread}', [ForumController::class, 'thread'])->name('thread');
    Route::post('/{category}/{thread}/repondre', [ForumController::class, 'reply'])->name('reply')->middleware('throttle:forum-post');
    Route::post('/{category}/{thread}/voter', [ForumController::class, 'vote'])->name('vote')->middleware('throttle:forum-post');
    Route::post('/moderer/{action}/{id}', [ForumController::class, 'moderate'])->name('moderate');
});

// Multimédia
Route::prefix('multimedia')->name('multimedia.')->group(function () {
    Route::get('/', [MultimediaController::class, 'index'])->name('index');
    Route::get('/{slug}', [MultimediaController::class, 'show'])->name('show');
});

// Carte des acteurs
Route::prefix('carte')->name('carte.')->group(function () {
    Route::get('/', [CarteController::class, 'index'])->name('index');
    Route::get('/reseau', [CarteController::class, 'network'])->name('network');
    Route::get('/acteurs', [CarteController::class, 'acteurs'])->name('acteurs');
    Route::get('/acteur/{slugOrId}', [CarteController::class, 'acteur'])->name('acteur');
});

// Hub Formation
Route::prefix('formation')->name('formation.')->group(function () {
    Route::get('/', [FormationController::class, 'index'])->name('index');
    Route::get('/{slug}', [FormationController::class, 'show'])->name('show');
});

// Événements
Route::prefix('evenements')->name('evenements.')->group(function () {
    Route::get('/', [EvenementsController::class, 'index'])->name('index');
    Route::get('/{slug}', [EvenementsController::class, 'show'])->name('show');
    Route::post('/{slug}/inscription', [EvenementsController::class, 'inscription'])->name('inscription')->middleware('throttle:event-register');
});

/*
|--------------------------------------------------------------------------
| Routes authentifiées
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {
    Route::view('/inscription-en-attente', 'auth.registration-pending')->name('registration.pending');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'approved',
    'verified',
])->group(function () {

    // Publication / dépublication depuis l'espace public (sans passer par /admin)
    Route::prefix('contenu')->name('contenu.')->group(function () {
        Route::post('/ressources/{ressource}/publication', [PublicManageController::class, 'toggleResource'])
            ->name('ressources.toggle');
        Route::post('/actualites/{actualite}/publication', [PublicManageController::class, 'toggleActualite'])
            ->name('actualites.toggle');
        Route::post('/medias/{media}/publication', [PublicManageController::class, 'toggleMedia'])
            ->name('medias.toggle');
        Route::post('/evenements/{event}/publication', [PublicManageController::class, 'toggleEvent'])
            ->name('evenements.toggle');
        Route::post('/formations/{formation}/publication', [PublicManageController::class, 'toggleFormation'])
            ->name('formations.toggle');
        Route::post('/acteurs/{actor}/publication', [PublicManageController::class, 'toggleActor'])
            ->name('acteurs.toggle');
        Route::post('/forum/threads/{thread}/publication', [PublicManageController::class, 'toggleForumThread'])
            ->name('forum.threads.toggle');
    });

    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user && ($user->hasRole('super_admin') || $user->hasRole('moderateur'))) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('membre.dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| API JSON pour widgets embarquables
|--------------------------------------------------------------------------
*/

Route::prefix('api/widget')->name('widget.')->group(function () {
    Route::get('/news', [HomeController::class, 'widgetNews'])->name('news');
    Route::get('/events', [HomeController::class, 'widgetEvents'])->name('events');
});

/*
|--------------------------------------------------------------------------
| Back-office Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth:sanctum', config('jetstream.auth_session'), 'approved', 'verified'])->group(function () {

    // Validation des inscriptions (validateurs dédiés ou admins)
    Route::middleware('can.validate.registrations')->group(function () {
        Route::get('/utilisateurs-en-attente', [AdminUserController::class, 'pending'])
            ->name('users.pending');
        Route::post('/utilisateurs/{user}/approuver', [AdminUserController::class, 'approve'])
            ->name('users.approve');
        Route::post('/utilisateurs/{user}/refuser', [AdminUserController::class, 'reject'])
            ->name('users.reject');
    });

    Route::middleware(['admin', 'admin.2fa'])->group(function () {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::post('/guide/complete', [AdminGuideController::class, 'complete'])->name('guide.complete');

    Route::resource('hero-slides', AdminHeroSlideController::class)->except(['show']);
    Route::resource('home-partners', AdminHomePartnerController::class)->except(['show', 'index']);

    // Utilisateurs
    Route::resource('utilisateurs', AdminUserController::class)->except(['show'])
        ->parameters(['utilisateurs' => 'user']);

    // Actualités
    Route::resource('actualites', AdminActualiteController::class)->except(['show']);

    // Ressources bibliothèque
    Route::post('/ressources/{ressource}/toggle-validation', [AdminResourceController::class, 'toggleValidation'])
        ->name('ressources.toggle-validation');
    Route::resource('ressources', AdminResourceController::class)->except(['show']);

    // Événements
    Route::resource('evenements', AdminEventController::class)->except(['show']);

    // Médias
    Route::resource('medias', AdminMediaController::class)->except(['show']);

    // Acteurs
    Route::resource('acteurs', AdminActorController::class)->except(['show']);

    // Formations
    Route::resource('formations', AdminFormationController::class)->except(['show']);
    Route::post('/formations/{formation}/toggle-validation', [AdminFormationController::class, 'toggleValidation'])
        ->name('formations.toggle-validation');

    // Modules des formations
    Route::get('/formations/{formation}/modules', [AdminFormationModuleController::class, 'index'])
        ->name('formations.modules.index');
    Route::get('/formations/{formation}/modules/create', [AdminFormationModuleController::class, 'create'])
        ->name('formations.modules.create');
    Route::post('/formations/{formation}/modules', [AdminFormationModuleController::class, 'store'])
        ->name('formations.modules.store');
    Route::get('/formations/{formation}/modules/{module}/edit', [AdminFormationModuleController::class, 'edit'])
        ->name('formations.modules.edit');
    Route::put('/formations/{formation}/modules/{module}', [AdminFormationModuleController::class, 'update'])
        ->name('formations.modules.update');
    Route::delete('/formations/{formation}/modules/{module}', [AdminFormationModuleController::class, 'destroy'])
        ->name('formations.modules.destroy');
    Route::post('/formations/{formation}/modules/{module}/toggle-publish', [AdminFormationModuleController::class, 'togglePublish'])
        ->name('formations.modules.toggle-publish');
    Route::post('/formations/{formation}/modules/reorder', [AdminFormationModuleController::class, 'reorder'])
        ->name('formations.modules.reorder');

    // Leçons des formations
    Route::get('/formations/{formation}/lessons', [AdminFormationLessonController::class, 'index'])
        ->name('formations.lessons.index');
    Route::get('/formations/{formation}/lessons/create', [AdminFormationLessonController::class, 'create'])
        ->name('formations.lessons.create');
    Route::post('/formations/{formation}/lessons', [AdminFormationLessonController::class, 'store'])
        ->name('formations.lessons.store');
    Route::get('/formations/{formation}/lessons/{lesson}/edit', [AdminFormationLessonController::class, 'edit'])
        ->name('formations.lessons.edit');
    Route::put('/formations/{formation}/lessons/{lesson}', [AdminFormationLessonController::class, 'update'])
        ->name('formations.lessons.update');
    Route::delete('/formations/{formation}/lessons/{lesson}', [AdminFormationLessonController::class, 'destroy'])
        ->name('formations.lessons.destroy');
    Route::post('/formations/{formation}/lessons/{lesson}/toggle-publish', [AdminFormationLessonController::class, 'togglePublish'])
        ->name('formations.lessons.toggle-publish');
    Route::post('/formations/{formation}/lessons/reorder', [AdminFormationLessonController::class, 'reorder'])
        ->name('formations.lessons.reorder');

    // Quiz des formations
    Route::get('/formations/{formation}/quizzes', [AdminFormationQuizController::class, 'index'])
        ->name('formations.quizzes.index');
    Route::get('/formations/{formation}/quizzes/create', [AdminFormationQuizController::class, 'create'])
        ->name('formations.quizzes.create');
    Route::post('/formations/{formation}/quizzes', [AdminFormationQuizController::class, 'store'])
        ->name('formations.quizzes.store');
    Route::get('/formations/{formation}/quizzes/{quiz}/edit', [AdminFormationQuizController::class, 'edit'])
        ->name('formations.quizzes.edit');
    Route::put('/formations/{formation}/quizzes/{quiz}', [AdminFormationQuizController::class, 'update'])
        ->name('formations.quizzes.update');
    Route::delete('/formations/{formation}/quizzes/{quiz}', [AdminFormationQuizController::class, 'destroy'])
        ->name('formations.quizzes.destroy');
    Route::post('/formations/{formation}/quizzes/{quiz}/toggle-publish', [AdminFormationQuizController::class, 'togglePublish'])
        ->name('formations.quizzes.toggle-publish');
    Route::get('/formations/{formation}/quizzes/get-lessons', [AdminFormationQuizController::class, 'getLessons'])
        ->name('formations.quizzes.get-lessons');

    // Forum — modération
    Route::prefix('forum')->name('forum.')->group(function () {
        Route::get('/', [AdminForumController::class, 'index'])->name('index');
        Route::post('/threads/{thread}/validate', [AdminForumController::class, 'validateThread'])->name('threads.validate');
        Route::delete('/threads/{thread}', [AdminForumController::class, 'destroyThread'])->name('threads.destroy');
        Route::delete('/replies/{reply}', [AdminForumController::class, 'destroyReply'])->name('replies.destroy');
    });

    // Flux RSS entrants
    Route::prefix('rss')->name('rss.')->group(function () {
        Route::get('/', [AdminRssController::class, 'index'])->name('index');
        Route::post('/sources', [AdminRssController::class, 'storeSource'])->name('sources.store');
        Route::patch('/sources/{source}/toggle', [AdminRssController::class, 'toggleSource'])->name('sources.toggle');
        Route::delete('/sources/{source}', [AdminRssController::class, 'destroySource'])->name('sources.destroy');
        Route::post('/fetch', [AdminRssController::class, 'fetch'])->name('fetch');
        Route::post('/items/{item}/approve', [AdminRssController::class, 'approve'])->name('items.approve');
        Route::post('/items/{item}/reject', [AdminRssController::class, 'reject'])->name('items.reject');
    });

    // Newsletter
    Route::prefix('newsletter')->name('newsletter.')->group(function () {
        Route::get('/', [AdminNewsletterController::class, 'index'])->name('index');
        Route::post('/test-email', [AdminNewsletterController::class, 'sendTestEmail'])->name('test-email');
        Route::get('/abonnes/export', [AdminNewsletterController::class, 'exportSubscribers'])->name('subscribers.export');
        Route::delete('/abonnes/{subscriber}', [AdminNewsletterController::class, 'destroySubscriber'])->name('subscribers.destroy');

        Route::get('/campagnes/creer', [AdminNewsletterController::class, 'createCampaign'])->name('campaigns.create');
        Route::post('/campagnes', [AdminNewsletterController::class, 'storeCampaign'])->name('campaigns.store');
        Route::get('/campagnes/{campaign}/apercu', [AdminNewsletterController::class, 'previewCampaign'])->name('campaigns.preview');
        Route::get('/campagnes/{campaign}', [AdminNewsletterController::class, 'showCampaign'])->name('campaigns.show');
        Route::get('/campagnes/{campaign}/modifier', [AdminNewsletterController::class, 'editCampaign'])->name('campaigns.edit');
        Route::put('/campagnes/{campaign}', [AdminNewsletterController::class, 'updateCampaign'])->name('campaigns.update');
        Route::delete('/campagnes/{campaign}', [AdminNewsletterController::class, 'destroyCampaign'])->name('campaigns.destroy');
        Route::post('/campagnes/{campaign}/envoyer', [AdminNewsletterController::class, 'sendCampaign'])->name('campaigns.send');
        Route::post('/campagnes/{campaign}/relancer', [AdminNewsletterController::class, 'retryCampaign'])->name('campaigns.retry');
        Route::post('/campagnes/{campaign}/annuler', [AdminNewsletterController::class, 'cancelCampaign'])->name('campaigns.cancel');
    });

    }); // fin middleware admin + 2FA
});
