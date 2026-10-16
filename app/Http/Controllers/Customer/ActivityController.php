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
        })->whereNotIn('status', ['rejected', 'reversed'])
          ->with(['fromAccount', 'toAccount', 'merchant', 'currency']);

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

    /**
     * Printable receipt for a single transaction. The status shown is read from
     * the live record, so a pending receipt becomes credited / debited on its own.
     */
    public function receipt(Request $request, Transaction $transaction): \Illuminate\View\View
    {
        return $this->loadForReceipt($transaction);
    }

    /**
     * Download the receipt as a PDF file.
     */
    public function downloadReceipt(Request $request, Transaction $transaction): \Illuminate\Http\Response
    {
        $viewData = $this->loadForReceipt($transaction);

        $pdf = \PDF::loadView('customer.receipt-pdf', $viewData->getData());

        $reference = $transaction->transaction_id ?? 'TXN-' . strtoupper(substr((string) $transaction->id, 0, 8));
        $filename = 'receipt_' . $reference . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate a statement of account PDF for a date range.
     */
    public function statement(Request $request): \Illuminate\Http\Response
    {
        $user = Auth::user();
        $accountIds = $user->accounts()->pluck('accounts.id');

        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'account_id' => ['nullable', 'integer'],
        ]);

        $query = Transaction::where(function ($q) use ($accountIds) {
            $q->whereIn('from_account_id', $accountIds)->orWhereIn('to_account_id', $accountIds);
        });

        if ($request->filled('account_id') && $request->integer('account_id') > 0) {
            $accountId = $request->integer('account_id');
            if ($accountIds->contains($accountId)) {
                $query->where(function ($q) use ($accountId) {
                    $q->where('from_account_id', $accountId)->orWhere('to_account_id', $accountId);
                });
            }
        }

        $transactions = $query
            ->whereBetween('created_at', [$validated['start_date'], $validated['end_date']])
            ->whereNotIn('status', ['reversed'])
            ->with(['fromAccount', 'toAccount', 'currency'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalDeposits = 0.0;
        $totalWithdrawals = 0.0;
        $openingBalance = 0.0;
        $closingBalance = 0.0;

        foreach ($transactions as $txn) {
            $decimals = $txn->currency?->decimals ?? 2;
            $amount = (float) $txn->amount;
            $isDeposit = in_array($txn->type, [
                \App\Enums\TransactionType::DepositCredit,
                \App\Enums\TransactionType::Credit,
                \App\Enums\TransactionType::Refund,
            ], true);

            if ($isDeposit) {
                $totalDeposits += $amount;
            } else {
                $totalWithdrawals += $amount;
            }
            $closingBalance += $isDeposit ? $amount : -$amount;
        }

        $accounts = $user->accounts()->with('currency')->get();

        return \PDF::loadView('customer.statement', [
            'user'            => $user,
            'transactions'    => $transactions,
            'startDate'       => $validated['start_date'],
            'endDate'         => $validated['end_date'],
            'accounts'        => $accounts,
            'totalDeposits'   => $totalDeposits,
            'totalWithdrawals'=> $totalWithdrawals,
            'closingBalance'  => $closingBalance,
            'generatedAt'     => now(),
        ])->download('statement_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Shared logic: authorise access, eager-load relations, and prepare the
     * view data for both the on-screen receipt and the PDF download.
     */
    protected function loadForReceipt(Transaction $transaction): \Illuminate\View\View
    {
        $user = Auth::user();
        $accountIds = $user->accounts()->pluck('accounts.id');

        // Only the owner of either side of the transaction may open the receipt.
        $ownsTransaction = ($transaction->from_account_id && $accountIds->contains($transaction->from_account_id))
            || ($transaction->to_account_id && $accountIds->contains($transaction->to_account_id));

        abort_unless($ownsTransaction, 403);

        $transaction->load([
            'fromAccount', 'fromAccount.currency',
            'toAccount', 'toAccount.currency',
            'currency', 'merchant',
        ]);

        return view('customer.receipt', [
            'transaction'  => $transaction,
            'isIncoming'   => $transaction->isIncoming(),
            'metadata'     => $transaction->metadata ?? [],
            'issuedAt'     => now(),
        ]);
    }
}
