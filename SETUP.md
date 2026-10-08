# Bastion Peak — Local Development Setup

## Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- Node.js 20+ and npm
- MySQL 8.0 or MariaDB 10.6+
- A web server (Nginx or Apache)

## Quick Start

```bash
# 1. Clone or copy this directory to your server
cd bastionpeak2

# 2. Install PHP dependencies
composer install --no-interaction --prefer-dist

# 3. Copy environment file
cp .env.example .env
php artisan key:generate

# 4. Create the database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS bastion_peak CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Run migrations with seed data
php artisan migrate --seed --force

# 6. Install npm dependencies (optional, for asset building)
npm install
npm run build

# 7. Serve the application
php artisan serve
# Visit http://127.0.0.1:8000
```

## Default Credentials

| Role | Email | Password |
|---|---|---|
| Super Admin | admin@bastionpeak.internal | AdminPass123! |

Customers must sign in with the credentials created during seeding, or have an admin create an account for them.

## Shared Hosting (cPanel / JiggyNetHost)

The application must live **outside** `public_html` for security. Point the web root to the Laravel `public/` directory.

```
/home/youruser/
  ├── bastion_peak/          ← project root (above web root)
  │   ├── app/
  │   ├── bootstrap/
  │   ├── config/
  │   ├── database/
  │   ├── public/            ← symlink or copy this into public_html/
  │   └── ...
  └── public_html/
      ├── index.php          ← symlink to ../bastion_peak/public/index.php
      ├── .htaccess          ← copy from bastion_peak/public/.htaccess
      └── ...
```

Or on cPanel:

```
1. Upload the full project as bastion_peak/ above public_html
2. In public_html, replace everything with a symlink to bastion_peak/public/
   Or manually copy public/.htaccess and public/index.php
3. Set document root to public_html/
4. Create the database and user in cPanel MySQL Databases
5. Import the database via phpMyAdmin or command line
6. Run `php artisan migrate --seed` via SSH or cPanel Terminal
7. Set up cron for scheduled jobs (limit reset):
   * * * * * /usr/local/bin/php /home/youruser/bastion_peak/artisan schedule:run
```

## Cron Jobs (Required on Shared Hosting)

```cron
# Run Laravel scheduler every minute
* * * * * /usr/local/bin/php /home/youruser/bastion_peak/artisan schedule:run >> /dev/null 2>&1
```

This handles: daily limit resets, stale notification cleanup, and periodic balance integrity checks.

## Environment Variables

Key variables in `.env`:

| Variable | Purpose | Default |
|---|---|---|
| `APP_NAME` | Application name | Bastion Peak |
| `APP_DEBUG` | Show errors | false (production) |
| `APP_URL` | Public URL | https://bastionpeak.internal |
| `DB_*` | Database connection | See `.env.example` |
| `BASTION_LARGE_TRANSACTION_THRESHOLD` | Extra confirmation threshold | 10000 |
| `BASTION_FAILED_LOGIN_LOCKOUT` | Failed logins before lock | 5 |
| `BASTION_SESSION_TIMEOUT` | Minutes before idle logout | 120 |
| `BASTION_DEPOSIT_REQUIRES_APPROVAL` | Admin must confirm deposits | true |
| `BASTION_PAYMENT_REQUIRES_APPROVAL` | Admin must confirm payments | true |

## Queue Workers

On shared hosting, background queue workers are typically unavailable. Use:

```env
QUEUE_CONNECTION=sync
```

For production with queue support, configure Supervisor:

```
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/bastion_peak/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=2
```

## Security Checklist

- [ ] `.env` is outside `public_html` (or above web root)
- [ ] `APP_DEBUG=false` in production
- [ ] HTTPS enforced (redirect HTTP to HTTPS in `.htaccess`)
- [ ] File permissions: `storage/` and `bootstrap/cache/` writable (755 or 775)
- [ ] All passwords use bcrypt (Laravel default)
- [ ] CSRF protection enabled (Laravel default)
- [ ] Rate limiting on auth routes
- [ ] Upload directory outside `public/` with authorized route serving

## Banking Compliance

- All money stored as `DECIMAL(27,18)` — never `FLOAT`
- All balance changes within database transactions with `SELECT ... FOR UPDATE`
- Idempotency keys prevent duplicate transactions
- Every admin action logged to `audit_logs` (append-only)
- Balance = sum of ledger history (not an independent field)
