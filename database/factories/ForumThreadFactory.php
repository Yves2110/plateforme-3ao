<?php

namespace Database\Factories;

use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ForumThreadFactory extends Factory
{
    protected $model = ForumThread::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6);
        $categories = ['pratiques', 'semences', 'marche', 'politique', 'recherche', 'entraide'];

        return [
            'title'        => $title,
            'slug'         => Str::slug($title) . '-' . Str::random(4),
            'body'         => $this->faker->paragraphs(3, true),
            'category'     => $this->faker->randomElement($categories),
            'user_id'      => User::factory()->withPersonalTeam(),
            'views'        => $this->faker->numberBetween(0, 200),
            'is_pinned'    => false,
            'is_locked'    => false,
        ];
    }
}
