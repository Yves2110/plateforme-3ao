<?php

namespace App\Mail\Concerns;

use App\Support\PlatformMailAddress;
use Illuminate\Mail\Mailables\Envelope;

trait UsesPlatformSender
{
    protected function platformEnvelope(string $subject): Envelope
    {
        $from = PlatformMailAddress::from();

        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($from->getAddress(), $from->getName()),
            subject: $subject,
        );
    }
}
