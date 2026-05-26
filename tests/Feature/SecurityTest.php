<?php

namespace Tests\Feature;

use App\Rules\NotDisposableEmail;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_disposable_email_is_rejected(): void
    {
        $validator = Validator::make(
            ['email' => 'test@yopmail.com'],
            ['email' => [new NotDisposableEmail]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_honeypot_blocks_newsletter_submission(): void
    {
        $response = $this->post(route('newsletter.subscribe'), [
            'email'         => 'valid@gmail.com',
            'website'       => 'http://spam.test',
            '_form_started' => time() - 5,
        ]);

        $response->assertStatus(422);
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get(route('home'));

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertNotEmpty($response->headers->get('Content-Security-Policy'));
    }

    public function test_admin_routes_require_authentication(): void
    {
        $this->seed(RolesPermissionsSeeder::class);

        $this->get(route('admin.newsletter.index'))->assertRedirect();
    }
}
