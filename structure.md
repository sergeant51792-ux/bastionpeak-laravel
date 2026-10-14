# Bastion Peak — Project Structure

Laravel 11 banking infrastructure platform for finance and operations teams. Built with PHP 8.2+, Filament 3, Livewire 3, Spatie Permissions, Tailwind, Vite, and MySQL.

## Root files

| File | Purpose |
|---|---|
| `composer.json` | PHP dependencies: Laravel 11, Filament 3, Livewire 3, Spatie Permission/ActivityLog, Sanctum, Blade Lucide Icons, DOMPDF |
| `package.json` | NPM dependencies for frontend build (Vite, Tailwind, Alpine.js) |
| `vite.config.js` | Vite asset pipeline configuration |
| `tailwind.config.js` | Tailwind CSS configuration |
| `docker-compose.yml` | Docker environment: MySQL, Redis, app, nginx |
| `.env.example` | Environment variable template |
| `README.md` | Project documentation |
| `.gitignore` | Git ignore patterns |
| `structure.md` | This file |
| `bastion-peak-redesign-prompt.md` | Frontend redesign specification |

## Directory tree

```
bastionpeak-laravel/
├── app/
│   ├── Console/Commands/
│   │   ├── AcceptanceCheck.php      # 9-point quality gate (no emojis, hex colors, TODOs, etc.)
│   │   ├── CheckBalances.php        # Verifies ledger balance consistency
│   │   ├── CleanupNotifications.php # Prunes old notifications
│   ├── Enums/
│   │   ├── AccountStatus.php        # active, frozen, locked, closed
│   │   ├── AccountType.php          # main, sub, reserve, escrow
│   │   ├── TransactionStatus.php    # pending, pending_review, approved, rejected, posted, reversed
│   │   └── TransactionType.php      # deposit_credit, payment_debit, transfer
│   ├── Events/
│   │   ├── DepositApproved.php      # Fired when deposit is approved
│   │   ├── DepositRejected.php      # Fired when deposit is rejected
│   │   ├── PaymentApproved.php      # Fired when payment is approved
│   │   ├── PaymentRejected.php      # Fired when payment is rejected
│   │   ├── TransactionPosted.php    # Fired when transaction posts to ledger
│   │   ├── AccountLocked.php        # Fired when account is locked
│   │   └── AdminAction.php          # Fires on all admin actions
│   ├── Http/Controllers/
│   │   ├── Admin/                    # Admin controllers (Super Admin role)
│   │   │   ├── ApprovalController.php
│   │   │   ├── AccountController.php
│   │   │   ├── AccountDepositMethodController.php
│   │   │   ├── AuditLogController.php
│   │   │   ├── CardController.php
│   │   │   ├── CardRequestController.php
│   │   │   ├── CreditDebitController.php
│   │   │   ├── DepositMethodController.php
│   │   │   ├── FinancialServiceController.php
│   │   │   ├── MerchantController.php
│   │   │   ├── MessageController.php
│   │   │   ├── OverviewController.php
│   │   │   ├── ReportController.php
│   │   │   ├── SettingController.php
│   │   │   ├── TransactionController.php
│   │   │   └── UserController.php
│   │   ├── Auth/
│   │   │   ├── SignInController.php
│   │   │   ├── RegisterController.php
│   │   │   └── ForgotPasswordController.php
│   │   ├── Customer/                 # Customer-facing controllers
│   │   │   ├── AccountController.php
│   │   │   ├── ActivityController.php
│   │   │   ├── CardController.php
│   │   │   ├── DepositController.php
│   │   │   ├── FinancialServiceController.php
│   │   │   ├── HomeController.php
│   │   │   ├── MessageController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── PayController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── ReceiveController.php
│   │   │   ├── StatementController.php
│   │   │   ├── TopUpController.php
│   │   │   ├── TransferController.php
│   │   │   └── ...
│   │   ├── PageController.php        # Static pages (support, learn, policies, etc.)
│   │   └── Middleware/
│   │       ├── Authenticate.php       # Handles guest-to-auth redirects
│   │       ├── CheckAccountStatus.php  # Blocks access if account frozen
│   │       ├── HandleLocale.php       # Multi-language (en, es, fr)
│   │       ├── NoCache.php           # Prevents caching for authenticated pages
│   │       └── RequireRole.php       # Role-based access for admin
│   ├── Jobs/
│   │   ├── GenerateStatement.php
│   │   ├── ProcessDepositProof.php   # OCR/placeholder image handling
│   │   ├── ResetLimitUsage.php       # Daily/monthly limit resets
│   │   └── SendNotification.php
│   ├── Listeners/
│   │   ├── CheckBalanceAfterTransaction.php
│   │   ├── LogAdminAction.php
│   │   ├── SendDepositApprovedNotification.php
│   │   ├── SendDepositRejectedNotification.php
│   │   ├── SendPaymentApprovedNotification.php
│   │   ├── SendPaymentRejectedNotification.php
│   │   └── SendAccountLockedNotification.php
│   ├── Models/
│   │   ├── User.php                  # Spatie roles, TOTP, profile picture, language/theme prefs
│   │   ├── Account.php               # Bank accounts with IBAN, balance, limits
│   │   ├── AccountLimit.php
│   │   ├── AccountDepositMethod.php
│   │   ├── Transaction.php           # Double-entry, status, type enums
│   │   ├── TransactionLeg.php        # Debit/credit ledger entries
│   │   ├── Card.php
│   │   ├── CardRequest.php
│   │   ├── Currency.php
│   │   ├── Customer.php
│   │   ├── DepositMethod.php
│   │   ├── FinancialService.php
│   │   ├── FinancialServiceApplication.php
│   │   ├── LimitUsage.php
│   │   ├── LoginOtp.php
│   │   ├── Merchant.php
│   │   ├── Message.php
│   │   ├── Notification.php
│   │   ├── SystemSetting.php
│   │   └── TaxRefund.php
│   ├── Pages/Admin/                  # Filament 3 pages
│   │   ├── Approvals.php             # Transaction approval queue (Livewire)
│   │   ├── AuditLog.php
│   │   ├── Overview.php
│   │   ├── Reports.php
│   │   └── Settings.php
│   ├── Policies/
│   │   ├── AccountPolicy.php
│   │   ├── MessagePolicy.php
│   │   └── TransactionPolicy.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   ├── Filament/AdminPanelProvider.php
│   │   └── RouteServiceProvider.php
│   ├── Resources/                    # Filament CRUD resources
│   │   ├── AccountResource.php
│   │   ├── CardResource.php
│   │   ├── CustomerResource.php
│   │   ├── MerchantResource.php
│   │   └── TransactionResource.php
│   ├── Services/                     # Domain services (business logic)
│   │   ├── BalanceCheckService.php
│   │   ├── CardService.php
│   │   ├── DepositService.php       # Deposit submit/approve/reject with ledger
│   │   ├── LedgerService.php        # Double-entry bookkeeping core
│   │   ├── NotificationService.php
│   │   ├── PaymentService.php       # Payment submit/approve/reject with limit checks
│   │   └── TotpService.php          # 2FA TOTP setup
│   └── Widgets/                      # Filament dashboard widgets
│       ├── AnomalyWidget.php
│       ├── PendingApprovalsWidget.php
│       ├── RecentActionsWidget.php
│       └── StatsOverview.php
├── bootstrap/
│   ├── app.php                      # Bootstrap and configure Laravel app
│   └── providers.php                # Provider registration
├── config/
│   ├── app.php, auth.php, cache.php
│   ├── blade-icons.php              # Icon configuration
│   ├── database.php, filesystems.php
│   ├── logging.php, mail.php
│   ├── permission.php               # Spatie permission config
│   ├── queue.php, services.php
│   ├── session.php
│   └── config/bastion.php           # Custom Bastion Peak config
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/                  # Chronologically ordered (2024-01 through 2026-10)
│   │   ├── 0001_*                   # Base Laravel (users, cache, jobs)
│   │   ├── 2024_01_01_*             # Core: roles, currencies, accounts, merchants, transactions
│   │   ├── 2024_01_01_000011        # transaction_legs (double-entry)
│   │   ├── 2026_10_*                # Cards, deposits, financial services, profile, OTP, TOTP
│   └── seeders/
│       ├── AdminUserSeeder.php      # Bootstrap admin account
│       ├── CurrencySeeder.php
│       ├── CustomerSeeder.php
│       ├── DepositMethodSeeder.php
│       ├── FinancialServiceSeeder.php
│       ├── MerchantSeeder.php
│       ├── RoleSeeder.php           # Super Admin, Customer, Auditor roles
│       └── DatabaseSeeder.php
├── public/
│   ├── css/tokens.css               # Design tokens (colors, shadows, radii)
│   ├── css/app.css                  # Compiled Tailwind via Vite
│   ├── customer/                    # Customer portal CSS (separate token set)
│   │   ├── app.css
│   │   ├── tokens.css
│   │   └── BUILD-REPORT.md
│   ├── fonts/schibsted.css          # Schibsted Grotesk self-hosted fonts
│   ├── js/app.js                    # Frontend JavaScript entry
│   ├── lottie/                      # Animation files
│   │   ├── bank-card.json
│   │   ├── bastion-peak-logo.json
│   │   ├── grant.json
│   │   ├── loan.json
│   │   ├── success-check.json
│   │   └── tax-refund.json
│   ├── manifest.json                # PWA manifest
│   ├── favicon.svg
│   ├── build/                       # Vite compiled assets (production)
│   └── index.php                    # Laravel entry point
├── resources/
│   ├── css/app.css                  # Main CSS (Tailwind imports + custom rules)
│   ├── js/
│   │   ├── app.js                   # Main JS entry
│   │   └── bootstrap.js             # Bootstrap utilities
│   ├── lang/
│   │   ├── en.json
│   │   ├── es.json
│   │   └── fr.json                  # i18n translation files
│   ├── views/
│   │   ├── admin/                   # Admin Blade templates (custom, not Filament)
│   │   │   ├── approvals.blade.php  # Deposit/Payment approval queue
│   │   │   ├── audit-log.blade.php
│   │   │   ├── overview.blade.php
│   │   │   ├── transactions.blade.php
│   │   │   ├── users.blade.php
│   │   │   ├── accounts.blade.php
│   │   │   ├── cards.blade.php
│   │   │   ├── merchants.blade.php
│   │   │   ├── reports.blade.php
│   │   │   ├── settings.blade.php
│   │   │   ├── transaction-detail.blade.php
│   │   │   ├── transaction-create.blade.php
│   │   │   ├── user-detail.blade.php
│   │   │   ├── account-detail.blade.php
│   │   │   ├── card-detail.blade.php
│   │   │   ├── card-requests.blade.php
│   │   │   ├── financial-services.blade.php
│   │   │   ├── financial-service-applications.blade.php
│   │   │   ├── messages.blade.php
│   │   │   ├── message-thread.blade.php
│   │   ├── auth/                    # Authentication views
│   │   │   ├── signin.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── verify-otp.blade.php   (6-digit OTP)
│   │   │   ├── verify-pin.blade.php   (4-digit PIN)
│   │   │   ├── verify-login-pin.blade.php
│   │   │   ├── first-signin.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   ├── reset-password.blade.php
│   │   │   └── change-password.blade.php
│   │   ├── customer/                  # Customer-facing views
│   │   │   ├── home.blade.php
│   │   │   ├── transfer.blade.php
│   │   │   ├── deposit.blade.php
│   │   │   ├── top-up.blade.php
│   │   │   ├── receive.blade.php
│   │   │   ├── pay.blade.php
│   │   │   ├── cards.blade.php
│   │   │   ├── account-detail.blade.php
│   │   │   ├── activity.blade.php
│   │   │   ├── messages.blade.php
│   │   │   ├── notifications.blade.php
│   │   │   ├── statements.blade.php
│   │   │   ├── profile.blade.php
│   │   │   └── financial-service-apply.blade.php
│   │   │   └── financial-services.blade.php
│   │   ├── filament/                  # Filament page views
│   │   │   ├── pages/approvals.blade.php  # Livewire approval queue
│   │   │   ├── pages/accounts.blade.php
│   │   │   ├── pages/audit-log.blade.php
│   │   │   ├── pages/customers.blade.php
│   │   │   ├── pages/overview.blade.php
│   │   │   ├── pages/reports.blade.php
│   │   │   ├── pages/settings.blade.php
│   │   │   └── widgets/
│   │   │       ├── anomalies.blade.php
│   │   │       ├── pending-approvals.blade.php
│   │   │       ├── recent-actions.blade.php
│   │   │       └── stats-overview.blade.php
│   │   ├── components/                  # Reusable Blade components
│   │   │   ├── account-card.blade.php
│   │   │   ├── avatar.blade.php
│   │   │   ├── bottom-sheet.blade.php
│   │   │   ├── bp-logo.blade.php       # Logo (uses Lucide "landmark" icon)
│   │   │   ├── confirm-dialog.blade.php
│   │   │   ├── custom-select.blade.php
│   │   │   ├── data-table.blade.php
│   │   │   ├── empty-state.blade.php
│   │   │   ├── icon.blade.php          # Lucide icon wrapper
│   │   │   ├── limit-meter.blade.php
│   │   │   ├── proof-uploader.blade.php
│   │   │   ├── quick-action.blade.php
│   │   │   ├── request-stepper.blade.php
│   │   │   ├── status-pill.blade.php
│   │   │   ├── toast.blade.php
│   │   │   └── txn-row.blade.php
│   │   ├── layouts/                  # Layout templates
│   │   │   ├── admin.blade.php
│   │   │   ├── app.blade.php
│   │   │   ├── app-dark.blade.php
│   │   │   ├── customer.blade.php
│   │   │   └── public.blade.php
│   │   ├── landing.blade.php         # Landing page
│   │   └── pages/                    # Static content pages
│   │       ├── learn.blade.php
│   │       ├── policies.blade.php
│   │       ├── privacy.blade.php
│   │       ├── recommendations.blade.php
│   │       ├── support.blade.php
│   │       └── terms.blade.php
├── routes/
│   ├── web.php                       # Public + customer routes
│   ├── routes/admin.php              # Admin routes (Super Admin role middleware)
│   ├── routes/console.php
│   └── routes/routes/console.php
├── tests/
│   ├── bootstrap.php
│   ├── TestCase.php
│   ├── Feature/
│   │   ├── AdminRouteTest.php
│   │   └── ExampleTest.php
│   └── Unit/
│       └── ExampleTest.php
└── storage/                          # Logs, cache, file uploads
```

## Key data flows

### Deposit flow
Customer submits → `DepositService::submit()` → Transaction created with `status=pending_review` → Admin approves via `/admin/approvals` or Filament `/panel/approvals` → `DepositService::approve()` → Credit posted to ledger → `DepositApproved` event fires → Customer sees success popup

### Payment/transfer flow
Customer submits → `PaymentService::submit()` → Limit check → Transaction created with `status=pending_review` → Admin approves → `PaymentService::approve()` → Double-entry debit/credit posted → `PaymentApproved` event fires → Success popup

### Approval workflows
Two admin systems exist:
- **Custom Blade** (`/admin/*`, middleware `role:Super Admin`) — `ApprovalController.php`
- **Filament panel** (`/panel/*`, `canAccessPanel()` checks) — `Approvals.php` page

The Filament approvals page uses Livewire `wire:submit` for approve/reject (no form `action` fallback to the custom admin route).

## Design tokens reference

| Variable | Light | Dark | Purpose |
|---|---|---|---|
| `--bg` | `#F1F5F9` | `#0F172A` | Page background |
| `--surface` | `#FFFFFF` | `rgba(30,35,60,0.45)` | Cards/surfaces |
| `--surface-raised` | `#F8FAFC` | `rgba(40,50,80,0.55)` | Raised surfaces |
| `--line` | `#E2E8F0` | `rgba(60,70,100,0.4)` | Dividers/borders |
| `--text` | `#0F172A` | `#F1F5F9` | Primary text |
| `--text-muted` | `#64748B` | `#94A3B8` | Secondary text |
| `--primary` | `#1E3A8A` | `#3B82F6` | Primary (navy blue) |
| `--primary-dark` | `#0F2557` | `#2563EB` | Darker primary |
| `--accent` | `#3B82F6` | `#60A5FA` | Accent (blue) |
| `--accent-subtle` | `#EFF6FF` | `#1E3A8A` | Subtle accent background |
| `--success` | `#059669` | `#4ADE80` | Credit/incoming |
| `--danger` | `#DC2626` | `#F87171` | Debit/error |
| `--warning` | `#D97706` | `#FB923C` | Pending/warnings |
| `--card-navy` | `#0F2557` | `#1E3A8A` | Account card gradient start |
| `--card-blue` | `#1E3A8A` | `#3B82F6` | Account card gradient mid |
| `--card-sky` | `#075985` | `#0EA5E9` | Account card gradient end |

## Acceptance checks (`php artisan bastion:acceptance`)

1. No emoji in codebase
2. CSS variables used (tokens.css exists)
3. No hardcoded hex colors in Blade views
4. No TODO/FIXME comments
5. No console.log
6. No `href="#"` placeholders
7. Semantic HTML present (header, main, nav)
8. Tabular-nums on money
9. Accessible labels (aria-label)
