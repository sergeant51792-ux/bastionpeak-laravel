<?php

declare(strict_types=1);

namespace App\Kernel;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Http\Middleware\TrustHosts;
use App\Http\Middleware\RequireRole;
use App\Http\Middleware\CheckAccountStatus;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\NoCache;
use App\Http\Middleware\Authenticate;
use Symfony\Component\HttpFoundation\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            if (file_exists(__DIR__ . '/../routes/routes/admin.php')) {
                require __DIR__ . '/../routes/routes/admin.php';
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trusted proxies (configure for your hosting)
        $middleware->trustProxies(at: '*');

        // Global middleware
        $middleware->use([
            PreventRequestsDuringMaintenance::class,
            TrimStrings::class,
            NoCache::class,
        ]);

        // Web middleware group
        $middleware->web([
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        // API middleware group
        $middleware->api([
            ThrottleRequests::class . ':api',
            SubstituteBindings::class,
        ]);

        // Named middleware
        $middleware->alias([
            'auth' => Authenticate::class,
            'role' => RequireRole::class,
            'account.status' => CheckAccountStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom error pages will be registered here
    })
    ->withProviders([
        \App\Providers\AppServiceProvider::class,
        \App\Providers\AuthServiceProvider::class,
        \App\Providers\Filament\AdminPanelProvider::class,
        \App\Providers\EventServiceProvider::class,
        \App\Providers\RouteServiceProvider::class,
    ])
    ->create();
