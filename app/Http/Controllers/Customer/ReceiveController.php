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

        if ($selectedAccount) {
            $selectedAccount->load('depositMethods');
        }

        $depositMethodsData = collect();
        foreach ($accounts as $account) {
            $methods = $account->depositMethods()
                ->wherePivot('is_active', true)
                ->get()
                ->map(function ($method) use ($account) {
                    return [
                        'id' => $method->id,
                        'code' => $method->code,
                        'name' => $method->name,
                        'type' => $method->type,
                        'account_id' => $account->id,
                        'account_name' => $account->name,
                        'currency_symbol' => $account->currency->symbol ?? '$',
                        'currency_code' => $account->currency->code ?? 'USD',
                        'address' => $method->pivot->address ?? '',
                        'instructions' => $method->pivot->instructions ?? '',
                        'qr_path' => $method->pivot->qr_path ?? '',
                    ];
                });
            $depositMethodsData = $depositMethodsData->merge($methods);
        }

        $globalMethods = \App\Models\DepositMethod::where('is_active', true)
            ->where(function ($q) {
                $q->where('is_global', true)
                  ->orWhereDoesntHave('accounts');
            })
            ->get()
            ->map(function ($method) use ($accounts) {
                $account = $accounts->first();
                return [
                    'id' => $method->id,
                    'code' => $method->code,
                    'name' => $method->name,
                    'type' => $method->type,
                    'account_id' => $account ? $account->id : 0,
                    'account_name' => $account ? $account->name : 'No active account',
                    'currency_symbol' => $account?->currency->symbol ?? '$',
                    'currency_code' => $account?->currency->code ?? 'USD',
                    'address' => $method->address ?? '',
                    'instructions' => $method->instructions ?? $method->description ?? '',
                    'qr_path' => $method->qr_path ?? '',
                    'icon_image_path' => $method->icon_image_path ?? '',
                ];
            });
        $depositMethodsData = $depositMethodsData->merge($globalMethods)->unique('id')->values();

        return view('customer.receive', [
            'accounts'             => $accounts,
            'selectedAccount'      => $selectedAccount,
            'depositMethodsData'   => $depositMethodsData,
        ]);
    }
}
