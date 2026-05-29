<?php

namespace Tests\Feature;

use App\Models\Formation;
use App\Models\FormationCertificate;
use App\Models\FormationEnrollment;
use App\Models\FormationLesson;
use App\Models\FormationProgress;
use App\Models\User;
use App\Services\FormationCompletionService;
use App\Services\FormationEnrollmentService;
use Database\Seeders\FormationLmsDemoSeeder;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormationLmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesPermissionsSeeder::class);
        $this->seed(FormationLmsDemoSeeder::class);
    }

    public function test_user_can_enroll_in_free_formation_with_lms_content(): void
    {
        $formation = Formation::where('slug', 'initiation-gestion-participative')->firstOrFail();
        $user = User::factory()->create(['approved_at' => now(), 'email_verified_at' => now()]);

        $entryUrl = app(FormationEnrollmentService::class)->courseEntryUrl($formation, $user);

        $response = $this->actingAs($user)->post(route('formation.enroll', $formation->slug));

        $response->assertRedirect($entryUrl);
        $this->assertDatabaseHas('formation_enrollments', [
            'user_id' => $user->id,
            'formation_id' => $formation->id,
            'status' => FormationEnrollment::STATUS_ACTIVE,
        ]);
    }

    public function test_enrolled_user_can_access_lesson_and_complete_it(): void
    {
        $formation = Formation::where('slug', 'initiation-gestion-participative')->firstOrFail();
        $lessons = $this->orderedLessons($formation);
        $lesson = $lessons->firstOrFail();
        $nextLesson = $lessons->get(1);
        $this->assertNotNull($nextLesson);

        $user = User::factory()->create(['approved_at' => now(), 'email_verified_at' => now()]);
        FormationEnrollment::create([
            'user_id' => $user->id,
            'formation_id' => $formation->id,
            'status' => FormationEnrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('learning.lesson', [$formation->slug, $lesson]))
            ->assertOk()
            ->assertSee($lesson->title);

        $this->actingAs($user)
            ->postJson(route('learning.complete', [$formation->slug, $lesson]), ['time_spent' => 120])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'redirect_url' => route($nextLesson->learningRouteName(), [$formation->slug, $nextLesson]),
                'next_lesson_title' => $nextLesson->title,
            ]);

        $this->assertDatabaseHas('formation_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    /** @return \Illuminate\Support\Collection<int, FormationLesson> */
    private function orderedLessons(Formation $formation)
    {
        return FormationLesson::query()
            ->where('formation_lessons.is_published', true)
            ->whereHas('module', fn ($q) => $q->where('formation_id', $formation->id)->where('is_published', true))
            ->join('formation_modules', 'formation_modules.id', '=', 'formation_lessons.module_id')
            ->orderBy('formation_modules.order')
            ->orderBy('formation_lessons.order')
            ->select('formation_lessons.*')
            ->get();
    }

    public function test_admin_can_export_formation_enrollments_csv(): void
    {
        $formation = Formation::where('slug', 'initiation-gestion-participative')->firstOrFail();
        $admin = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->firstOrFail();

        $learner = User::factory()->create([
            'approved_at' => now(),
            'email_verified_at' => now(),
            'name' => 'Apprenant Export',
            'email' => 'export-learner@example.org',
            'organization' => 'Coop Test',
            'country' => 'Burkina Faso',
        ]);

        FormationEnrollment::create([
            'user_id' => $learner->id,
            'formation_id' => $formation->id,
            'status' => FormationEnrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.formations.enrollments.export', $formation));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $content = $response->streamedContent();
        $this->assertStringContainsString('Nom complet', $content);
        $this->assertStringContainsString('export-learner@example.org', $content);
        $this->assertStringContainsString('Initiation à la gestion participative', $content);
    }

    public function test_external_registration_link_only_after_platform_enrollment(): void
    {
        $author = User::query()->firstOrFail();
        $formation = Formation::create([
            'title' => 'Webinaire test',
            'slug' => 'webinaire-test-zoom',
            'type' => 'webinaire',
            'organizer' => '3AO',
            'is_online' => true,
            'language' => 'fr',
            'registration_url' => 'https://zoom.us/register-test',
            'is_validated' => true,
            'user_id' => $author->id,
        ]);

        $user = User::factory()->create(['approved_at' => now(), 'email_verified_at' => now()]);

        $this->actingAs($user)
            ->get(route('formation.show', $formation->slug))
            ->assertOk()
            ->assertDontSee('zoom.us/register-test', false);

        $this->actingAs($user)
            ->post(route('formation.enroll', $formation->slug))
            ->assertRedirect('https://zoom.us/register-test');

        $this->actingAs($user)
            ->get(route('formation.show', $formation->slug))
            ->assertOk()
            ->assertSee('zoom.us/register-test', false);
    }

    public function test_login_redirect_auto_enrolls_and_starts_course(): void
    {
        $formation = Formation::where('slug', 'initiation-gestion-participative')->firstOrFail();
        $user = User::factory()->create(['approved_at' => now(), 'email_verified_at' => now()]);
        $entryUrl = app(FormationEnrollmentService::class)->courseEntryUrl($formation, $user);

        $this->actingAs($user)
            ->get(route('formation.show', [$formation->slug, 'inscrire' => 1]))
            ->assertRedirect($entryUrl);

        $this->assertDatabaseHas('formation_enrollments', [
            'user_id' => $user->id,
            'formation_id' => $formation->id,
            'status' => FormationEnrollment::STATUS_ACTIVE,
        ]);
    }

    public function test_quiz_lesson_redirects_to_quiz_page(): void
    {
        $formation = Formation::where('slug', 'initiation-gestion-participative')->firstOrFail();
        $quizLesson = FormationLesson::whereHas('module', fn ($q) => $q->where('formation_id', $formation->id))
            ->where('type', 'quiz')
            ->firstOrFail();

        $user = User::factory()->create(['approved_at' => now(), 'email_verified_at' => now()]);
        FormationEnrollment::create([
            'user_id' => $user->id,
            'formation_id' => $formation->id,
            'status' => FormationEnrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('learning.lesson', [$formation->slug, $quizLesson]))
            ->assertRedirect(route('learning.quiz', [$formation->slug, $quizLesson]));
    }

    public function test_certificate_is_issued_when_all_lessons_are_completed(): void
    {
        $formation = Formation::where('slug', 'initiation-gestion-participative')->firstOrFail();
        $user = User::factory()->create([
            'approved_at' => now(),
            'email_verified_at' => now(),
            'organization' => 'Coopérative Test',
        ]);

        FormationEnrollment::create([
            'user_id' => $user->id,
            'formation_id' => $formation->id,
            'status' => FormationEnrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        $lessons = FormationLesson::whereHas('module', fn ($q) => $q->where('formation_id', $formation->id)->where('is_published', true))
            ->where('is_published', true)
            ->get();

        foreach ($lessons as $lesson) {
            FormationProgress::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'completed_at' => now(),
            ]);
        }

        $certificate = app(FormationCompletionService::class)->tryFinalize($user, $formation);

        $this->assertInstanceOf(FormationCertificate::class, $certificate);
        $this->assertSame($user->name, $certificate->learner_name);
        $this->assertSame($formation->title, $certificate->formation_title);
        $this->assertTrue($certificate->pdfExists());

        $this->actingAs($user)
            ->get(route('learning.certificate.download', $formation->slug))
            ->assertOk();
    }
}
