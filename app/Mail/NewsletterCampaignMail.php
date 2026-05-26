<?php

namespace App\Mail;

use App\Mail\Concerns\UsesPlatformSender;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMail extends Mailable
{
    use Queueable, SerializesModels, UsesPlatformSender;

    public function __construct(
        public readonly NewsletterCampaign $campaign,
        public readonly NewsletterSubscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return $this->platformEnvelope($this->campaign->subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter-campaign',
            with: [
                'campaign'       => $this->campaign,
                'unsubscribeUrl' => $this->subscriber->unsubscribeUrl(),
                'siteUrl'        => url('/'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
