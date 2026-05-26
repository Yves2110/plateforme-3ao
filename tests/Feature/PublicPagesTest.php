<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_actualites_index_loads(): void
    {
        $this->get('/actualites')->assertStatus(200);
    }

    public function test_bibliotheque_index_loads(): void
    {
        $this->get('/bibliotheque')->assertStatus(200);
    }

    public function test_evenements_index_loads(): void
    {
        $this->get('/evenements')->assertStatus(200);
    }

    public function test_carte_index_loads(): void
    {
        $this->get('/carte')->assertStatus(200);
    }

    public function test_multimedia_index_loads(): void
    {
        $this->get('/multimedia')->assertStatus(200);
    }

    public function test_multimedia_video_filter_loads(): void
    {
        $this->get('/multimedia?type=video')->assertStatus(200);
    }

    public function test_multimedia_podcast_filter_loads(): void
    {
        $this->get('/multimedia?type=podcast')->assertStatus(200);
    }

    public function test_recherche_loads(): void
    {
        $this->get('/recherche?q=agroecologie')->assertStatus(200);
    }

    public function test_security_headers_present(): void
    {
        $response = $this->get('/');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_lang_switcher_sets_locale(): void
    {
        $this->get('/?lang=en');
        $this->assertEquals('en', session('locale'));
    }

    public function test_lang_switcher_rejects_invalid_locale(): void
    {
        $this->get('/?lang=de');
        $this->assertNotEquals('de', session('locale'));
    }

    public function test_default_locale_is_french_on_home(): void
    {
        $response = $this->withSession([])->get('/');
        $response->assertStatus(200);
        $response->assertSee('Formation', false);
        $response->assertSee('Alliance pour l\'Agroécologie en Afrique de l\'Ouest', false);
        $response->assertSee('lang="fr"', false);
    }

    public function test_english_locale_shows_translated_nav(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/');
        $response->assertStatus(200);
        $response->assertSee('Training', false);
        $response->assertSee('Alliance for Agroecology in West Africa', false);
        $response->assertDontSee('nav.training', false);
    }

    public function test_french_nav_keys_not_leaked_as_literals(): void
    {
        $response = $this->withSession(['locale' => 'fr'])->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('nav.training', false);
        $response->assertDontSee('nav.about', false);
        $response->assertSee('Formation', false);
        $response->assertSee('À propos', false);
    }
}
