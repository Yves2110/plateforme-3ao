<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterCampaign extends Model
{
    public const string STATUS_DRAFT = 'draft';

    public const string STATUS_SCHEDULED = 'scheduled';

    public const string STATUS_SENDING = 'sending';

    public const string STATUS_SENT = 'sent';

    public const string STATUS_CANCELLED = 'cancelled';

    public const string STATUS_FAILED = 'failed';

    protected $fillable = [
        'subject',
        'intro_html',
        'status',
        'scheduled_at',
        'sent_at',
        'recipients_count',
        'sent_success_count',
        'sent_failed_count',
        'last_error',
        'user_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(NewsletterCampaignItem::class)->orderBy('sort_order');
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SCHEDULED], true);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT     => 'Brouillon',
            self::STATUS_SCHEDULED => 'Programmée',
            self::STATUS_SENDING   => 'Envoi en cours',
            self::STATUS_SENT      => 'Envoyée',
            self::STATUS_CANCELLED => 'Annulée',
            self::STATUS_FAILED    => 'Échec',
            default                => $this->status,
        };
    }

    public function sendSummary(): ?string
    {
        if (! in_array($this->status, [self::STATUS_SENT, self::STATUS_FAILED, self::STATUS_SENDING], true)) {
            return null;
        }

        if ($this->status === self::STATUS_SENDING) {
            return 'Envoi en cours…';
        }

        return sprintf(
            'Succès : %d — Échec : %d',
            $this->sent_success_count,
            $this->sent_failed_count
        );
    }
}
