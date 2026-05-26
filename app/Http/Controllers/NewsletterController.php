<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Rules\NotDisposableEmail;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc,dns', 'max:255', new NotDisposableEmail],
            'name'  => 'nullable|string|max:120',
        ]);

        $email = strtolower(trim($data['email']));

        $subscriber = NewsletterSubscriber::query()->where('email', $email)->first();

        if ($subscriber) {
            if ($subscriber->is_active) {
                return back()->with('newsletter_status', 'already');
            }

            $subscriber->update([
                'is_active'       => true,
                'unsubscribed_at' => null,
                'subscribed_at'   => now(),
                'name'            => $data['name'] ?? $subscriber->name,
            ]);

            return back()->with('newsletter_status', 'resubscribed');
        }

        NewsletterSubscriber::create([
            'email'         => $email,
            'name'          => $data['name'] ?? null,
            'source'        => 'footer',
            'ip'            => $request->ip(),
            'is_active'     => true,
            'subscribed_at' => now(),
        ]);

        return back()->with('newsletter_status', 'subscribed');
    }

    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::query()->where('token', $token)->firstOrFail();

        if ($subscriber->is_active) {
            $subscriber->update([
                'is_active'       => false,
                'unsubscribed_at' => now(),
            ]);
        }

        return view('newsletter.unsubscribed', compact('subscriber'));
    }
}
