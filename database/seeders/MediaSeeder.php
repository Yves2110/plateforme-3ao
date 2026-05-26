<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Media;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title'       => 'Forum régional agroécologie Dakar 2025',
                'type'        => 'video',
                'description' => 'Temps forts du forum régional tenu à Dakar en novembre 2025 réunissant 300 acteurs de 15 pays.',
                'url'         => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'duration'    => '18:42',
                'source'      => 'YouTube / 3AO',
                'views'       => 312,
            ],
            [
                'title'       => 'Pratiques de conservation des semences au Sahel',
                'type'        => 'video',
                'description' => 'Reportage sur les techniques paysannes de conservation in situ des semences locales.',
                'url'         => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'duration'    => '24:15',
                'source'      => 'YouTube / FAO',
                'views'       => 189,
            ],
            [
                'title'       => 'Épisode 1 — L\'agroécologie, c\'est quoi ?',
                'type'        => 'podcast',
                'description' => 'Introduction à l\'agroécologie par des chercheurs et praticiens africains.',
                'url'         => 'https://example.com/podcast1.mp3',
                'duration'    => '32:10',
                'source'      => '3AO Podcast',
                'views'       => 256,
            ],
            [
                'title'       => 'Épisode 2 — Femmes agricultrices, actrices du changement',
                'type'        => 'podcast',
                'description' => 'Témoignages de femmes agricultrices transformant leurs communautés par l\'agroécologie.',
                'url'         => 'https://example.com/podcast2.mp3',
                'duration'    => '41:05',
                'source'      => '3AO Podcast',
                'views'       => 198,
            ],
            [
                'title'       => 'Épisode 3 — Politiques publiques et transition agroécologique',
                'type'        => 'podcast',
                'description' => 'Analyse des cadres politiques nationaux favorables à l\'agroécologie au Sénégal, Mali et Burkina.',
                'url'         => 'https://example.com/podcast3.mp3',
                'duration'    => '55:30',
                'source'      => '3AO Podcast',
                'views'       => 143,
            ],
            [
                'title'       => 'Champs agroforestiers du Mali — Série photographique',
                'type'        => 'photo',
                'description' => 'Série de photos illustrant les systèmes agroforestiers dans la région de Ségou au Mali.',
                'views'       => 421,
            ],
            [
                'title'       => 'Marchés locaux et circuits courts au Sénégal',
                'type'        => 'photo',
                'description' => 'Photoreportage sur les marchés de producteurs biologiques à Thiès et Fatick.',
                'views'       => 287,
            ],
            [
                'title'       => 'Galerie — Forum de Dakar 2025',
                'type'        => 'gallery',
                'description' => 'Galerie photo officielle du Forum Régional de l\'Agroécologie tenu à Dakar les 14-16 novembre 2025.',
                'views'       => 534,
            ],
            [
                'title'       => 'Galerie — Atelier semences paysannes Bamako',
                'type'        => 'gallery',
                'description' => 'Atelier de formation sur la sélection et la conservation des semences paysannes au Mali.',
                'views'       => 312,
            ],
            [
                'title'       => 'Webinaire — Agroécologie et changement climatique',
                'type'        => 'video',
                'description' => 'Enregistrement du webinaire du 20 janvier 2026 sur les synergies entre adaptation climatique et agroécologie.',
                'url'         => 'https://www.youtube.com/watch?v=LXb3EKWsInQ',
                'duration'    => '1:02:30',
                'source'      => 'YouTube / 3AO',
                'views'       => 445,
            ],
            [
                'title'       => 'Épisode 4 — Sols vivants et microbiome agricole',
                'type'        => 'podcast',
                'description' => 'Dialogue entre un pédologue et un paysan innovateur sur la vie des sols et les pratiques de régénération.',
                'url'         => 'https://example.com/podcast4.mp3',
                'duration'    => '48:20',
                'source'      => '3AO Podcast',
                'views'       => 167,
            ],
            [
                'title'       => 'Jardins communautaires urbains — Dakar',
                'type'        => 'photo',
                'description' => 'Initiatives d\'agriculture urbaine dans les quartiers périphériques de Dakar.',
                'views'       => 209,
            ],
        ];

        foreach ($items as $data) {
            Media::create([
                'title'        => $data['title'],
                'slug'         => Str::slug($data['title']) . '-' . Str::random(4),
                'type'         => $data['type'],
                'description'  => $data['description'],
                'url'          => $data['url'] ?? null,
                'duration'     => $data['duration'] ?? null,
                'source'       => $data['source'] ?? null,
                'is_published' => true,
                'published_at' => now()->subDays(rand(1, 180)),
                'views'        => $data['views'],
            ]);
        }
    }
}
