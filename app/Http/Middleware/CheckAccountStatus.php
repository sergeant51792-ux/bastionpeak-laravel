<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && in_array(Auth::user()->status, ['frozen', 'locked', 'blocked', 'suspended'])) {
            return abort(403, 'This account is restricted. Contact support for assistance.');
        }

        return $next($request);
    }
}
