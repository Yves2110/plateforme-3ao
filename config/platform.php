<?php

return [
    /*
    | E-mail du propriétaire de la plateforme (compte fondateur, invisible aux autres admins).
    | Doit correspondre au compte marqué is_platform_owner en base.
    */
    'owner_email' => env('PLATFORM_OWNER_EMAIL', 'ismaelyveskabore@gmail.com'),
];
