<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;

class ReceiveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();

        $selectedAccountId = $request->query('account');

        $accounts = $user->accounts()->where('status', 'active')->with('currency', 'depositMethods')->get();

        $selectedAccount = null;
        if ($selectedAccountId) {
            $selectedAccount = $accounts->firstWhere('id', $selectedAccountId) ?? $accounts->first();
        } else {
            $selectedAccount = $accounts->first();
        }

        return view('customer.receive', [
            'accounts'        => $accounts,
            'selectedAccount' => $selectedAccount,
        ]);
    }
}
