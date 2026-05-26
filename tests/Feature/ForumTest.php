<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ForumThread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumTest extends TestCase
{
    use RefreshDatabase;

    public function test_forum_index_accessible_by_guests(): void
    {
        $response = $this->get('/communaute');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_create_thread(): void
    {
        $response = $this->get('/communaute/creer');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_create_thread(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user)->get('/communaute/creer');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_thread(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user)->post('/communaute/creer', [
            'title'    => 'Test de discussion agroécologie',
            'category' => 'pratiques',
            'body'     => 'Contenu de la discussion de test avec suffisamment de texte.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('forum_threads', ['title' => 'Test de discussion agroécologie']);
    }

    public function test_thread_view_count_increments(): void
    {
        $thread = ForumThread::factory()->create(['views' => 0]);

        $this->get("/communaute/{$thread->category}/{$thread->slug}");
        $this->assertDatabaseHas('forum_threads', ['id' => $thread->id, 'views' => 1]);
    }
}
