<?php

namespace Database\Seeders;

use App\Models\Actualite;
use App\Models\Actor;
use App\Models\ActorLink;
use App\Models\Event;
use App\Models\Formation;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\Resource;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class FullDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ===== USERS =====
        $admin = User::firstOrCreate(['email' => 'admin@3ao.org'], [
            'name'     => 'Admin 3AO',
            'password' => Hash::make('Admin3AO@2026!'),
            'country'  => 'Sénégal',
            'organization' => 'Alliance 3AO',
            'bio'      => 'Administrateur de la Plateforme Collaborative pour l\'Agroécologie.',
        ]);
        $adminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin->assignRole($adminRole);

        $users = [
            ['name' => 'Aminata Diallo',   'email' => 'aminata@demo.org',  'country' => 'Sénégal',       'organization' => 'FONGS Sénégal'],
            ['name' => 'Moussa Coulibaly', 'email' => 'moussa@demo.org',   'country' => 'Mali',           'organization' => 'CNOP-Mali'],
            ['name' => 'Fatou Konaté',     'email' => 'fatou@demo.org',    'country' => 'Burkina Faso',   'organization' => 'ROPPA'],
            ['name' => 'Ibrahim Sawadogo', 'email' => 'ibrahim@demo.org',  'country' => 'Burkina Faso',   'organization' => 'AProBio BF'],
            ['name' => 'Aïssata Traoré',  'email' => 'aissata@demo.org',  'country' => 'Côte d\'Ivoire', 'organization' => 'Agri-Bio CI'],
        ];
        $createdUsers = [];
        $contribRole = Role::firstOrCreate(['name' => 'contributeur', 'guard_name' => 'web']);
        foreach ($users as $u) {
            $user = User::firstOrCreate(['email' => $u['email']], array_merge($u, [
                'password' => Hash::make('Demo@2026!'),
                'bio'      => 'Membre actif de la communauté agroécologique.',
            ]));
            $user->assignRole($contribRole);
            $createdUsers[] = $user;
        }

        // ===== TAGS =====
        $tagData = [
            ['name' => 'Agroécologie',        'slug' => 'agroecologie',        'color' => '#52B788'],
            ['name' => 'Semences paysannes',   'slug' => 'semences-paysannes',  'color' => '#D4A017'],
            ['name' => 'Eau & Sols',           'slug' => 'eau-sols',            'color' => '#3B82F6'],
            ['name' => 'Politiques publiques', 'slug' => 'politiques',          'color' => '#8B5CF6'],
            ['name' => 'Changement climatique','slug' => 'climat',              'color' => '#14B8A6'],
            ['name' => 'Marchés & Filières',   'slug' => 'marches',             'color' => '#F97316'],
            ['name' => 'Fertilité des sols',   'slug' => 'fertilite-sols',      'color' => '#84CC16'],
            ['name' => 'Biodiversité',         'slug' => 'biodiversite',        'color' => '#10B981'],
        ];
        foreach ($tagData as $t) {
            Tag::firstOrCreate(['slug' => $t['slug']], $t);
        }

        // ===== ACTUALITÉS =====
        $actualites = [
            [
                'title'        => 'Appel à projets : Innovations agroécologiques 2026',
                'slug'         => 'appel-projets-innovations-agroecologiques-2026',
                'content'      => "L'Alliance 3AO lance un appel à projets pour financer des innovations agroécologiques dans la région Afrique de l'Ouest. Doté d'une enveloppe de 2 millions d'euros, cet appel cible les organisations paysannes, ONG et instituts de recherche.\n\nLes candidatures sont ouvertes jusqu'au 30 juin 2026. Les projets retenus bénéficieront d'un accompagnement technique et financier sur 24 mois.",
                'category'     => 'Financement',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(2),
                'user_id'      => $admin->id,
            ],
            [
                'title'        => 'Forum régional sur l\'agroécologie 2026 : inscriptions ouvertes',
                'slug'         => 'forum-regional-agroecologie-2026-inscriptions',
                'content'      => "Le forum régional annuel sur l'agroécologie se tiendra du 15 au 17 juillet 2026 à Ouagadougou. Cette édition rassemblera plus de 500 participants issus de 15 pays d'Afrique de l'Ouest.\n\nAu programme : tables rondes thématiques, présentations de projets innovants, ateliers pratiques et réseautage.",
                'category'     => 'Événement',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
                'user_id'      => $admin->id,
            ],
            [
                'title'        => 'Publication : Guide pratique de la fertilisation organique',
                'slug'         => 'publication-guide-fertilisation-organique',
                'content'      => "3AO publie un nouveau guide pratique destiné aux agriculteurs sur les techniques de fertilisation organique adaptées aux sols sahéliens. Ce guide de 120 pages est disponible en français, wolof et bambara.",
                'category'     => 'Publication',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(8),
                'user_id'      => $createdUsers[0]->id,
            ],
            [
                'title'        => 'Le ROPPA et ses membres renforcent la coopération régionale',
                'slug'         => 'roppa-cooperation-regionale-2026',
                'content'      => "Réunion stratégique à Dakar entre le ROPPA, l'Alliance 3AO et les organisations paysannes membres pour renforcer les synergies régionales sur les questions de souveraineté alimentaire et de transition agroécologique.",
                'category'     => 'Politique',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(12),
                'user_id'      => $createdUsers[2]->id,
            ],
            [
                'title'        => 'Résultats du programme DESIRA+ : bilan très positif',
                'slug'         => 'resultats-programme-desira-2026',
                'content'      => "Le programme DESIRA+ financé par la CEDEAO présente des résultats encourageants sur la transition agroécologique. 45 000 agriculteurs formés, 12 000 ha convertis en agroécologie, et une augmentation moyenne de 23% des rendements.",
                'category'     => 'Agroécologie',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(18),
                'user_id'      => $admin->id,
            ],
            [
                'title'        => 'Semences paysannes : un patrimoine à préserver d\'urgence',
                'slug'         => 'semences-paysannes-patrimoine-urgence',
                'content'      => "Un rapport alarmant révèle que 75% des variétés traditionnelles africaines ont disparu en 50 ans. L'Alliance 3AO lance une campagne de sensibilisation et de documentation des variétés locales encore cultivées.",
                'category'     => 'Semences',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(22),
                'user_id'      => $createdUsers[1]->id,
            ],
        ];
        foreach ($actualites as $data) {
            Actualite::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // ===== RESSOURCES =====
        $ressources = [
            [
                'title'        => 'Guide des pratiques agroécologiques en zone sahélienne',
                'slug'         => 'guide-pratiques-agroecologiques-sahelien',
                'type'         => 'pdf',
                'abstract'     => 'Ce guide présente les principales pratiques agroécologiques adaptées aux conditions climatiques sahéliennes, avec des fiches pratiques pour les agriculteurs : zaï, demi-lunes, haies vives, compostage.',
                'language'     => 'fr',
                'is_validated' => true,
                'user_id'      => $admin->id,
            ],
            [
                'title'        => 'Étude de cas : semences paysannes au Mali — Région de Ségou',
                'slug'         => 'etude-cas-semences-paysannes-mali-segou',
                'type'         => 'rapport',
                'abstract'     => 'Retour d\'expérience sur la conservation et la valorisation des semences paysannes traditionnelles dans la région de Ségou, Mali. Méthodologie, résultats et recommandations.',
                'language'     => 'fr',
                'is_validated' => true,
                'user_id'      => $createdUsers[1]->id,
            ],
            [
                'title'        => 'Agroecology Transition in West Africa: Research Results 2020–2024',
                'slug'         => 'agroecology-transition-west-africa-2020-2024',
                'type'         => 'article',
                'abstract'     => 'This publication presents the main research results on agroecological transition in West Africa from 2020 to 2024, covering 8 countries and 120 research sites.',
                'language'     => 'en',
                'is_validated' => true,
                'user_id'      => $admin->id,
            ],
            [
                'title'        => 'Manuel de compostage pour les agriculteurs d\'Afrique de l\'Ouest',
                'slug'         => 'manuel-compostage-agriculteurs-afrique-ouest',
                'type'         => 'guide',
                'abstract'     => 'Manuel pratique sur les différentes techniques de compostage (Berkley, Indore, vermicompostage) adaptées aux conditions tropicales avec des ressources locales.',
                'language'     => 'fr',
                'is_validated' => true,
                'user_id'      => $createdUsers[0]->id,
            ],
            [
                'title'        => 'Politique agricole commune de la CEDEAO et agroécologie',
                'slug'         => 'politique-agricole-cedeao-agroecologie',
                'type'         => 'rapport',
                'abstract'     => 'Analyse critique de l\'ECOWAP et des opportunités pour intégrer les principes de l\'agroécologie dans les politiques agricoles régionales.',
                'language'     => 'fr',
                'is_validated' => true,
                'user_id'      => $createdUsers[2]->id,
            ],
        ];
        foreach ($ressources as $data) {
            Resource::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // ===== ÉVÉNEMENTS =====
        $events = [
            [
                'title'        => 'Forum Régional Agroécologie 2026 — Ouagadougou',
                'slug'         => 'forum-regional-agroecologie-2026',
                'type'         => 'forum',
                'description'  => 'Forum régional annuel réunissant 500+ acteurs de l\'agroécologie en Afrique de l\'Ouest. Tables rondes, ateliers, expositions et réseautage.',
                'start_date'   => Carbon::now()->addDays(30),
                'end_date'     => Carbon::now()->addDays(32),
                'location'     => 'Palais des Congrès',
                'country'      => 'Burkina Faso',
                'lat'          => 12.3714,
                'lng'          => -1.5197,
                'is_online'    => false,
                'is_validated' => true,
            ],
            [
                'title'        => 'Webinaire : Techniques de compostage en milieu tropical',
                'slug'         => 'webinaire-compostage-tropical-juin-2026',
                'type'         => 'webinaire',
                'description'  => 'Webinaire pratique sur les techniques de compostage adaptées au milieu tropical. Intervenants : experts du CIRAD et agriculteurs témoins.',
                'start_date'   => Carbon::now()->addDays(14),
                'is_online'    => true,
                'is_validated' => true,
            ],
            [
                'title'        => 'Atelier : Gestion intégrée des ravageurs — Dakar',
                'slug'         => 'atelier-gestion-ravageurs-dakar-2026',
                'type'         => 'atelier',
                'description'  => 'Formation pratique sur la gestion intégrée des ravageurs sans pesticides chimiques. 3 jours en présentiel avec visites de terrain.',
                'start_date'   => Carbon::now()->addDays(45),
                'end_date'     => Carbon::now()->addDays(47),
                'location'     => 'Centre de Formation FONGS',
                'country'      => 'Sénégal',
                'lat'          => 14.7167,
                'lng'          => -17.4677,
                'is_online'    => false,
                'is_validated' => true,
            ],
            [
                'title'        => 'Conférence : Souveraineté semencière en Afrique de l\'Ouest',
                'slug'         => 'conference-souverainete-semenciere-bamako',
                'type'         => 'conference',
                'description'  => 'Conférence internationale sur les enjeux de la souveraineté semencière, les lois semencières UPOV et les droits des agriculteurs.',
                'start_date'   => Carbon::now()->addDays(60),
                'location'     => 'Hôtel de l\'Amitié',
                'country'      => 'Mali',
                'lat'          => 12.6392,
                'lng'          => -8.0029,
                'is_online'    => false,
                'is_validated' => true,
            ],
        ];
        foreach ($events as $data) {
            Event::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // ===== ACTEURS =====
        $actors = [
            ['name' => 'ROPPA',         'slug' => 'roppa',          'type' => 'Réseau',       'country' => 'Burkina Faso',   'lat' => 12.3714,  'lng' => -1.5197,  'description' => 'Réseau des Organisations Paysannes et de Producteurs de l\'Afrique de l\'Ouest.', 'website' => 'https://roppa-afrique.org', 'is_validated' => true],
            ['name' => 'CNOP-Mali',     'slug' => 'cnop-mali',      'type' => 'ONG',          'country' => 'Mali',           'lat' => 12.6392,  'lng' => -8.0029,  'description' => 'Coordination Nationale des Organisations Paysannes du Mali.', 'is_validated' => true],
            ['name' => 'FONGS',         'slug' => 'fongs-senegal',  'type' => 'ONG',          'country' => 'Sénégal',        'lat' => 14.7167,  'lng' => -17.4677, 'description' => 'Fédération des ONG du Sénégal — appui aux organisations paysannes.', 'website' => 'https://fongs.sn', 'is_validated' => true],
            ['name' => 'AProBio BF',    'slug' => 'aprobio-bf',     'type' => 'ONG',          'country' => 'Burkina Faso',   'lat' => 11.1789,  'lng' => -4.2979,  'description' => 'Association pour la Promotion de l\'Agriculture Biologique au Burkina Faso.', 'is_validated' => true],
            ['name' => 'CIRAD',         'slug' => 'cirad-ao',       'type' => 'Institution',  'country' => 'Sénégal',        'lat' => 14.7500,  'lng' => -17.3000, 'description' => 'Centre de coopération internationale en recherche agronomique pour le développement.', 'website' => 'https://cirad.fr', 'is_validated' => true],
            ['name' => 'ARAA / CEDEAO', 'slug' => 'araa-cedeao',   'type' => 'Institution',  'country' => 'Togo',           'lat' => 6.1370,   'lng' => 1.2123,   'description' => 'Agence Régionale pour l\'Agriculture et l\'Alimentation de la CEDEAO.', 'is_validated' => true],
            ['name' => 'Agri-Bio CI',   'slug' => 'agri-bio-ci',    'type' => 'Entreprise',   'country' => 'Côte d\'Ivoire', 'lat' => 5.3600,   'lng' => -4.0083,  'description' => 'Entreprise sociale spécialisée dans la transition agroécologique en Côte d\'Ivoire.', 'is_validated' => true],
            ['name' => 'FENOP',         'slug' => 'fenop-bf',       'type' => 'Réseau',       'country' => 'Burkina Faso',   'lat' => 12.2372,  'lng' => -1.5616,  'description' => 'Fédération Nationale des Organisations Paysannes du Burkina Faso.', 'is_validated' => true],
        ];
        $actorModels = [];
        foreach ($actors as $data) {
            $actorModels[$data['slug']] = Actor::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // Liens entre acteurs (réseau)
        $links = [
            ['from' => 'roppa', 'to' => 'cnop-mali',     'type' => 'partenariat'],
            ['from' => 'roppa', 'to' => 'fongs-senegal', 'type' => 'partenariat'],
            ['from' => 'roppa', 'to' => 'fenop-bf',      'type' => 'partenariat'],
            ['from' => 'roppa', 'to' => 'araa-cedeao',   'type' => 'projet'],
            ['from' => 'cirad-ao', 'to' => 'aprobio-bf', 'type' => 'projet',      'project' => 'DESIRA+'],
            ['from' => 'cirad-ao', 'to' => 'cnop-mali',  'type' => 'projet',      'project' => 'Semences locales'],
            ['from' => 'araa-cedeao', 'to' => 'fongs-senegal', 'type' => 'financement'],
            ['from' => 'agri-bio-ci', 'to' => 'cirad-ao', 'type' => 'projet'],
        ];
        foreach ($links as $l) {
            if (isset($actorModels[$l['from']]) && isset($actorModels[$l['to']])) {
                ActorLink::firstOrCreate([
                    'actor_id_from' => $actorModels[$l['from']]->id,
                    'actor_id_to'   => $actorModels[$l['to']]->id,
                    'relation_type' => $l['type'],
                ], ['project_name' => $l['project'] ?? null]);
            }
        }

        // ===== FORMATIONS =====
        $formations = [
            [
                'title'            => 'Atelier pratique : Zaï et demi-lunes pour la restauration des sols',
                'slug'             => 'atelier-zai-demi-lunes-restauration-sols',
                'type'             => 'atelier',
                'organizer'        => 'AProBio BF / ROPPA',
                'country'          => 'Burkina Faso',
                'location'         => 'Ouagadougou',
                'is_online'        => false,
                'start_date'       => Carbon::now()->addDays(20),
                'end_date'         => Carbon::now()->addDays(22),
                'duration'         => '3 jours',
                'description'      => 'Formation pratique sur les techniques de zaï et demi-lunes pour restaurer les sols dégradés. Travaux en champ avec des agriculteurs expérimentés.',
                'objectives'       => "• Comprendre les mécanismes de dégradation des sols sahéliens\n• Maîtriser la technique du zaï traditionnel et amélioré\n• Construire des demi-lunes pour la collecte d'eau\n• Planifier la restauration d'un champ dégradé",
                'audience'         => 'Agriculteurs, agents de vulgarisation, techniciens agricoles',
                'language'         => 'fr',
                'price'            => null,
                'registration_url' => 'https://3ao.org/inscription-formation',
                'is_validated'     => true,
                'user_id'          => $admin->id,
            ],
            [
                'title'            => 'Webinaire : Introduction à l\'agroforesterie tropicale',
                'slug'             => 'webinaire-agroforesterie-tropicale',
                'type'             => 'webinaire',
                'organizer'        => 'CIRAD / Alliance 3AO',
                'is_online'        => true,
                'start_date'       => Carbon::now()->addDays(10),
                'duration'         => '2 heures',
                'description'      => 'Webinaire introductif sur les systèmes agroforestiers adaptés à l\'Afrique de l\'Ouest : parkings agroforestiers, haies vives, cultures sous couvert arboré.',
                'objectives'       => "• Comprendre les bénéfices de l'agroforesterie\n• Découvrir les espèces d'arbres adaptées à chaque zone\n• Identifier les opportunités d'intégration dans son exploitation",
                'audience'         => 'Tous publics, agriculteurs, ONG',
                'language'         => 'fr',
                'price'            => null,
                'registration_url' => 'https://zoom.us/register',
                'is_validated'     => true,
                'user_id'          => $admin->id,
            ],
            [
                'title'            => 'Certification : Agriculteur en agroécologie — Niveau 1',
                'slug'             => 'certification-agriculteur-agroecologie-niveau-1',
                'type'             => 'certification',
                'organizer'        => 'FONGS / Alliance 3AO',
                'country'          => 'Sénégal',
                'location'         => 'Thiès',
                'is_online'        => false,
                'start_date'       => Carbon::now()->addDays(60),
                'end_date'         => Carbon::now()->addDays(74),
                'duration'         => '2 semaines',
                'description'      => 'Programme de certification reconnu à l\'échelle régionale pour les agriculteurs engagés dans la transition agroécologique. Théorie et pratique en alternance.',
                'objectives'       => "• Acquérir les bases théoriques de l'agroécologie\n• Mettre en œuvre 6 pratiques agroécologiques certifiantes\n• Obtenir le certificat régional 3AO Niveau 1",
                'audience'         => 'Agriculteurs avec au moins 2 ans d\'expérience',
                'language'         => 'fr',
                'price'            => 15000,
                'registration_url' => 'https://3ao.org/certification',
                'is_validated'     => true,
                'user_id'          => $admin->id,
            ],
            [
                'title'            => 'Cours en ligne : Gestion durable de l\'eau agricole',
                'slug'             => 'cours-gestion-eau-agricole',
                'type'             => 'cours',
                'organizer'        => 'ARAA / CEDEAO',
                'is_online'        => true,
                'duration'         => '6 semaines (auto-rythmé)',
                'description'      => 'Cours en ligne sur les techniques de gestion durable de l\'eau : collecte des eaux de pluie, irrigation goutte-à-goutte, recharge des nappes phréatiques.',
                'objectives'       => "• Maîtriser les techniques de collecte des eaux de pluie\n• Concevoir un système d'irrigation basse consommation\n• Calculer le bilan hydrique d'une exploitation",
                'audience'         => 'Agriculteurs, hydrologues, ONG, agents gouvernementaux',
                'language'         => 'fr',
                'price'            => null,
                'registration_url' => 'https://elearning.araa.org',
                'is_validated'     => true,
                'user_id'          => $createdUsers[2]->id,
            ],
        ];
        foreach ($formations as $data) {
            Formation::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // ===== FORUM THREADS =====
        $threads = [
            [
                'title'        => 'Quelles variétés de mil résistantes à la sécheresse pour le Sahel ?',
                'slug'         => 'varietes-mil-resistantes-secheresse-sahel',
                'category'     => 'semences',
                'body'         => "Bonjour à toutes et tous,\n\nJe recherche des retours d'expérience sur les variétés de mil les plus adaptées aux épisodes de sécheresse de plus en plus fréquents dans notre zone (région de Ségou, Mali).\n\nEn 2024, j'ai testé la variété SOSAT-C88 avec de bons résultats, mais je voudrais diversifier. Avez-vous des recommandations basées sur votre expérience terrain ?",
                'is_validated' => true,
                'user_id'      => $createdUsers[1]->id,
            ],
            [
                'title'        => 'Expériences avec le zaï amélioré — partage de pratiques',
                'slug'         => 'experiences-zai-ameliore-partage-pratiques',
                'category'     => 'pratiques',
                'body'         => "Depuis 3 ans, j'expérimente le zaï amélioré avec ajout de compost dans les poquets. Les résultats sont vraiment encourageants :\n\n- Germination plus rapide\n- Plants plus robustes\n- Rendements en hausse de 40% la 3e année\n\nQui pratique aussi le zaï amélioré ? Quelles sont vos variantes ?",
                'is_validated' => true,
                'user_id'      => $createdUsers[3]->id,
            ],
            [
                'title'        => 'Impact de la loi UPOV 91 sur nos droits semenciers',
                'slug'         => 'impact-loi-upov-91-droits-semenciers',
                'category'     => 'politique',
                'body'         => "La pression pour adopter la convention UPOV 91 s'intensifie dans plusieurs pays de la CEDEAO. Cette loi risque de criminaliser nos pratiques traditionnelles d'échange et de conservation de semences.\n\nComment nos organisations se mobilisent-elles face à cette menace ? Quelles alternatives juridiques proposer ?",
                'is_validated' => true,
                'user_id'      => $createdUsers[2]->id,
            ],
            [
                'title'        => 'Comment accéder aux marchés urbains pour les produits bio ?',
                'slug'         => 'acceder-marches-urbains-produits-bio',
                'category'     => 'marches',
                'body'         => "Nous produisons des légumes certifiés bio à 30 km de Dakar, mais l'accès aux marchés urbains reste difficile. Les intermédiaires prennent 60% de la marge.\n\nEstce que certains d'entre vous ont développé des circuits courts directs ? Livraison en ville, marchés bio, restaurants, épiceries spécialisées ?",
                'is_validated' => true,
                'user_id'      => $createdUsers[0]->id,
            ],
            [
                'title'        => 'Financement carbone pour les pratiques agroécologiques : mythe ou réalité ?',
                'slug'         => 'financement-carbone-pratiques-agroecologiques',
                'category'     => 'financement',
                'body'         => "On parle beaucoup de crédits carbone pour financer la transition agroécologique. Mais dans les faits, est-ce accessible aux petits producteurs en Afrique de l'Ouest ?\n\nJ'ai entendu parler de projets pilotes au Sénégal et au Ghana. Quelqu'un a de l'expérience avec ces mécanismes ?",
                'is_validated' => true,
                'user_id'      => $createdUsers[4]->id,
            ],
        ];

        $threadModels = [];
        foreach ($threads as $data) {
            $threadModels[] = ForumThread::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // Réponses forum
        $replies = [
            [
                'thread_idx'   => 0,
                'body'         => "La variété ICMV IS 89305 est excellente pour les zones à pluviométrie < 400mm. Je l'utilise depuis 5 ans avec des rendements stables même en année sèche.",
                'user_id'      => $createdUsers[3]->id,
                'is_validated' => true,
            ],
            [
                'thread_idx'   => 0,
                'body'         => "Pour compléter : le mil Souna III est très populaire au Sénégal pour sa précocité (75 jours). Intéressant pour les zones à saison des pluies courte.",
                'user_id'      => $createdUsers[0]->id,
                'is_validated' => true,
            ],
            [
                'thread_idx'   => 1,
                'body'         => "Bravo pour ces résultats ! Je pratique le zaï depuis 6 ans. Mon amélioration : j'ajoute des termites dans les poquets — elles creusent des galeries qui facilitent l'infiltration de l'eau.",
                'user_id'      => $createdUsers[2]->id,
                'is_validated' => true,
            ],
            [
                'thread_idx'   => 2,
                'body'         => "Le collectif Bové et plusieurs organisations paysannes africaines travaillent sur une charte alternative. Je peux partager les documents de travail si vous êtes intéressés.",
                'user_id'      => $admin->id,
                'is_validated' => true,
            ],
            [
                'thread_idx'   => 3,
                'body'         => "Nous avons lancé un AMAP (paniers hebdomadaires) avec 50 familles à Dakar. Ça marche bien mais la logistique est complexe. Je vous détaille notre organisation si vous voulez.",
                'user_id'      => $createdUsers[4]->id,
                'is_validated' => true,
            ],
        ];
        foreach ($replies as $r) {
            if (isset($threadModels[$r['thread_idx']])) {
                ForumReply::firstOrCreate([
                    'thread_id' => $threadModels[$r['thread_idx']]->id,
                    'user_id'   => $r['user_id'],
                    'body'      => $r['body'],
                ], ['is_validated' => true]);
            }
        }

        $this->call(FormationCatalogSeeder::class);

        $this->command->info('✅ FullDemoSeeder : toutes les données factices insérées !');
        $this->command->info('👤 Admin    : admin@3ao.org / Admin3AO@2026!');
        $this->command->info('👤 Membres  : aminata@demo.org / Demo@2026! (et autres)');
    }
}
