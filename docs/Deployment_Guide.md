# Deployment Guide

This guide covers steps to deploy IT Quty to a production environment. It focuses on Laravel best practices and common production concerns.

## Server Requirements
- PHP 8.0+ (8.1 recommended)
- MySQL 5.7+ / MariaDB or PostgreSQL
- Composer 2.2+
- Node.js 14+
- Optional: Redis for cache/queues

## Pre-deployment Checklist
1. Provision server with PHP-FPM + Nginx
2. Create system user for the app and set directory permissions
3. Install Composer and Node.js
4. Configure firewall and SSL (Let's Encrypt)

## Environment Variables
Important `.env` keys:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
QUEUE_CONNECTION=redis
```

## Deployment Steps
```bash
# Pull code
git pull origin master

# Install PHP deps
composer install --no-dev --optimize-autoloader

# Install Node deps & build assets
npm ci && npm run prod

# Migrate database
php artisan migrate --force

# Seed minimal data (if needed)
php artisan db:seed --class=TestUsersTableSeeder --force

# Permissions and caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Queue workers (use supervisor/systemd)
php artisan queue:restart
```

## Process Supervision
Use Supervisor or systemd to keep workers running. Example Supervisor config snippet:
```
[program:quty-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work redis --sleep=3 --tries=3 --timeout=60
numprocs=2
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/quty/worker.log
```

## Backups & Monitoring
- Regular DB dumps (mysqldump) to secured storage
- File storage backup (if using local driver)
- Monitor logs (Sentry, Papertrail) and set up alerting

## Rollback Strategy
- Keep migrations reversible
- Use database snapshots before major changes

## Notes
- Do not run seeders in production unless intentional — they may overwrite data.
- For blue/green deployments, use separate database migrations and feature toggles where appropriate.
