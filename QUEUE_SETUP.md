# Configuration de la Queue pour l'envoi des emails

## Comment ça fonctionne

Lors de la validation d'une commande, le système :

1. **Tente d'envoyer les emails immédiatement** (synchrone)
2. **Si le serveur mail est inaccessible**, un `Job` est mis en queue et réessaie automatiquement :
   - Tentative 1 : après 1 minute
   - Tentative 2 : après 5 minutes
   - Tentative 3 : après 15 minutes
   - Tentative 4 : après 30 minutes
   - Tentative 5 : après 1 heure
3. **La commande est toujours validée**, même si les emails échouent

## Configuration dans `.env`

```env
# Driver de queue : database (utilise la table jobs en BDD)
QUEUE_CONNECTION=database

# Configuration mail SMTP (exemple Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre@gmail.com
MAIL_PASSWORD=votre_mot_de_passe_application
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre@gmail.com
MAIL_FROM_NAME="Votre Boutique"

# Configuration mail (exemple avec un service transactionnel type Brevo/Mailgun)
# MAIL_MAILER=smtp
# MAIL_HOST=smtp-relay.brevo.com
# MAIL_PORT=587
# MAIL_USERNAME=votre_login_brevo
# MAIL_PASSWORD=votre_cle_api_brevo
# MAIL_ENCRYPTION=tls
```

## Lancer le worker de queue

Après avoir configuré `.env`, exécutez cette commande pour démarrer le worker :

```bash
php artisan queue:work --queue=default --tries=5 --backoff=60 --timeout=60
```

### En production (avec Supervisor)

Créez `/etc/supervisor/conf.d/laravel-worker.conf` :

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/votre-projet/artisan queue:work database --sleep=3 --tries=5 --timeout=60
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/votre-projet/storage/logs/worker.log
stopwaitsecs=3600
```

Puis :
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## Vérifier les jobs en attente

```bash
# Voir les jobs en attente
php artisan queue:monitor

# Relancer les jobs échoués
php artisan queue:retry all

# Voir les jobs définitivement échoués
php artisan queue:failed
```
