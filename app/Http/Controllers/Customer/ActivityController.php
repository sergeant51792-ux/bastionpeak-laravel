<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Services\NotificationService;

class ActivityController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();
        $accountIds = $user->accounts()->pluck('accounts.id');

        $query = Transaction::where(function ($q) use ($accountIds) {
            $q->whereIn('from_account_id', $accountIds)->orWhereIn('to_account_id', $accountIds);
        })->with(['fromAccount', 'toAccount', 'merchant', 'currency']);

        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter === 'money_in') {
                $query->whereIn('to_account_id', $accountIds)->whereIn('type', ['credit', 'deposit_credit', 'refund']);
            } elseif ($filter === 'money_out') {
                $query->whereIn('from_account_id', $accountIds)->whereIn('type', ['debit', 'payment_debit', 'adjustment']);
            } elseif ($filter === 'in_review') {
                $query->whereIn('status', ['pending_review', 'pending_match']);
            }
        }

        if ($request->filled('account_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_account_id', $request->account_id)
                  ->orWhere('to_account_id', $request->account_id);
            });
        }

        $transactions = $query->orderByDesc('created_at')->paginate(20);

        return view('customer.activity', [
            'transactions' => $transactions,
            'filter'       => $request->filter ?? 'all',
            'accountFilter' => $request->account_id ?? null,
            'accounts'     => $user->accounts()->with('currency')->get(),
        ]);
    }
}
