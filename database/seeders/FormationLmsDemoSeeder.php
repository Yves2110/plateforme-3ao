<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\FormationAnswer;
use App\Models\FormationLesson;
use App\Models\FormationModule;
use App\Models\FormationQuestion;
use App\Models\FormationQuiz;
use App\Models\User;
use Illuminate\Database\Seeder;

class FormationLmsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->first()
            ?? User::query()->first();

        if (! $author) {
            $this->command?->warn('Aucun utilisateur trouvé — contenu LMS démo ignoré.');

            return;
        }

        $formation = Formation::updateOrCreate(
            ['slug' => 'initiation-gestion-participative'],
            [
                'title' => 'Initiation à la gestion participative',
                'type' => 'cours',
                'organizer' => 'Réseau 3AO',
                'country' => 'Burkina Faso',
                'location' => null,
                'is_online' => true,
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->addMonths(3)->endOfMonth(),
                'duration' => '3 heures',
                'description' => "Ce parcours en ligne vous introduit aux principes de la gestion participative des aires protégées en Afrique de l'Ouest.\n\nVous progresserez à votre rythme à travers des modules théoriques et un quiz d'évaluation final.",
                'objectives' => "• Comprendre les fondements de la gouvernance participative\n• Identifier les acteurs clés d'un dispositif de co-gestion\n• Appliquer les principes de base à un cas concret",
                'audience' => 'Agents de terrain, gestionnaires de PA, membres des communautés riveraines',
                'language' => 'fr',
                'price' => 0,
                'registration_url' => null,
                'is_validated' => true,
                'user_id' => $author->id,
            ]
        );

        $module1 = FormationModule::updateOrCreate(
            ['formation_id' => $formation->id, 'order' => 1],
            [
                'title' => 'Module 1 — Fondamentaux',
                'description' => 'Les bases de la gouvernance participative.',
                'is_published' => true,
            ]
        );

        FormationLesson::updateOrCreate(
            ['module_id' => $module1->id, 'order' => 1],
            [
                'title' => 'Qu\'est-ce que la gestion participative ?',
                'description' => 'Introduction aux concepts clés.',
                'type' => 'text',
                'content' => "## Gestion participative\n\nLa gestion participative des aires protégées repose sur l'implication active des communautés locales dans la prise de décision, la planification et le suivi des activités de conservation.\n\n### Principes essentiels\n\n1. **Reconnaissance des droits** — Les communautés riveraines disposent de droits légitimes sur les ressources naturelles.\n2. **Partage des bénéfices** — Les retombées économiques de la conservation doivent profiter aux populations locales.\n3. **Transparence** — Les décisions et la gestion des ressources doivent être transparentes et consultables.\n4. **Renforcement des capacités** — Former et accompagner les acteurs locaux est indispensable.\n\n### Enjeux en Afrique de l'Ouest\n\nLes aires protégées transfrontalières (APTF) nécessitent une coordination entre États et une participation des populations des deux côtés des frontières.",
                'duration_minutes' => 15,
                'is_published' => true,
            ]
        );

        FormationLesson::updateOrCreate(
            ['module_id' => $module1->id, 'order' => 2],
            [
                'title' => 'Les acteurs de la co-gestion',
                'description' => 'Panorama des parties prenantes.',
                'type' => 'text',
                'content' => "## Cartographie des acteurs\n\n| Acteur | Rôle |\n|--------|------|\n| État | Cadre légal, appui technique |\n| Communautés | Gestion quotidienne, surveillance |\n| ONG | Appui technique, médiation |\n| Autorités coutumières | Médiation, légitimité locale |\n| Partenaires techniques | Financement, expertise |\n\nLa réussite d'un dispositif participatif dépend de la **clarté des rôles** et de la **confiance mutuelle** entre ces acteurs.",
                'duration_minutes' => 20,
                'is_published' => true,
            ]
        );

        $module2 = FormationModule::updateOrCreate(
            ['formation_id' => $formation->id, 'order' => 2],
            [
                'title' => 'Module 2 — Évaluation',
                'description' => 'Testez vos connaissances acquises.',
                'is_published' => true,
            ]
        );

        $quizLesson = FormationLesson::updateOrCreate(
            ['module_id' => $module2->id, 'order' => 1],
            [
                'title' => 'Quiz — Validation des acquis',
                'description' => 'Répondez aux questions pour valider ce parcours.',
                'type' => 'quiz',
                'content' => null,
                'duration_minutes' => 10,
                'is_published' => true,
            ]
        );

        $quiz = FormationQuiz::updateOrCreate(
            ['lesson_id' => $quizLesson->id],
            [
                'title' => 'Quiz de fin de parcours',
                'description' => '10 questions pour valider votre compréhension des fondamentaux.',
                'passing_score' => 70,
                'time_limit_minutes' => 15,
                'max_attempts' => 3,
                'is_published' => true,
                'show_correct_answers' => true,
            ]
        );

        $questions = [
            [
                'question' => 'La gestion participative implique les communautés locales dans la prise de décision.',
                'type' => 'true_false',
                'points' => 1,
                'answers' => [
                    ['answer' => 'Vrai', 'is_correct' => true],
                    ['answer' => 'Faux', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Quels sont les principes essentiels de la gestion participative ? (plusieurs réponses possibles)',
                'type' => 'multiple_choice',
                'points' => 2,
                'answers' => [
                    ['answer' => 'Reconnaissance des droits', 'is_correct' => true],
                    ['answer' => 'Exclusion des communautés', 'is_correct' => false],
                    ['answer' => 'Partage des bénéfices', 'is_correct' => true],
                    ['answer' => 'Transparence', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'Quel acteur est principalement responsable du cadre légal d\'une aire protégée ?',
                'type' => 'single_choice',
                'points' => 1,
                'answers' => [
                    ['answer' => 'L\'État', 'is_correct' => true],
                    ['answer' => 'Une ONG internationale', 'is_correct' => false],
                    ['answer' => 'Un tour-opérateur', 'is_correct' => false],
                    ['answer' => 'Un chasseur individuel', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Pourquoi le renforcement des capacités est-il important ?',
                'type' => 'single_choice',
                'points' => 1,
                'answers' => [
                    ['answer' => 'Pour permettre aux acteurs locaux de participer efficacement', 'is_correct' => true],
                    ['answer' => 'Pour remplacer les autorités coutumières', 'is_correct' => false],
                    ['answer' => 'Pour réduire le nombre de participants', 'is_correct' => false],
                    ['answer' => 'Pour éviter toute prise de décision collective', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Les APTF (aires protégées transfrontalières) nécessitent une coordination entre États.',
                'type' => 'true_false',
                'points' => 1,
                'answers' => [
                    ['answer' => 'Vrai', 'is_correct' => true],
                    ['answer' => 'Faux', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($questions as $order => $qData) {
            $question = FormationQuestion::updateOrCreate(
                ['quiz_id' => $quiz->id, 'order' => $order + 1],
                [
                    'question' => $qData['question'],
                    'type' => $qData['type'],
                    'points' => $qData['points'],
                    'explanation' => null,
                ]
            );

            foreach ($qData['answers'] as $aOrder => $aData) {
                FormationAnswer::updateOrCreate(
                    ['question_id' => $question->id, 'order' => $aOrder + 1],
                    [
                        'answer' => $aData['answer'],
                        'is_correct' => $aData['is_correct'],
                    ]
                );
            }
        }

        $this->command?->info("Formation LMS démo créée : {$formation->title} ({$formation->slug})");
    }
}
