# Bastion Peak — cPanel Deployment Guide

This guide walks you through deploying the Bastion Peak banking application to a cPanel hosting environment on your `bastionpeaks.com` domain.

## Prerequisites

Before starting, ensure your cPanel hosting meets these requirements:

- **PHP**: 8.2 or 8.3 (Laravel 11 requires PHP 8.2+)
- **MySQL**: 8.0 or 5.7+ (MariaDB 10.4+ acceptable)
- **cPanel Account** with:
  - File Manager or FTP access
  - Terminal access (SSH)
  - MySQL database management
  - Subdomain / domain management
- **Composer** installed server-side (or use the [Git Deployment method](#method-b-deploy-via-git-cpanel) if Composer is unavailable)
- **Node.js** v18+ (for building frontend assets)

---

## No Terminal Access — Quick Start (recommended)

This is the supported path when your cPanel has **no Terminal/SSH**. It keeps the
Laravel application **outside** the web root (so `.env`, `app/`, `storage/` are
never exposed) and uses a single token-protected helper script to do everything
artisan would normally do.

### 1. Upload the application

Put the whole project in your home directory (NOT the web root), e.g.:

```
/home/bastionpeaks/bastionpeak-laravel/
```

Upload the ZIP through **File Manager → Upload**, then **Extract**. Make sure
`vendor/` is included (run `composer install` and `npm run build` on your own
machine first, then upload). Never upload `node_modules/`.

### 2. Create the web root

In `/home/bastionpeaks/public_html/` place these files:

1. `cpanel/index.php`  →  `public_html/index.php`
2. `cpanel/.htaccess`  →  `public_html/.htaccess`
3. `deploy.php` (from the project root) → `public_html/deploy.php`

`index.php` automatically finds the app folder and calls `usePublicPath()`, so
`public_path()`, `@vite`, `asset()` and `storage:link` all resolve to
`public_html`. If your app folder has a different name, edit the
`$APP_DIR_SEARCH` array at the top of `index.php`.

### 3. Create `.env`

Create `/home/bastionpeaks/bastionpeak-laravel/.env` via File Manager with real
credentials (copy `.env.example`, then edit). Minimum:

```env
APP_NAME="Bastion Peak"
APP_ENV=production
APP_KEY=base64:PASTE_KEY_FROM_YOUR_MACHINE
APP_DEBUG=false
APP_URL=https://bastionpeaks.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=bastionpeak
DB_USERNAME=bastionpeak
DB_PASSWORD=your_db_password

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

DEPLOY_KEY=some-long-random-string-only-you-know
```

> Generate `APP_KEY` locally with `php artisan key:generate` and paste the value.
> Use `SESSION_DRIVER=file` / `CACHE_STORE=file` initially — `database` drivers
> will 500 on every page until migrations have created the `sessions`/`cache`
> tables. Switch to `database` after step 5 if you prefer.

### 4. Set the deploy key

Add a long random secret to the app's `.env`:

```env
DEPLOY_KEY=some-long-random-string-only-you-know
```

`deploy.php` reads `DEPLOY_KEY` from `.env`. It falls back to a default key if
missing (and shows a warning on the page telling you to set it), so set this
before exposing the file.

### 5. Run setup through the browser

Open:

```
https://bastionpeaks.com/deploy.php?key=YOUR_KEY
```

The page shows a health check (PHP version, extensions, DB connection, writable
folders) and buttons. Click them **in order**:

1. **Run migrations**
2. **Seed data**
3. **Copy build assets** (copies `public/build` into `public_html/build`)
4. **Storage link** (creates `public_html/storage` → `storage/app/public`)
5. **Cache config** / **Cache routes** / **Cache views**

`Run everything` does all of the above at once. `Clear caches` is available if
you change `.env` or code.

### 6. Delete `deploy.php`

**Delete `deploy.php` from `public_html` as soon as setup works.** Anyone with
the key can run migrations and seeders.

### 7. Smoke test

- `https://bastionpeaks.com/` → landing page
- `https://bastionpeaks.com/signin` → login
- `https://bastionpeaks.com/register` → signup (creates a pending user)
- `https://bastionpeaks.com/admin` → admin panel (seeded Super Admin)

If you see a 500, view `storage/logs/laravel.log`, then re-add `deploy.php`
temporarily and check the **info** page.

## Deploying Updates via GitHub

Once the first setup above is done, use GitHub to ship updates instead of
re-uploading files by hand. There are two supported options.

### Option 1 — cPanel «Git™ Version Control» (no terminal)

Most cPanel hosts expose **Git™ Version Control** even when there is no terminal.

1. cPanel → **Git™ Version Control** → **Create**.
2. Clone URL: `https://github.com/sergeant51792-ux/bastionpeak-laravel.git`.
3. Repository path: `/home/bastionpeaks/bastionpeak-laravel` (the app folder,
   **outside** `public_html`).
4. Create. For later updates, click **Update from Remote** (runs `git pull`).

Because `vendor/`, `.env` and `storage/` are git-ignored, pulls never overwrite
them. After a pull that adds migrations, re-open
`deploy.php?key=…&action=migrate` (or `action=all`), then delete `deploy.php`
again.

### Option 2 — GitHub Actions (auto-deploy on push)

`.github/workflows/deploy.yml` builds Composer + Vite assets on every push to
`main` and FTPs them to cPanel. Add these **repository secrets**
(Settings → Secrets and variables → Actions):

| Secret | Example | Purpose |
|---|---|---|
| `FTP_SERVER` | `ftp.bastionpeaks.com` | FTP host from cPanel |
| `FTP_USERNAME` | `bastionpeaks` | FTP account |
| `FTP_PASSWORD` | `…` | FTP password |
| `APP_DIR` | `/home/bastionpeaks/bastionpeak-laravel` | App folder (outside web root) |
| `PUBLIC_DIR` | `/home/bastionpeaks/public_html` | Web root |
| `DEPLOY_URL` | `https://bastionpeaks.com/deploy.php` | Optional: for the migration step |
| `DEPLOY_KEY` | same as `.env` `DEPLOY_KEY` | Optional: for the migration step |

The workflow uploads the app (including a freshly built `vendor/`, so the server
needs no Composer) and syncs `public/` into `public_html` — it deliberately does
**not** overwrite `public_html/index.php` or `.htaccess`, so your cPanel shim
stays. To run migrations after a deploy, use **Actions → Deploy to cPanel → Run
workflow** and tick **run_migrations**.

> Keep the repository **private** — this is a banking application. Never commit
> `.env`, and rotate `DEPLOY_KEY` if it is ever exposed.

### Changing the document root instead (alternative)

If cPanel lets you change the document root, point it at
`/home/bastionpeaks/bastionpeak-laravel/public` and skip `cpanel/index.php`.
Everything else (steps 3, 4, 5, 6) is identical — `deploy.php` auto-detects the
layout.

> The old public Artisan URLs (`/run-migrate`, `/run-seed`, `/storage-link`,
> `/config-cache`, `/optimize`, `/clear-cache`, `/copy-build`) were **removed**
> because they were reachable by anyone. `deploy.php` replaces them and is
> key-protected.

### Cron without terminal

Through **cPanel → Cron Jobs** (if available):

```
* * * * * /usr/bin/curl -s https://bastionpeaks.com/up >/dev/null 2>&1
```

Without cron, scheduled tasks (cleanup, limit resets) will not run
automatically. Everything else works without it.

---

## Step 3: Create the Database

1. Log in to your cPanel.
2. Go to **MySQL Databases**.
3. **Create a new database** — e.g., `bastionpeak_db`.
4. **Create a new MySQL user** — e.g., `bastionpeak_user`.
5. Assign a **strong password** and **add the user to the database** with **All Privileges**.
6. Note down:
   - Database name: `bastionpeak_db`
   - Database user: `bastionpeak_user`
   - Password: (the one you set)
   - Host: `localhost`

---

## Step 2: Configure the Domain

1. In cPanel, go to **Domains** (or **Subdomains / Addon Domains**).
2. Ensure your domain `bastionpeaks.com` is added.
   - If you are deploying as the root domain, skip this step.
   - If deploying on a subdomain (e.g., `app.bastionpeaks.com`), add it as a subdomain pointing to `/public_html`.
3. Set the **document root** to the Laravel `public/` folder:
   - The Laravel public directory must be at `public_html` (the web root).
   - Your `index.php`, `.htaccess`, and static assets live in `public/`.

> If your host only allows `public_html` as the document root but Laravel's `public/` folder is inside the project root, you'll need to either move `public/` contents into `public_html/` or adjust paths. See [Step 4](#method-a-deploy-via-zip--terminal) for the recommended approach.

---

## Step 3: Deploy the Application

### Method A: Deploy via Git (Recommended if SSH + Git available)

1. In cPanel, find **Git™ Version Control** (or use SSH directly).
2. Create a new repository deployment:
   - Clone from GitHub:
     ```
     git clone https://github.com/sergeant51792-ux/bastionpeak-laravel.git
     ```
   - Or via cPanel Git UI, enter: `https://github.com/sergeant51792-ux/bastionpeak-laravel.git`
3. Choose the deployment path — e.g., `/home/yourcpaneluser/bastionpeak-laravel`.
4. Click **Clone** (or **Create** in the UI).

### Method B: Deploy via ZIP Upload + Terminal

1. Download the repository as a ZIP from: https://github.com/sergeant51792-ux/bastionpeak-laravel
2. Extract all files locally.
3. Upload the entire extracted folder to your cPanel account via **File Manager** or **FTP** (e.g., to `/home/yourcpaneluser/bastionpeak-laravel`).
4. Alternatively, upload the ZIP directly to your home directory via File Manager, then use **Terminal** in cPanel to extract it.

---

## Step 4: Set Document Root

Laravel's entry point is `public/index.php`. The document root must point to the `public/` directory:

1. In cPanel, go to **Setup → Select Document Root for your domain** or go to **MultiPHP INI Editor / Domain Manager**.
2. Change the document root to the `public/` directory inside your project:
   - Default: `/home/yourcpaneluser/public_html`
   - Change to: `/home/yourcpaneluser/bastionpeak-laravel/public`

> If the cPanel interface doesn't allow changing the document root:
> - Move the contents of `public/` into `public_html/`
> - Update the `$loader` and `require` paths in `index.php` to reference the parent directory
> - Update `index.php` constants from `__DIR__` to point to `../` properly

---

## Step 5: Configure `.env`

Via cPanel **Terminal**:

```bash
cd /home/yourcpaneluser/bastionpeak-laravel
cp .env.example .env
```

Edit `.env` via File Manager or Terminal (`nano` or `vi`):

```env
APP_NAME="Bastion Peak"
APP_ENV=production
APP_KEY=base64:GENERATE_WITH_PHP_ARTISAN_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://bastionpeaks.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=bastionpeak_db
DB_USERNAME=bastionpeak_user
DB_PASSWORD=your_strong_db_password

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=25
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=noreply@bastionpeaks.com
MAIL_FROM_NAME="Bastion Peak"

STORAGE_LINK=storage/app/public
```

Replace placeholder values with your real database credentials and domain.

### Generate the APP_KEY

```bash
php artisan key:generate
```

---

## Step 6: Install Composer Dependencies

Via cPanel **Terminal**:

```bash
cd /home/yourcpaneluser/bastionpeak-laravel
composer install --optimize-autoloader --no-dev
```

> If `composer` is not available on your host, ask your hosting provider or use **Method B: Deploy via ZIP** with pre-installed vendor directory. You can generate `composer.lock` locally before uploading.

---

## Step 7: Build Frontend Assets

If you have Node.js available on the server:

```bash
cd /home/yourcpaneluser/bastionpeak-laravel
npm install
npm run build
```

If **npm/Node.js is not available**, run these commands locally before uploading:

```bash
npm install
npm run build
```

Then upload the contents of `public/build/` to your server's `public/build/` directory.

---

## Step 8: Set Up Storage Symlink

The `storage/app/public` directory must be accessible from the web:

```bash
cd /home/yourcpaneluser/bastionpeak-laravel
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

---

## Step 9: Run Database Migrations and Seeders

```bash
cd /home/yourcpaneluser/bastionpeak-laravel
php artisan migrate --force
php artisan db:seed --force
```

This will:
- Create all database tables (users, accounts, currencies, transactions, cards, etc.)
- Seed currencies (USD, EUR, etc.)
- Seed roles (Super Admin, Customer)
- Create the default admin user

> After seeding, the default admin credentials are printed in the console output. **Change the admin password immediately** after first login.

---

## Step 10: Set File Permissions

Run these commands via **Terminal** in cPanel:

```bash
cd /home/yourcpaneluser/bastionpeak-laravel
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/build
```

If your host runs PHP as `nobody` or a different user:

```bash
chown -R yourcpaneluser:yourcpaneluser bootstrap/cache
chown -R yourcpaneluser:yourcpaneluser storage
```

---

## Step 11: Configure Cron Job (Queued Jobs / Scheduled Tasks)

In cPanel, go to **Cron Jobs** and add these entries:

```bash
* * * * * cd /home/yourcpaneluser/bastionpeak-laravel && php artisan schedule:run >> /dev/null 2>&1
* * * * * cd /home/yourcpaneluser/bastionpeak-laravel && php artisan queue:work --stopwait --sleep=1 --tries=1 >> /dev/null 2>&1
```

---

## Step 12: Configure SSL

cPanel provides free Let's Encrypt SSL certificates:

1. Go to **SSL/TLS Status** (or **Manage SSL sites**).
2. Select your domain `bastionpeaks.com`.
3. Click **Run AutoSSL** or **Issue** to install an SSL certificate.
4. Ensure HTTPS redirects are configured (see `.htaccess` or server config).

### Force HTTPS in Laravel

In `App\Http\Middleware\TrustProxies` or via the `AppServiceProvider`:

```php
public function boot()
{
    \URL::forceScheme('https');
}
```

Or add to `.htaccess` in the `public/` directory:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## Step 13: Optimize for Production

```bash
cd /home/yourcpaneluser/bastionpeak-laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## Step 14: Test the Deployment

1. Visit `https://bastionpeaks.com` — you should see the landing page.
2. Visit `https://bastionpeaks.com/signin` — you should see the login page.
3. Visit `https://bastionpeaks.com/register` — sign up a test customer account.
4. Log in to the admin panel at `https://bastionpeaks.com/admin`.
5. Run `php artisan migrate:fresh --seed` if you need to re-seed.

---

## Environment Configuration Summary

| Setting | Value |
|---|---|
| Domain | bastionpeaks.com |
| Document Root | `/home/yourcpaneluser/bastionpeak-laravel/public` |
| PHP Version | 8.2 or 8.3 |
| Database | MySQL |
| Storage Link | `public/storage` → `storage/app/public` |
| APP_ENV | production |
| APP_DEBUG | false |
| HTTPS | Enforced |
| DEPLOY_KEY | set in `.env`; delete `deploy.php` after setup |

---

## Troubleshooting

### 500 Internal Server Error

- Check permissions: `storage/` and `bootstrap/cache/` must be writable.
- Check the error log in cPanel under **Errors** or **Raw Access**.
- Ensure `storage/logs/laravel.log` exists and is writable.

### Session Not Persisting

- Set `SESSION_DRIVER=database` and ensure the `sessions` table exists (created by migration).
- Or set `SESSION_DRIVER=file` and ensure `storage/framework/sessions/` is writable.

### Assets Not Loading (CSS/JS)

- Run `npm run build` locally and upload `public/build/` to the server.
- Ensure `public/build/manifest.json` is present.
- Clear cache: `php artisan config:clear && php artisan view:clear && php artisan cache:clear`.

### Email Not Sending

- Configure SMTP in `.env`:
  ```
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.yourmail.com
  MAIL_PORT=587
  MAIL_USERNAME=your@email.com
  MAIL_PASSWORD=your_password
  MAIL_ENCRYPTION=tls
  ```

### Filament Admin Panel Access

- The admin panel is at `/admin`.
- Log in as the seeded Super Admin user.
- Ensure `APP_URL` is set to your domain, not localhost.

### Cron Not Running

- Verify cron jobs are set up in cPanel.
- Test manually: `php /home/yourcpaneluser/bastionpeak-laravel/artisan schedule:run`.

---

## Security Recommendations

1. **Disable `php artisan serve`** — use Apache/Nginx directly.
2. **Keep `.env` outside `public_html`** — store it in the project root only.
3. **Run `composer install --no-dev`** — avoids installing dev tools in production.
4. **Never commit `.env`** to the repository.
5. **Use strong passwords** for all accounts.
6. **Enable 2FA** on the GitHub account and cPanel.
7. **Regularly review** the audit log in the Filament admin panel.
8. **Keep dependencies updated**: `composer update` (test first on a staging copy).
```
