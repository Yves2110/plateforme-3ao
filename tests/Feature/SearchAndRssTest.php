<?php

namespace Tests\Feature;

use App\Models\Actor;
use App\Models\RssSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Models\User;

class SearchAndRssTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_includes_actors(): void
    {
        Actor::create([
            'name' => 'ROPPA Burkina',
            'slug' => 'roppa-burkina',
            'type' => 'Réseau OP',
            'country' => 'Burkina Faso',
            'is_validated' => true,
        ]);

        $this->get('/recherche?q=ROPPA')
            ->assertStatus(200)
            ->assertSee('ROPPA Burkina');
    }

    public function test_search_suggest_returns_json(): void
    {
        Actor::create([
            'name' => 'CNOP Mali',
            'slug' => 'cnop-mali',
            'type' => 'OP',
            'is_validated' => true,
        ]);

        $this->getJson('/recherche/suggest?q=CNOP')
            ->assertStatus(200)
            ->assertJson(['CNOP Mali']);
    }

    public function test_admin_rss_page_requires_permission(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::firstOrCreate(['name' => 'gerer-rss', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web'])->givePermissionTo('gerer-rss');

        $admin = User::factory()->withPersonalTeam()->create(['email_verified_at' => now()]);
        $admin->assignRole('super_admin');

        $this->actingAs($admin)->get('/admin/rss')->assertStatus(200);

        RssSource::create(['name' => 'Test', 'url' => 'https://example.com/feed.xml']);
        $this->actingAs($admin)->get('/admin/rss')->assertSee('Test');
    }
}
