<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HandleLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = ['en', 'es', 'fr', 'de', 'zh', 'ja', 'pt', 'ru', 'ar', 'hi'];

        $locale = $request->segment(1);
        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
        } else {
            $locale = session('locale', config('app.locale', 'en'));
            if (!in_array($locale, $supportedLocales)) {
                $locale = 'en';
            }
            App::setLocale($locale);
        }

        return $next($request);
    }
}
