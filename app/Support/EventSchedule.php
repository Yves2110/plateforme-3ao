<?php

namespace App\Support;

use App\Models\Event;
use Carbon\Carbon;

final readonly class EventSchedule
{
    public const string STATUS_ACTIVE = 'active';

    public const string STATUS_SOON = 'soon';

    public const string STATUS_EXPIRED = 'expired';

    public static function for(Event $event): self
    {
        return new self($event);
    }

    public function __construct(
        protected Event $event
    ) {}

    public function startsAt(): Carbon
    {
        return $this->event->start_date->copy()->startOfDay();
    }

    public function endsAt(): Carbon
    {
        $end = $this->event->end_date ?? $this->event->start_date;

        return $end->copy()->endOfDay();
    }

    public function isExpired(): bool
    {
        return $this->endsAt()->lt(now()->startOfDay());
    }

    /** Début dans les 7 prochains jours (événement pas encore commencé). */
    public function isSoon(): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        $today = now()->startOfDay();
        $start = $this->startsAt();

        return $start->gte($today) && $start->lte($today->copy()->addDays(7));
    }

    public function status(): string
    {
        if ($this->isExpired()) {
            return self::STATUS_EXPIRED;
        }

        if ($this->isSoon()) {
            return self::STATUS_SOON;
        }

        return self::STATUS_ACTIVE;
    }

    public function daysUntilStart(): ?int
    {
        if ($this->isExpired() || $this->startsAt()->lt(now()->startOfDay())) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->startsAt(), false);
    }

    public function label(): ?string
    {
        return match ($this->status()) {
            self::STATUS_EXPIRED => __('evenements.status_expired'),
            self::STATUS_SOON => match ($this->daysUntilStart()) {
                0 => __('evenements.status_today'),
                1 => __('evenements.status_tomorrow'),
                default => __('evenements.status_in_days', ['days' => $this->daysUntilStart()]),
            },
            default => null,
        };
    }

    public function heroImageUrl(): string
    {
        if ($this->event->thumbnail) {
            return asset('storage/'.$this->event->thumbnail);
        }

        return '';
    }
}
