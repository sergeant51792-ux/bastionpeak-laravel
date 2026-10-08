<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share authenticated user with all views
        View::composer('*', function ($view) {
            $view->with('authUser', auth()->user());
        });

        // Custom Blade directives
        Blade::if('role', function (string $role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        Blade::if('customer', fn () => auth()->check() && auth()->user()->hasRole('Customer'));
        Blade::if('admin', fn () => auth()->check() && auth()->user()->hasRole('Super Admin'));
        Blade::if('auditor', fn () => auth()->check() && auth()->user()->hasRole('Auditor'));
    }
}
