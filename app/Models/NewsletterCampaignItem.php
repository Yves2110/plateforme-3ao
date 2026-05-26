<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterCampaignItem extends Model
{
    protected $fillable = [
        'newsletter_campaign_id',
        'item_type',
        'item_id',
        'sort_order',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NewsletterCampaign::class, 'newsletter_campaign_id');
    }

    public function resolveModel(): Actualite|Event|null
    {
        return match ($this->item_type) {
            'actualite' => Actualite::query()
                ->where('is_published', true)
                ->find($this->item_id),
            'event' => Event::query()
                ->where('is_validated', true)
                ->find($this->item_id),
            default => null,
        };
    }
}
