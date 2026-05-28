<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Actualite;
use App\Models\Event;
use App\Models\Formation;
use App\Models\ForumThread;
use App\Models\Media;
use App\Models\Resource;
use App\Support\PublicContentGate;
use Illuminate\Http\RedirectResponse;

class PublicManageController extends Controller
{
    public function toggleResource(Resource $ressource): RedirectResponse
    {
        PublicContentGate::authorize(['publier-bibliotheque']);

        $ressource->update(['is_validated' => ! $ressource->is_validated]);

        return $this->backWithFlash(
            $ressource->is_validated
                ? 'Ressource publiée sur la bibliothèque.'
                : 'Ressource retirée de la bibliothèque publique.'
        );
    }

    public function toggleActualite(Actualite $actualite): RedirectResponse
    {
        PublicContentGate::authorize(['publier-actualites']);

        $published = ! $actualite->is_published;
        $actualite->update([
            'is_published'  => $published,
            'published_at'  => $published ? ($actualite->published_at ?? now()) : $actualite->published_at,
        ]);

        return $this->backWithFlash(
            $published
                ? 'Actualité publiée.'
                : 'Actualité dépubliée.'
        );
    }

    public function toggleMedia(Media $media): RedirectResponse
    {
        PublicContentGate::authorize(['contribuer-multimedia']);

        $published = ! $media->is_published;
        $media->update([
            'is_published' => $published,
            'published_at' => $published ? ($media->published_at ?? now()) : $media->published_at,
        ]);

        return $this->backWithFlash(
            $published
                ? 'Média publié.'
                : 'Média dépublié.'
        );
    }

    public function toggleEvent(Event $event): RedirectResponse
    {
        PublicContentGate::authorize(['creer-evenements']);

        $event->update(['is_validated' => ! $event->is_validated]);

        return $this->backWithFlash(
            $event->is_validated
                ? 'Événement publié.'
                : 'Événement dépublié.'
        );
    }

    public function toggleFormation(Formation $formation): RedirectResponse
    {
        PublicContentGate::authorize(['gerer-formations', 'administrer-utilisateurs']);

        $formation->update(['is_validated' => ! $formation->is_validated]);

        return $this->backWithFlash(
            $formation->is_validated
                ? 'Formation publiée.'
                : 'Formation dépubliée.'
        );
    }

    public function toggleActor(Actor $actor): RedirectResponse
    {
        PublicContentGate::authorize(['gerer-carte', 'soumettre-acteur']);

        $actor->update(['is_validated' => ! $actor->is_validated]);

        return $this->backWithFlash(
            $actor->is_validated
                ? 'Fiche acteur publiée sur la carte.'
                : 'Fiche acteur retirée de la carte.'
        );
    }

    public function toggleForumThread(ForumThread $thread): RedirectResponse
    {
        PublicContentGate::authorize(['moderer-forum']);

        $thread->update(['is_validated' => ! $thread->is_validated]);

        return $this->backWithFlash(
            $thread->is_validated
                ? 'Discussion publiée.'
                : 'Discussion dépubliée.'
        );
    }

    protected function backWithFlash(string $message): RedirectResponse
    {
        return back()->with('success', $message);
    }
}
