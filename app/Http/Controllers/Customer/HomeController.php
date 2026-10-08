<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LedgerService;
use App\Models\Account;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = $request->user();
        $accountIds = $user->accounts()->pluck('id');
        $accounts = $user->accounts()->orderBy('type')->orderBy('created_at')->with('currency')->get();

        $baseCurrency = \App\Models\Currency::where('is_base', true)->first();
        $baseCode = $baseCurrency?->code ?? 'USD';

        $targetCurrencies = \App\Models\Currency::whereIn('code', [$baseCode, 'USDT', 'BTC'])
            ->orWhere('is_base', true)
            ->get()
            ->filter()
            ->unique('code')
            ->values();

        if (!$targetCurrencies->contains('code', 'USDT')) {
            $usdt = \App\Models\Currency::where('code', 'USDT')->first();
            if ($usdt) {
                $targetCurrencies->push($usdt);
            }
        }
        if (!$targetCurrencies->contains('code', 'BTC')) {
            $btc = \App\Models\Currency::where('code', 'BTC')->first();
            if ($btc) {
                $targetCurrencies->push($btc);
            }
        }

        $targetCurrencies = $targetCurrencies->unique('code')->values();

        $walletBalances = [];
        foreach ($targetCurrencies as $currency) {
            $rate = max((float) ($currency->exchange_rate ?? 1), 0.0000000001);
            $balance = 0;
            foreach ($accounts as $account) {
                if ((float) $account->balance <= 0) {
                    continue;
                }
                $accountCurrency = $account->currency;
                $accountRate = max((float) ($accountCurrency->exchange_rate ?? 1), 0.0000000001);
                $inBase = (float) $account->balance * $accountRate;
                if ($currency->id === $accountCurrency->id) {
                    $balance += (float) $account->balance;
                } elseif ($rate > 0) {
                    $balance += $inBase / $rate;
                }
            }
            $walletBalances[$currency->code] = [
                'code' => $currency->code,
                'name' => $currency->name ?? $currency->code,
                'symbol' => $currency->symbol,
                'decimals' => (int) ($currency->decimals ?? 2),
                'balance' => round($balance, $currency->decimals ?? 2),
            ];
        }

        return view('customer.home', [
            'user'             => $user,
            'accounts'         => $accounts,
            'activeAccountIdx' => (int) $request->cookie('bp_active_account', 0),
            'hideBalance'      => (bool) $request->cookie('bp_hide_balance', false),
            'walletBalances'   => $walletBalances,
            'walletCurrency'   => $request->cookie('bp_wallet_currency', $baseCode),
            'activeCard'       => $user->cards()->where('status', 'active')->latest('created_at')->first(),
            'inProgress'       => \App\Models\Transaction::where(function ($q) use ($accountIds) {
                $q->whereIn('from_account_id', $accountIds)->orWhereIn('to_account_id', $accountIds);
            })->whereIn('status', ['pending_review', 'pending_match'])
                ->orderByDesc('created_at')
                ->limit(3)
                ->get(),
            'recentActivity'   => \App\Models\Transaction::where(function ($q) use ($accountIds) {
                $q->whereIn('from_account_id', $accountIds)->orWhereIn('to_account_id', $accountIds);
            })->with('currency')->orderByDesc('created_at')->limit(5)->get(),
        ]);
    }
}
