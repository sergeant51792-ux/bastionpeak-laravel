<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request, Account $account): \Illuminate\View\View
    {
        $user = Auth::user();

        abort_if($account->user_id !== $user->id, 403);

        return view('customer.account-detail', [
            'account'     => $account,
            'currency'    => $account->currency,
            'limits'      => $account->limits()->get(),
            'lockHistory' => \App\Models\AuditLog::where('target_type', 'account')
                ->where('target_id', $account->id)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get(),
            'ledger'      => \App\Models\Transaction::where(function ($q) use ($account) {
                $q->where('from_account_id', $account->id)->orWhere('to_account_id', $account->id);
            })->orderByDesc('created_at')->limit(50)->get(),
        ]);
    }
}
