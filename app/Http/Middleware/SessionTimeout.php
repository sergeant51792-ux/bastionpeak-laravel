<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $timeout = (int) config('session.timeout', 600);

        if (Auth::check()) {
            $lastActivity = session('last_activity');

            if ($lastActivity) {
                if ((time() - $lastActivity) > $timeout) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('signin')->with('status', 'Your session has expired. Please sign in again to continue.');
                }
            } else {
                session(['last_activity' => time()]);
            }
        }

        return $next($request);
    }
}
