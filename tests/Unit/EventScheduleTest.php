<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Support\EventSchedule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function makeEvent(array $attrs): Event
    {
        return Event::create(array_merge([
            'title'        => 'Test',
            'slug'         => 'test-'.uniqid(),
            'type'         => 'Forum',
            'start_date'   => now(),
            'is_validated' => true,
        ], $attrs));
    }

    public function test_expired_when_end_date_passed(): void
    {
        Carbon::setTestNow('2026-05-28 12:00:00');

        $event = $this->makeEvent([
            'slug'       => 'expired-event',
            'start_date' => '2026-05-20',
            'end_date'   => '2026-05-25',
        ]);

        $this->assertSame(EventSchedule::STATUS_EXPIRED, $event->schedule()->status());
    }

    public function test_soon_within_seven_days(): void
    {
        Carbon::setTestNow('2026-05-28 12:00:00');

        $event = $this->makeEvent([
            'slug'       => 'soon-event',
            'start_date' => '2026-06-02',
        ]);

        $this->assertSame(EventSchedule::STATUS_SOON, $event->schedule()->status());
        $this->assertSame(5, $event->schedule()->daysUntilStart());
    }

    public function test_active_when_far_future(): void
    {
        Carbon::setTestNow('2026-05-28 12:00:00');

        $event = $this->makeEvent([
            'slug'       => 'future-event',
            'start_date' => '2026-07-01',
        ]);

        $this->assertSame(EventSchedule::STATUS_ACTIVE, $event->schedule()->status());
        $this->assertNull($event->schedule()->label());
    }
}
