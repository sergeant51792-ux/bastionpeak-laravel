<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;

class TransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();
        $accountIds = $user->accounts()->pluck('accounts.id');

        return view('customer.transfer', [
            'user'         => $user,
            'recentTransfers' => \App\Models\Transaction::where(function ($q) use ($accountIds) {
                $q->whereIn('from_account_id', $accountIds)->orWhereIn('to_account_id', $accountIds);
            })->with('currency')->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ]);
    }
}
