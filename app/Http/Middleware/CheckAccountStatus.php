<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (in_array(Auth::user()->status, ['frozen', 'locked', 'blocked', 'suspended'])) {
                return abort(403, 'This account is restricted. Contact support for assistance.');
            }

            $activeAccountIdx = (int) $request->cookie('bp_active_account', 0);
            $activeAccount = Auth::user()->accounts()
                ->select(['id', 'status'])
                ->orderBy('type')
                ->orderBy('created_at')
                ->offset($activeAccountIdx)
                ->limit(1)
                ->first();
            if ($activeAccount && in_array($activeAccount->status, ['frozen', 'locked', 'closed'])) {
                return abort(403, 'Your active account is ' . $activeAccount->status . '. Contact support for assistance.');
            }
        }

        return $next($request);
    }
}
