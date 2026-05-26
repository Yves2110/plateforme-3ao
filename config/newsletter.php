<?php

return [

    /*
    | Exécuter l'envoi de campagne immédiatement (sans worker queue).
    | Les e-mails individuels peuvent encore être mis en file si QUEUE_CONNECTION != sync.
    */
    'dispatch_sync' => env('NEWSLETTER_DISPATCH_SYNC', true),

    /*
    | Envoyer chaque e-mail abonné immédiatement (SMTP).
    | Si false et QUEUE_CONNECTION=database, il faut lancer php artisan queue:work.
    */
    'send_mails_sync' => env('NEWSLETTER_SEND_MAILS_SYNC', true),

    /*
    | Vérifier les campagnes programmées à chaque visite de la page admin newsletter.
    */
    'process_on_admin_visit' => env('NEWSLETTER_PROCESS_ON_ADMIN_VISIT', true),

    /*
    | Minutes avant de considérer une campagne « sending » comme bloquée.
    */
    'stuck_minutes' => (int) env('NEWSLETTER_STUCK_MINUTES', 15),

];
