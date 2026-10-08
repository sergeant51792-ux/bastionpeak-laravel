<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Pages\Admin\Overview;
use App\Pages\Admin\Approvals;
use App\Pages\Admin\Reports;
use App\Pages\Admin\AuditLog;
use App\Pages\Admin\Settings;
use App\Resources\Admin\CustomerResource;
use App\Resources\Admin\AccountResource;
use App\Resources\Admin\TransactionResource;
use App\Resources\Admin\MerchantResource;
use App\Resources\Admin\CardResource;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('bastion-peak-admin')
            ->path('admin')
            ->domain(config('app.url') . '/admin')
            ->login()
            ->colors([
                'primary' => Color::hex('#0E6E63'),
            ])
            ->discoverResources(in: app_path('Resources/Admin'), for: 'App\Resources\\Admin')
            ->discoverPages(in: app_path('Pages/Admin'), for: 'App\Pages\\Admin')
            ->pages([
                Overview::class,
                Approvals::class,
            ])
            ->discoverWidgets(in: app_path('Widgets'), for: 'App\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                SubstituteBindings::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web');
    }
}
