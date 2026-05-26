<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'contributeur', 'guard_name' => 'web']);
    }

    public function test_guest_cannot_access_admin(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect();
    }

    public function test_regular_user_cannot_access_admin(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->withPersonalTeam()->create();
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_admin_dashboard_shows_stats(): void
    {
        $admin = User::factory()->withPersonalTeam()->create();
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertSee('Tableau de bord');
        $response->assertSee('Utilisateurs');
    }
}
