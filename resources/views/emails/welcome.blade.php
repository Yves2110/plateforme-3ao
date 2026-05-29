<x-mail::message>
# Bienvenue sur la Plateforme 3AO !

Bonjour **{{ $user->name }}**,

Nous sommes ravis de vous accueillir sur la **Plateforme Collaborative pour l'Agroécologie** de l'Alliance 3AO   le hub de référence pour les acteurs de l'agroécologie en Afrique de l'Ouest.

Voici ce que vous pouvez faire dès maintenant :

<x-mail::table>
| Fonctionnalité | Description |
|:---|:---|
| 📚 Bibliothèque | Accédez à des centaines de ressources documentaires |
| 💬 Forum | Échangez avec la communauté agroécologique |
| 🗺️ Carte | Découvrez le réseau des acteurs de la région |
| 📅 Événements | Inscrivez-vous aux prochains événements |
</x-mail::table>

<x-mail::button :url="url('/')" color="success">
Accéder à la plateforme
</x-mail::button>

Si vous avez des questions, notre équipe est disponible à **contact3ao@gmail.com**.

Bonne exploration !

**L'équipe 3AO   Alliance pour l'Agroécologie en Afrique de l'Ouest**
</x-mail::message>
