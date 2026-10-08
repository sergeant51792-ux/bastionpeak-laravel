# Bastion Peak — Internal Banking System

## Overview

Bastion Peak is a closed-loop internal banking platform for the company. Admin-controlled, customer accounts are purpose-restricted, money flow is fully traceable, and spending is limited to approved sellers.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+ / Laravel 11 |
| Database | MySQL 8.0 |
| Frontend | Blade + Livewire + Alpine.js + Tailwind CSS |
| Admin Panel | Filament (free) |
| Charts | Chart.js |
| PDF | barryvdh/laravel-dompdf |
| Auth | Laravel Breeze |
| Fonts | Schibsted Grotesk (self-hosted) |

## Design Tokens

All colors, spacing, typography, and motion values are defined as CSS custom properties in `public/css/tokens.css`. Both light and dark themes ship from the same source of truth.

- **Light default** (admin, customer on system light)
- **Dark default** (customer mobile)
- `prefers-color-scheme` respected on first visit; user choice persisted per account

## Project Structure

```
bastionpeak2/
├── app/
│   ├── Http/Controllers/
│   │   ├── Customer/
│   │   └── Admin/
│   ├── Livewire/
│   │   ├── Customer/
│   │   └── Admin/
│   ├── Models/
│   ├── Services/
│   ├── Jobs/
│   ├── Listeners/
│   ├── Policies/
│   └── Enums/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   ├── web.php           (customer)
│   └── admin.php         (Filament panel)
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── components/
│   │   ├── customer/
│   │   └── admin/
│   └── css/
│       ├── tokens.css    (design tokens)
│       ├── app.css       (customer styles)
│       └── admin.css     (admin overrides)
├── public/
│   ├── css/
│   ├── js/
│   │   └── app.js
│   ├── fonts/
│   └── customer/         (Phase 1 prototype)
└── composer.json
```

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Roles

| Role | Scope |
|---|---|
| Super Admin | Full control |
| Customer | Own accounts and requests only |
| Auditor | Read-only |

## Development Phases

| Phase | Features |
|---|---|
| 1 | Auth, accounts, deposit/payment requests, admin confirm/reject, double-entry ledger, admin credit/debit tools |
| 2 | Account locking, limits, notifications, messaging, merchant whitelist, reports |
| 3 | Multi-currency, audit log, system settings |
| 4 | Virtual cards, advanced analytics, auditor role |

## Banking Rules

- All money stored as `DECIMAL(27,18)` (crypto) or `BIGINT` cents (fiat). Never `FLOAT`.
- All transactions use database transactions with row locking.
- Balance = sum of ledger history (prevents tampering).
- All admin actions are logged (append-only audit trail).
- Idempotency keys on every money movement.
- CSRF protection on all forms, bcrypt password hashing, HTTPS enforced.

## Brand

**BASTION PEAK** — internal use only. No external-facing pages.
