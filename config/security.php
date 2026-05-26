<?php

return [

    /*
    | Domaines d'e-mails jetables / temporaires (règle §10).
    | Compléter selon vos besoins ou brancher une API (Kickbox, etc.).
    */
    'disposable_email_domains' => [
        'yopmail.com',
        'yopmail.fr',
        'guerrillamail.com',
        'guerrillamail.net',
        'guerrillamail.org',
        'tempmail.com',
        'temp-mail.org',
        'mail-temp.com',
        '10minutemail.com',
        'throwaway.email',
        'mailinator.com',
        'sharklasers.com',
        'getnada.com',
        'dispostable.com',
        'fakeinbox.com',
        'trashmail.com',
    ],

    'form_min_seconds' => (int) env('SECURITY_FORM_MIN_SECONDS', 2),

    'require_2fa_roles' => [
        'super_admin',
        'moderateur',
    ],

    'require_admin_2fa' => env(
        'SECURITY_REQUIRE_ADMIN_2FA',
        env('APP_ENV') === 'production'
    ),

];
