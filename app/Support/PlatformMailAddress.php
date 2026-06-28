<?php

namespace App\Support;

use Symfony\Component\Mime\Address;

final class PlatformMailAddress
{
    public static function from(): Address
    {
        return new Address(
            (string) config('mail.from.address'),
            (string) config('mail.from.name', 'Plateforme agroécologique'),
        );
    }

    public static function replyTo(): ?Address
    {
        $address = config('mail.reply_to.address') ?: config('mail.from.address');

        if (! $address) {
            return null;
        }

        return new Address(
            (string) $address,
            (string) config('mail.reply_to.name', config('mail.from.name')),
        );
    }

    public static function applyTo(\Symfony\Component\Mime\Email $message): void
    {
        $message->from(self::from());

        if ($reply = self::replyTo()) {
            $message->replyTo($reply);
        }
    }
}
