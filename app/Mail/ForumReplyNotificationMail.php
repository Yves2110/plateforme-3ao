<?php

namespace App\Mail;

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForumReplyNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly ForumThread $thread,
        public readonly ForumReply  $reply,
        public readonly User        $recipient,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle réponse dans : ' . $this->thread->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.forum-reply',
            with: [
                'thread'    => $this->thread,
                'reply'     => $this->reply,
                'recipient' => $this->recipient,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
