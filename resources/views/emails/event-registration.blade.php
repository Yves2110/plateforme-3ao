<x-mail::message>
# Confirmation d'inscription

Bonjour **{{ $user->name }}**,

Votre inscription à l'événement suivant a bien été enregistrée :

<x-mail::panel>
**{{ $event->title }}**

📅 {{ $event->start_date->translatedFormat('d F Y') }}
@if(!$event->is_online)
📍 {{ $event->location }}{{ $event->country ? ', '.$event->country : '' }}
@else
🌐 Événement en ligne
@endif
</x-mail::panel>

@if($event->description)
{{ $event->description }}
@endif

<x-mail::button :url="route('evenements.show', $event->slug)" color="success">
Voir les détails de l'événement
</x-mail::button>

Nous avons hâte de vous y retrouver !

Cordialement,
**L'équipe 3AO**
{{ config('app.name') }}
</x-mail::message>
