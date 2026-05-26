<?php

namespace Tests\Feature;

use App\Models\Actor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarteTest extends TestCase
{
    use RefreshDatabase;

    public function test_carte_index_contains_map_and_vite_assets(): void
    {
        $response = $this->get('/carte');

        $response->assertStatus(200);
        $response->assertSee('id="map"', false);
        $response->assertSee('actorMap', false);
        $response->assertSee('carte-interactive.js', false);
        $response->assertSee('vendor/leaflet/leaflet.js', false);
        $response->assertSee('leaflet.markercluster.js', false);
    }

    public function test_carte_acteurs_returns_validated_actors_with_coordinates(): void
    {
        Actor::create([
            'name' => 'Test OP',
            'slug' => 'test-op',
            'type' => 'ONG',
            'country' => 'Burkina Faso',
            'lat' => 12.37,
            'lng' => -1.52,
            'is_validated' => true,
        ]);

        Actor::create([
            'name' => 'Sans GPS',
            'slug' => 'sans-gps',
            'type' => 'ONG',
            'country' => 'Mali',
            'is_validated' => true,
        ]);

        $response = $this->getJson('/carte/acteurs');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Test OP', 'lat' => 12.37]);
    }

    public function test_carte_acteur_page_loads(): void
    {
        $actor = Actor::create([
            'name' => 'ROPPA Test',
            'slug' => 'roppa-test',
            'type' => 'Réseau OP',
            'country' => 'Burkina Faso',
            'lat' => 12.37,
            'lng' => -1.52,
            'is_validated' => true,
        ]);

        $this->get(route('carte.acteur', $actor->slug))->assertStatus(200);
    }

    public function test_csp_allows_osm_tiles(): void
    {
        $response = $this->get('/carte');

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp);
        $this->assertStringContainsString('tile.openstreetmap.org', $csp);
        $this->assertStringContainsString('cdnjs.cloudflare.com', $csp);
    }

    public function test_carte_network_page_includes_graph_scripts(): void
    {
        Actor::create([
            'name' => 'Acteur Réseau',
            'slug' => 'acteur-reseau',
            'type' => 'ONG',
            'country' => 'Burkina Faso',
            'is_validated' => true,
        ]);

        $response = $this->get('/carte/reseau');

        $response->assertStatus(200);
        $response->assertSee('id="network-graph"', false);
        $response->assertSee('id="graph-nodes-data"', false);
        $response->assertSee('network-graph.js', false);
        $response->assertSee('js/vendor/d3.min.js', false);
        $response->assertSee('acteur-reseau', false);
    }
}
