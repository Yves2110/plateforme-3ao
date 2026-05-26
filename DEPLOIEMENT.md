# Guide de Déploiement — Plateforme 3AO

## Stack requise
- **PHP** 8.2+ avec extensions : `pdo_mysql`, `mbstring`, `intl`, `gd` ou `imagick`, `zip`, `xml`, `fileinfo`, `curl`
- **MySQL** 8.0+
- **Nginx** ou Apache
- **Node.js** 18+ (build Vite)
- **Composer** 2.x
- **Supervisor** (worker queue)

---

## 1. Cloner & Dépendances

```bash
git clone https://github.com/votre-org/plateforme-3ao.git /var/www/plateforme-3ao
cd /var/www/plateforme-3ao

composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

---

## 2. Configuration `.env`

```ini
APP_NAME="Plateforme 3AO"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.org

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plateforme_3ao
DB_USERNAME=3ao_user
DB_PASSWORD=VotreMotDePasseSecurise

# Stockage (laisser local ou configurer S3)
FILESYSTEM_DISK=public

# Queue (jobs DB)
QUEUE_CONNECTION=database

# Mail (exemple SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.votre-hebergeur.com
MAIL_PORT=587
MAIL_USERNAME=contact3ao@gmail.com
MAIL_PASSWORD=VotreMotDePasse
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=contact3ao@gmail.com
MAIL_FROM_NAME="Plateforme 3AO"

# Cache & Sessions
CACHE_STORE=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Sécurité
APP_KEY=    # généré ci-dessous
```

---

## 3. Initialisation

```bash
# Générer la clé d'application
php artisan key:generate

# Migrations + seeders
php artisan migrate --force
php artisan db:seed --force

# Lien symbolique storage
php artisan storage:link

# Optimisation production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache   # si Blade Icons utilisé
```

---

## 4. Permissions

```bash
chown -R www-data:www-data /var/www/plateforme-3ao
chmod -R 755 /var/www/plateforme-3ao/storage
chmod -R 755 /var/www/plateforme-3ao/bootstrap/cache
```

---

## 5. Nginx — Configuration VHost

```nginx
server {
    listen 443 ssl http2;
    server_name votre-domaine.org;

    root /var/www/plateforme-3ao/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/votre-domaine.org/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/votre-domaine.org/privkey.pem;

    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options SAMEORIGIN;
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy strict-origin-when-cross-origin;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location ~ /\.ht { deny all; }

    # Cache assets Vite
    location ~* \.(js|css|png|jpg|jpeg|webp|gif|ico|svg|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}

server {
    listen 80;
    server_name votre-domaine.org;
    return 301 https://$host$request_uri;
}
```

---

## 6. Queue Worker (Supervisor)

```ini
# /etc/supervisor/conf.d/3ao-worker.conf
[program:3ao-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/plateforme-3ao/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/3ao-worker.log
stopwaitsecs=3600
```

```bash
supervisorctl reread && supervisorctl update && supervisorctl start 3ao-worker:*
```

---

## 7. Tâches planifiées (Cron)

```cron
* * * * * www-data php /var/www/plateforme-3ao/artisan schedule:run >> /dev/null 2>&1
```

---

## 8. Certificat SSL (Let's Encrypt)

```bash
apt install certbot python3-certbot-nginx
certbot --nginx -d votre-domaine.org
```

---

## 9. Checklist pré-lancement

- [ ] `APP_DEBUG=false` dans `.env`
- [ ] Clé APP_KEY générée
- [ ] Storage link créé (`public/storage` → `storage/app/public`)
- [ ] Migrations exécutées + seeder admin
- [ ] Queue worker Supervisor actif
- [ ] Cron configuré
- [ ] SSL actif + redirect HTTP→HTTPS
- [ ] Tester `/sitemap.xml` et `/robots.txt`
- [ ] Tester envoi email (php artisan tinker → Mail::to(...)->send(...))
- [ ] Vérifier logs : `storage/logs/laravel.log`
- [ ] Compte admin créé : `php artisan db:seed --class=DatabaseSeeder`

---

## 10. Mise à jour (déploiement continu)

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
supervisorctl restart 3ao-worker:*
```

---

## Compte admin par défaut
- **Email** : `admin@3ao.org`
- **Mot de passe** : défini dans `.env` ou à changer immédiatement après premier login via `/admin`

> ⚠️ **Changer le mot de passe admin immédiatement en production !**
