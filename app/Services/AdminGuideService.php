<?php

namespace App\Services;

use App\Models\User;

class AdminGuideService
{
    public function roleLabel(User $user): string
    {
        $role = $user->roles->first()?->name;

        return match ($role) {
            'super_admin'        => 'administrateur système',
            'moderateur'         => 'modérateur',
            'contributeur'       => 'contributeur',
            'partenaire_externe' => 'partenaire externe',
            'validateur_inscriptions' => 'validateur d\'inscriptions',
            default              => 'utilisateur du back-office',
        };
    }

    public function showOnLoad(User $user): bool
    {
        return $user->admin_guide_completed_at === null;
    }

    /**
     * @return list<array{title: string, text: string, actions: list<string>, target: ?string, label: ?string}>
     */
    public function stepsFor(User $user): array
    {
        $roleLabel = $this->roleLabel($user);
        $isSuperAdmin = $user->hasRole('super_admin');

        $steps = [
            [
                'title'   => 'Bienvenue dans le back-office',
                'text'    => "En tant que {$roleLabel}, ce guide présente uniquement les rubriques du menu auxquelles vous avez accès. Chaque étape surligne une entrée à gauche et détaille ce que vous pouvez y faire.",
                'actions' => [
                    'Parcourez les étapes avec « Suivant » ou les flèches du clavier.',
                    'Fermez à tout moment avec « Passer » ou la touche Échap.',
                    'Relancez le guide via « Guide d\'utilisation » en haut à droite.',
                ],
                'target'  => null,
                'label'   => null,
            ],
            [
                'title'   => 'Tableau de bord',
                'text'    => 'Point d\'entrée du back-office : vue d\'ensemble de l\'activité de la plateforme.',
                'actions' => [
                    'Consulter les statistiques globales (contenus, membres, activité récente).',
                    'Repérer d\'un coup d\'œil les éléments en attente de validation.',
                    'Accéder rapidement aux sections les plus utilisées.',
                ],
                'target'  => 'admin-nav-dashboard',
                'label'   => 'Tableau de bord',
            ],
        ];

        if ($user->can('administrer-utilisateurs')) {
            $steps[] = [
                'title'   => 'Inscriptions en attente',
                'text'    => 'Gérez les demandes d\'accès au back-office soumises par de nouveaux membres.',
                'actions' => [
                    'Consulter la liste des comptes en attente d\'approbation.',
                    'Examiner le profil et l\'organisation du candidat.',
                    'Approuver ou refuser une inscription.',
                ],
                'target'  => 'admin-nav-users-pending',
                'label'   => 'Inscriptions',
            ];

            $steps[] = [
                'title'   => 'Utilisateurs',
                'text'    => 'Administration des comptes membres et de leurs accès au back-office.',
                'actions' => [
                    'Rechercher et consulter les fiches utilisateurs.',
                    'Modifier les profils et les informations de contact.',
                    'Attribuer un rôle (modérateur, contributeur, etc.) adapté à chaque membre.',
                ],
                'target'  => 'admin-nav-users',
                'label'   => 'Utilisateurs',
            ];
        }

        if ($user->can('publier-actualites')) {
            $steps[] = [
                'title'   => 'Actualités',
                'text'    => 'Publiez et gérez les articles d\'information visibles sur le site public.',
                'actions' => [
                    'Rédiger de nouveaux articles avec texte, image et extrait.',
                    'Enregistrer en brouillon ou publier immédiatement.',
                    'Modifier, dépublier ou supprimer des actualités existantes.',
                ],
                'target'  => 'admin-nav-actualites',
                'label'   => 'Actualités',
            ];
        }

        if ($user->can('publier-bibliotheque')) {
            $steps[] = [
                'title'   => 'Bibliothèque',
                'text'    => 'Déposez et organisez les ressources documentaires de la plateforme.',
                'actions' => [
                    'Ajouter des documents (PDF, rapports, guides…).',
                    'Valider ou rejeter les ressources soumises par les contributeurs.',
                    'Mettre à jour ou retirer des documents de la bibliothèque publique.',
                ],
                'target'  => 'admin-nav-ressources',
                'label'   => 'Bibliothèque',
            ];
        }

        if ($user->can('creer-evenements')) {
            $steps[] = [
                'title'   => 'Événements',
                'text'    => 'Créez et maintenez l\'agenda des événements de la communauté 3AO.',
                'actions' => [
                    'Créer un événement (titre, dates, lieu, description, visuel).',
                    'Publier l\'événement sur le calendrier public.',
                    'Modifier ou archiver les événements passés ou à venir.',
                ],
                'target'  => 'admin-nav-evenements',
                'label'   => 'Événements',
            ];
        }

        if ($user->can('contribuer-multimedia')) {
            $steps[] = [
                'title'   => 'Médias',
                'text'    => 'Enrichissez le site avec des contenus vidéo, photos et autres médias.',
                'actions' => [
                    'Téléverser des vidéos, images ou fichiers multimédias.',
                    'Organiser la galerie et les contenus associés au site.',
                    'Mettre à jour ou retirer des médias obsolètes.',
                ],
                'target'  => 'admin-nav-medias',
                'label'   => 'Médias',
            ];
        }

        if ($user->can('gerer-formations')) {
            $steps[] = [
                'title'   => 'Formations',
                'text'    => 'Concevez des parcours de formation en ligne pour les membres.',
                'actions' => [
                    'Créer des formations et définir leur programme.',
                    'Ajouter des modules, leçons et quiz d\'évaluation.',
                    'Valider, publier ou retirer une formation.',
                ],
                'target'  => 'admin-nav-formations',
                'label'   => 'Formations',
            ];
        }

        if ($user->can('gerer-carte') || $user->can('soumettre-acteur')) {
            $actions = [];

            if ($user->can('soumettre-acteur')) {
                $actions[] = 'Soumettre ou mettre à jour la fiche de votre organisation.';
            }

            if ($user->can('gerer-carte')) {
                $actions[] = 'Valider les fiches acteurs soumises par les membres.';
                $actions[] = 'Positionner et corriger les points sur la carte interactive.';
                $actions[] = 'Modifier les informations affichées sur la cartographie publique.';
            } else {
                $actions[] = 'Suivre l\'état de validation de votre fiche acteur.';
            }

            $steps[] = [
                'title'   => 'Acteurs & Carte',
                'text'    => 'Gérez le réseau des organisations et leur visibilité sur la carte agroécologique.',
                'actions' => $actions,
                'target'  => 'admin-nav-acteurs',
                'label'   => 'Acteurs',
            ];
        }

        if ($user->can('moderer-forum')) {
            $steps[] = [
                'title'   => 'Forum',
                'text'    => 'Modérez les échanges entre membres pour garantir un espace respectueux.',
                'actions' => [
                    'Consulter les fils de discussion et les messages signalés.',
                    'Valider les sujets en attente de modération.',
                    'Supprimer ou verrouiller des contenus inappropriés.',
                ],
                'target'  => 'admin-nav-forum',
                'label'   => 'Forum',
            ];
        }

        if ($isSuperAdmin) {
            $steps[] = [
                'title'   => 'Utilisateurs & Droits',
                'text'    => 'Administration avancée réservée à l\'administrateur système.',
                'actions' => [
                    'Gérer l\'ensemble des comptes et leurs rôles.',
                    'Configurer finement les permissions Spatie par rôle.',
                    'Superviser l\'accès complet au back-office et aux modules sensibles.',
                ],
                'target'  => 'admin-nav-users-system',
                'label'   => 'Droits',
            ];
        }

        if ($user->can('gerer-newsletter')) {
            $steps[] = [
                'title'   => 'Newsletter',
                'text'    => 'Communiquez par e-mail avec les abonnés de la plateforme.',
                'actions' => [
                    'Consulter et exporter la liste des abonnés.',
                    'Composer des campagnes à partir des actualités et événements.',
                    'Envoyer immédiatement ou programmer une campagne, puis suivre les statistiques d\'envoi.',
                ],
                'target'  => 'admin-nav-newsletter',
                'label'   => 'Newsletter',
            ];
        }

        if ($user->can('gerer-rss')) {
            $steps[] = [
                'title'   => 'Flux RSS entrants',
                'text'    => 'Automatisez l\'import d\'actualités depuis des sources externes.',
                'actions' => [
                    'Ajouter ou modifier des flux RSS à surveiller.',
                    'Contrôler la fréquence et le contenu importé automatiquement.',
                    'Activer ou désactiver une source selon vos besoins éditoriaux.',
                ],
                'target'  => 'admin-nav-rss',
                'label'   => 'RSS',
            ];
        }

        $accessibleCount = count(array_filter($steps, fn (array $s) => $s['target'] !== null));

        $steps[] = [
            'title'   => 'Vous êtes prêt !',
            'text'    => $accessibleCount > 0
                ? "Vous connaissez maintenant les {$accessibleCount} rubriques accessibles avec votre profil. Bonne utilisation du back-office 3AO !"
                : 'Votre accès au back-office est limité pour le moment. Contactez un administrateur si vous pensez qu\'il manque des rubriques.',
            'actions' => [
                'Relancez ce guide à tout moment via « Guide d\'utilisation » en haut à droite.',
                'Consultez le tableau de bord pour suivre l\'activité au quotidien.',
            ],
            'target'  => null,
            'label'   => null,
        ];

        return $steps;
    }
}
