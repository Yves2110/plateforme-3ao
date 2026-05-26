<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Actualite;
use App\Models\Resource;
use App\Models\Event;
use App\Models\Actor;
use App\Models\Tag;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Tags =====
        $tags = [
            ['name' => 'Agroécologie',       'slug' => 'agroecologie',       'color' => '#52B788'],
            ['name' => 'Semences paysannes',  'slug' => 'semences-paysannes', 'color' => '#D4A017'],
            ['name' => 'Eau & Sols',          'slug' => 'eau-sols',           'color' => '#3B82F6'],
            ['name' => 'Politiques publiques','slug' => 'politiques',         'color' => '#8B5CF6'],
            ['name' => 'Changement climatique','slug' => 'climat',            'color' => '#14B8A6'],
            ['name' => 'Marchés & Filières',  'slug' => 'marches',            'color' => '#F97316'],
        ];
        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }

        // ===== Actualités =====
        $actualites = [
            [
                'title'        => 'Appel à projets : Innovations agroécologiques 2024',
                'slug'         => 'appel-projets-innovations-agroecologiques-2024',
                'content'      => 'L\'Alliance 3AO lance un appel à projets pour financer des innovations agroécologiques dans la région Afrique de l\'Ouest.',
                'category'     => 'Annonce',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(6),
            ],
            [
                'title'        => 'Forum régional sur l\'agroécologie : inscription ouverte',
                'slug'         => 'forum-regional-agroecologie-inscription',
                'content'      => 'Le forum régional annuel sur l\'agroécologie se tiendra en juin 2026 à Ouagadougou. Les inscriptions sont ouvertes.',
                'category'     => 'Événement',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(7),
            ],
            [
                'title'        => 'Nouveau guide pratique sur la fertilisation organique',
                'slug'         => 'guide-pratique-fertilisation-organique',
                'content'      => '3AO publie un nouveau guide pratique destiné aux agriculteurs sur les techniques de fertilisation organique adaptées aux sols sahéliens.',
                'category'     => 'Publication',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(8),
            ],
            [
                'title'        => 'Le ROPPA et ses membres renforcent leur collaboration',
                'slug'         => 'roppa-membres-renforcent-collaboration',
                'content'      => 'Réunion stratégique à Dakar entre ROPPA, 3AO et les organisations paysannes membres pour renforcer les synergies régionales.',
                'category'     => 'Actualité',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(10),
            ],
            [
                'title'        => 'Résultats du programme DESIRA+ : bilan positif',
                'slug'         => 'resultats-programme-desira-bilan',
                'content'      => 'Le programme DESIRA+ financé par la CEDEAO présente des résultats encourageants sur la transition agroécologique en Afrique de l\'Ouest.',
                'category'     => 'Publication',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(14),
            ],
        ];
        foreach ($actualites as $data) {
            Actualite::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // ===== Ressources =====
        $ressources = [
            [
                'title'        => 'Guide des pratiques agroécologiques en zone sahélienne',
                'slug'         => 'guide-pratiques-agroecologiques-sahelien',
                'type'         => 'Guide technique',
                'abstract'     => 'Ce guide présente les principales pratiques agroécologiques adaptées aux conditions climatiques sahéliennes, avec des fiches pratiques pour les agriculteurs.',
                'author'       => 'CIRAD / 3AO',
                'language'     => 'fr',
                'country'      => 'Burkina Faso',
                'is_validated' => true,
                'published_at' => Carbon::now()->subMonths(2),
            ],
            [
                'title'        => 'Étude de cas : semences paysannes au Mali',
                'slug'         => 'etude-cas-semences-paysannes-mali',
                'type'         => 'Étude de cas',
                'abstract'     => 'Retour d\'expérience sur la conservation et la valorisation des semences paysannes traditionnelles dans la région de Ségou, Mali.',
                'author'       => 'ROPPA / CNOP-Mali',
                'language'     => 'fr',
                'country'      => 'Mali',
                'is_validated' => true,
                'published_at' => Carbon::now()->subMonths(3),
            ],
            [
                'title'        => 'Agroecology Transition in West Africa: Research Results',
                'slug'         => 'agroecology-transition-west-africa-research',
                'type'         => 'Publication scientifique',
                'abstract'     => 'This publication presents the main research results on agroecological transition in West Africa from 2020 to 2024.',
                'author'       => 'ARAA / CEDEAO',
                'language'     => 'en',
                'country'      => 'Régional',
                'is_validated' => true,
                'published_at' => Carbon::now()->subMonths(1),
            ],
        ];
        foreach ($ressources as $data) {
            Resource::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // ===== Événements =====
        $events = [
            [
                'title'        => 'Forum Régional Agroécologie 2026 — Ouagadougou',
                'slug'         => 'forum-regional-agroecologie-2026',
                'type'         => 'Forum',
                'description'  => 'Forum régional annuel réunissant les acteurs de l\'agroécologie en Afrique de l\'Ouest.',
                'start_date'   => Carbon::now()->addDays(30),
                'end_date'     => Carbon::now()->addDays(32),
                'location'     => 'Ouagadougou',
                'country'      => 'Burkina Faso',
                'lat'          => 12.3714,
                'lng'          => -1.5197,
                'is_online'    => false,
                'is_validated' => true,
            ],
            [
                'title'        => 'Webinaire : Techniques de compostage en milieu tropical',
                'slug'         => 'webinaire-techniques-compostage-tropical',
                'type'         => 'Webinaire',
                'description'  => 'Webinaire pratique sur les techniques de compostage adaptées au milieu tropical sahélien.',
                'start_date'   => Carbon::now()->addDays(15),
                'location'     => null,
                'country'      => null,
                'is_online'    => true,
                'is_validated' => true,
            ],
            [
                'title'        => 'Atelier de formation : Gestion intégrée des ravageurs',
                'slug'         => 'atelier-formation-gestion-ravageurs',
                'type'         => 'Atelier',
                'description'  => 'Formation pratique sur la gestion intégrée des ravageurs sans pesticides chimiques.',
                'start_date'   => Carbon::now()->addDays(45),
                'location'     => 'Dakar',
                'country'      => 'Sénégal',
                'lat'          => 14.7167,
                'lng'          => -17.4677,
                'is_online'    => false,
                'is_validated' => true,
            ],
        ];
        foreach ($events as $data) {
            Event::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // ===== Acteurs =====
        $actors = [
            [
                'name'         => 'ROPPA',
                'slug'         => 'roppa',
                'type'         => 'Réseau OP',
                'country'      => 'Burkina Faso',
                'region'       => 'Afrique de l\'Ouest',
                'lat'          => 12.3714,
                'lng'          => -1.5197,
                'description'  => 'Réseau des Organisations Paysannes et de Producteurs de l\'Afrique de l\'Ouest.',
                'website'      => 'https://roppa-afrique.org',
                'is_validated' => true,
            ],
            [
                'name'         => 'CNOP-Mali',
                'slug'         => 'cnop-mali',
                'type'         => 'OP',
                'country'      => 'Mali',
                'region'       => 'Afrique de l\'Ouest',
                'lat'          => 12.6392,
                'lng'          => -8.0029,
                'description'  => 'Coordination Nationale des Organisations Paysannes du Mali.',
                'is_validated' => true,
            ],
            [
                'name'         => 'FONGS Sénégal',
                'slug'         => 'fongs-senegal',
                'type'         => 'OP',
                'country'      => 'Sénégal',
                'region'       => 'Afrique de l\'Ouest',
                'lat'          => 14.7167,
                'lng'          => -17.4677,
                'description'  => 'Fédération des Organisations Non Gouvernementales du Sénégal.',
                'is_validated' => true,
            ],
        ];
        foreach ($actors as $data) {
            Actor::firstOrCreate(['slug' => $data['slug']], $data);
        }

        $this->command->info('✅ Données de démonstration insérées avec succès !');
    }
}
