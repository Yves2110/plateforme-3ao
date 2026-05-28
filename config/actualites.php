<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Catégories officielles des actualités (libellé stocké en base)
    |--------------------------------------------------------------------------
    */
    'categories' => [
        'Actualité' => [
            'badge' => 'actualite',
            'description' => 'Nouvelles générales de la plateforme',
        ],
        'Annonce' => [
            'badge' => 'annonce',
            'description' => 'Annonces et communications officielles',
        ],
        'Événement' => [
            'badge' => 'evenement',
            'description' => 'Forums, ateliers, webinaires, rencontres',
        ],
        'Financement' => [
            'badge' => 'financement',
            'description' => 'Appels à projets et opportunités de financement',
        ],
        'Publication' => [
            'badge' => 'publication',
            'description' => 'Guides, rapports et ressources documentaires',
        ],
    ],

    /*
    | Anciennes valeurs → catégorie officielle (affichage + badges)
    */
    'legacy_map' => [
        'Agroécologie'  => 'Publication',
        'Semences'      => 'Publication',
        'Politique'     => 'Actualité',
        'Marché'        => 'Actualité',
        'Formation'     => 'Événement',
        'International' => 'Actualité',
        'Partenaire'    => 'Actualité',
        // variantes minuscules (anciens seeders)
        'agroécologie'  => 'Publication',
        'semences'      => 'Publication',
        'politique'     => 'Actualité',
        'marche'        => 'Actualité',
        'marché'        => 'Actualité',
        'marches'       => 'Actualité',
        'formation'     => 'Événement',
        'financement'   => 'Financement',
        'annonce'       => 'Annonce',
        'evenement'     => 'Événement',
        'événement'     => 'Événement',
        'publication'   => 'Publication',
        'actualite'     => 'Actualité',
        'actualité'     => 'Actualité',
        'pratiques'     => 'Publication',
    ],

];
