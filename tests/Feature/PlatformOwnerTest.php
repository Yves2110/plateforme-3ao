<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PlatformOwnerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'contributeur', 'guard_name' => 'web']);
    }

    public function test_platform_owner_is_hidden_from_other_super_admins(): void
    {
        $owner = User::factory()->withPersonalTeam()->create([
            'approval_status' => 'approved',
            'email_verified_at' => now(),
            'is_platform_owner' => true,
        ]);
        $owner->assignRole('super_admin');

        $otherAdmin = User::factory()->withPersonalTeam()->create([
            'approval_status' => 'approved',
            'email_verified_at' => now(),
        ]);
        $otherAdmin->assignRole('super_admin');

        $this->actingAs($otherAdmin)
            ->get(route('admin.utilisateurs.index'))
            ->assertOk()
            ->assertDontSee($owner->email)
            ->assertSee($otherAdmin->email);
    }

    public function test_only_platform_owner_can_delete_super_admin(): void
    {
        $owner = User::factory()->withPersonalTeam()->create([
            'approval_status' => 'approved',
            'email_verified_at' => now(),
            'is_platform_owner' => true,
        ]);
        $owner->assignRole('super_admin');

        $otherSuper = User::factory()->withPersonalTeam()->create([
            'approval_status' => 'approved',
            'email_verified_at' => now(),
        ]);
        $otherSuper->assignRole('super_admin');

        $this->actingAs($otherSuper)
            ->delete(route('admin.utilisateurs.destroy', $owner))
            ->assertNotFound();

        $this->actingAs($owner)
            ->delete(route('admin.utilisateurs.destroy', $otherSuper))
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $otherSuper->id]);
    }
}
