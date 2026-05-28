<?php

namespace Tests\Feature;

use App\Mail\NewUserRegistrationMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'valider-inscriptions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'administrer-utilisateurs', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'moderateur', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'validateur_inscriptions', 'guard_name' => 'web'])
            ->syncPermissions(['valider-inscriptions']);
        Role::firstOrCreate(['name' => 'contributeur', 'guard_name' => 'web']);
    }

    public function test_registration_sends_alert_to_validator(): void
    {
        Mail::fake();

        $validator = User::factory()->withPersonalTeam()->create([
            'approval_status' => 'approved',
            'email_verified_at' => now(),
        ]);
        $validator->assignRole('validateur_inscriptions');

        $applicant = User::factory()->withPersonalTeam()->create([
            'approval_status' => 'pending',
        ]);

        app(\App\Services\RegistrationNotificationService::class)->notifyNewRegistration($applicant);

        Mail::assertSent(NewUserRegistrationMail::class, function (NewUserRegistrationMail $mail) use ($validator, $applicant) {
            return $mail->hasTo($validator->email) && $mail->user->is($applicant);
        });
    }

    public function test_validator_can_access_pending_page_but_not_dashboard(): void
    {
        $validator = User::factory()->withPersonalTeam()->create([
            'approval_status' => 'approved',
            'email_verified_at' => now(),
        ]);
        $validator->assignRole('validateur_inscriptions');

        $this->actingAs($validator)->get('/admin/utilisateurs-en-attente')->assertOk();
        $this->actingAs($validator)->get('/admin')->assertForbidden();
    }
}
