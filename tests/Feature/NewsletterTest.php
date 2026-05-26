<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesPermissionsSeeder::class);
    }

    public function test_public_can_subscribe_to_newsletter(): void
    {
        $response = $this->post(route('newsletter.subscribe'), [
            'email'          => 'abonne@gmail.com',
            '_form_started'  => time() - 5,
            'website'        => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('newsletter_status', 'subscribed');

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email'     => 'abonne@gmail.com',
            'is_active' => true,
        ]);
    }

    public function test_subscriber_can_unsubscribe_with_token(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email'         => 'leave@example.org',
            'is_active'     => true,
            'subscribed_at' => now(),
        ]);

        $response = $this->get(route('newsletter.unsubscribe', $subscriber->token));

        $response->assertOk();
        $this->assertFalse($subscriber->fresh()->is_active);
    }

    public function test_admin_can_access_newsletter_back_office(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->get(route('admin.newsletter.index'))
            ->assertOk();
    }

    public function test_guest_cannot_access_newsletter_admin(): void
    {
        $this->get(route('admin.newsletter.index'))->assertRedirect();
    }

    public function test_admin_can_export_subscribers_csv(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        NewsletterSubscriber::create([
            'email'         => 'export@example.org',
            'is_active'     => true,
            'subscribed_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.newsletter.subscribers.export'));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('export@example.org', $response->streamedContent());
    }
}
