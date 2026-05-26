SECURITY_RULES.md
# SECURITY_RULES.md

# Politique de sécurité – Application Laravel

Version : 1.0  
Application : Laravel  
Objectif : sécuriser une application générée rapidement (IA / vibe coding) contre les vulnérabilités courantes et les mauvaises pratiques.

---

# PRINCIPE FONDAMENTAL

Aucune donnée provenant du client n'est considérée comme fiable.

Toute validation effectuée côté navigateur doit être répétée côté serveur.

Le frontend, JavaScript, HTML et les appels API peuvent être modifiés par un attaquant.

Appliquer systématiquement :

- Validation
- Authentification
- Autorisation
- Journalisation
- Limitation des abus

---

# PRIORITÉ CRITIQUE

## 1. Validation obligatoire

Ne jamais utiliser directement :

```php
$request->all()
```

Toujours utiliser :

```php
$request->validated()
```

Créer une classe FormRequest :

```bash
php artisan make:request UserRequest
```

Exemple :

```php
public function store(UserRequest $request)
{
    $data = $request->validated();

    User::create($data);
}
```

Exemple de règles :

```php
public function rules()
{
    return [

        'name'=>'required|string|min:2|max:50',

        'email'=>'required|email:rfc,dns|max:100',

        'phone'=>[
            'nullable',
            'regex:/^\+?[0-9]{8,15}$/'
        ],

        'password'=>[
            'required',
            Password::min(12)
            ->mixedCase()
            ->letters()
            ->numbers()
            ->symbols()
        ]
    ];
}
```

Interdiction :

```php
User::create($request->all());
```

---

## 2. Protection contre Mass Assignment

Tous les modèles doivent définir :

```php
protected $fillable=[
'name',
'email',
'password'
];
```

ou :

```php
protected $guarded=[
'id',
'role',
'is_admin'
];
```

Interdiction absolue :

```php
protected $guarded=[];
```

Risque :

```json
{
"is_admin":true
}
```

---

## 3. Protection SQL Injection

Interdit :

```php
$query="
SELECT * FROM users
WHERE email='$email'
";
```

Interdit :

```php
DB::raw($request->input);
```

Toujours utiliser :

```php
User::where(
'email',
$email
);
```

ou :

```php
DB::select(
'SELECT * FROM users WHERE email=?',
[$email]
);
```

---

## 4. Protection XSS

Ne jamais afficher :

```blade
{!! $content !!}
```

Utiliser :

```blade
{{ $content }}
```

Autoriser HTML uniquement après nettoyage :

- DOMPurify
- HTMLPurifier

Activer CSP :

```http
Content-Security-Policy:
default-src 'self';
script-src 'self';
object-src 'none';
base-uri 'self';
frame-ancestors 'none';
```

Interdire :

```http
unsafe-inline
unsafe-eval
```

---

## 5. Protection CSRF

Tous les formulaires :

```blade
@csrf
```

Interdiction de désactiver CSRF sans justification.

Pour SPA/API stateful :

Utiliser Sanctum.

---

## 6. Sécurisation des routes

Toutes routes sensibles :

```php
Route::middleware([
'auth',
'verified',
'throttle:5,1'
])->group(function(){

});
```

Ajouter si nécessaire :

```php
role:admin
```

ou :

```php
can:update,user
```

Aucune route admin publique.

---

## 7. Authentification

Exiger :

- minimum 12 caractères
- majuscule
- minuscule
- chiffre
- symbole

Utiliser :

```php
Hash::make()
```

Interdit :

```php
md5()
sha1()
```

---

## 8. Authentification à deux facteurs

Obligatoire pour :

- administrateurs
- comptes sensibles

Utiliser :

- Laravel Fortify
- Google Authenticator
- TOTP

Éviter SMS si possible.

---

## 9. Vérification email obligatoire

Utiliser :

```php
MustVerifyEmail
```

Flux :

1 inscription

2 email envoyé

3 lien unique

4 expiration

5 activation

---

## 10. Bloquer faux emails

Refuser :

- yopmail
- guerrillamail
- tempmail
- mail-temp

Vérifier :

- DNS
- MX

API possibles :

- Kickbox
- ZeroBounce
- NeverBounce

---

## 11. Limitation brute force

Connexion :

```php
throttle:5,1
```

Ajouter :

- blocage IP
- blocage utilisateur
- CAPTCHA après plusieurs échecs

Exemple :

5 essais = 5 min

10 essais = 1 heure

---

## 12. Protection anti-bot formulaires

Ajouter :

Honeypot :

```html
<input name="website" hidden>
```

Ajouter :

- Cloudflare Turnstile
- CAPTCHA
- délai minimal

Interdire soumission :

moins de 2 secondes

---

## 13. Sécurisation des sessions

Dans :

config/session.php

```php
'http_only'=>true,

'same_site'=>'strict',

'secure'=>env('APP_ENV')==='production'
```

Régénérer session :

```php
$request->session()
->regenerate();
```

Après :

- connexion
- changement mot de passe
- changement privilèges

Expiration :

15–30 minutes inactives

---

## 14. Configuration production

Dans .env :

```env
APP_ENV=production

APP_DEBUG=false
```

Interdiction :

```env
APP_DEBUG=true
```

Risque :

- fuite SQL
- stack traces
- chemins serveur
- variables sensibles

---

## 15. APP_KEY obligatoire

Exécuter :

```bash
php artisan key:generate
```

Ne jamais laisser vide.

---

## 16. API sécurisées

Utiliser :

- Sanctum
- Passport

Exiger :

- expiration token
- permissions
- rate limiting

Exemple :

```php
Route::middleware([
'auth:sanctum',
'throttle:60,1'
]);
```

---

## 17. Upload sécurisé

Autoriser :

- jpg
- jpeg
- png
- pdf
- docx

Interdire :

- php
- exe
- js
- sh
- bat

Validation :

```php
'image'=>'mimes:jpg,jpeg,png|max:2048'
```

Renommer :

nom aléatoire

Stocker :

```txt
storage/app
```

Jamais :

```txt
public/
```

Scanner fichiers si possible.

Exemple :

ClamAV

---

## 18. Headers sécurité

Configurer :

```http
Strict-Transport-Security:
max-age=31536000;includeSubDomains

X-Frame-Options:DENY

X-Content-Type-Options:nosniff

Referrer-Policy:no-referrer

Permissions-Policy:
camera=(),
microphone=(),
geolocation=()

Content-Security-Policy:
default-src 'self'
```

---

## 19. Logs et surveillance

Journaliser :

- connexions
- erreurs
- suppression
- actions admin
- activités API

Ne jamais enregistrer :

- mots de passe
- tokens
- clés API

Utiliser :

- Sentry
- Wazuh
- Fail2Ban
- CrowdSec

---

## 20. Secrets

Ne jamais commiter :

```txt
.env
API_KEY
JWT_SECRET
DB_PASSWORD
```

Ajouter :

```gitignore
.env
.env.*
```

Utiliser :

- variables environnement
- Secret Manager
- Vault

Rotation recommandée :

90 jours

---

## 21. Dépendances

Avant chaque mise en production :

```bash
composer audit
npm audit
```

Ajouter :

```bash
composer update
npm update
```

Utiliser :

- Dependabot
- Snyk

Interdire bibliothèques abandonnées.

---

## 22. Audit obligatoire avant déploiement

Vérifier :

- routes/web.php
- routes/api.php
- .env
- modèles
- middleware
- policies
- gates
- uploads
- jobs
- queues
- DB::raw()
- {!! !!}
- request->all()

---

# RÈGLE FINALE

Une fonctionnalité sans :

- validation
- authentification
- autorisation
- journalisation

est considérée comme non sécurisée.

Si un contrôle existe côté navigateur uniquement, il est considéré comme inexistant.

Sécurité = couches multiples + surveillance + validation serveur.