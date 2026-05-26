<x-mail::message>
# Nouvelle réponse dans votre discussion

Bonjour **{{ $recipient->name }}**,

Une nouvelle réponse a été publiée dans le fil de discussion auquel vous participez :

<x-mail::panel>
**{{ $thread->title }}**

*{{ $reply->user->name }}* a répondu :

> {{ Str::limit(strip_tags($reply->body), 200) }}
</x-mail::panel>

<x-mail::button :url="route('thread.show', [$thread->category, $thread->slug])" color="success">
Voir la réponse complète
</x-mail::button>

Pour ne plus recevoir ces notifications, vous pouvez gérer vos préférences depuis votre profil.

Cordialement,
**L'équipe 3AO**
{{ config('app.name') }}
</x-mail::message>
