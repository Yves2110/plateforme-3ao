<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventRegistrationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Event $event,
        public readonly User  $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation d\'inscription — ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event-registration',
            with: [
                'event' => $this->event,
                'user'  => $this->user,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
