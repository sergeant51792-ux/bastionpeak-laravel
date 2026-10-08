<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\User;
use App\Models\TransactionLeg;
use App\Services\LedgerService;

class TransactionController extends Controller
{
    public function __construct(protected LedgerService $ledger) {}

    public function index(Request $request): \Illuminate\View\View
    {
        $query = Transaction::with(['fromAccount', 'toAccount', 'fromUser', 'toUser', 'merchant', 'currency']);

        if ($request->filled('search')) {
            $query->where('transaction_id', 'like', "%{$request->search}%");
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('account_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_account_id', $request->account_id)
                  ->orWhere('to_account_id', $request->account_id);
            });
        }

        $transactions = $query->orderByDesc('created_at')->paginate(50);

        return view('admin.transactions', [
            'transactions' => $transactions,
            'filters'      => $request->only('search', 'type', 'status', 'account_id'),
        ]);
    }

    public function show(Transaction $transaction): \Illuminate\View\View
    {
        return view('admin.transaction-detail', [
            'transaction' => $transaction->load(['fromAccount', 'toAccount', 'fromUser', 'toUser', 'merchant', 'operator']),
        ]);
    }

    public function create(Request $request): \Illuminate\View\View
    {
        $user = $request->query('user') ? User::find($request->query('user')) : null;
        $accounts = $user ? $user->accounts()->with('currency')->get() : [];

        return view('admin.transaction-create', [
            'user'     => $user,
            'accounts' => $accounts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'           => ['nullable', 'exists:users,id'],
            'account_id'        => ['required', 'exists:accounts,id'],
            'type'              => ['required', 'string', 'max:50'],
            'amount'            => ['required', 'numeric', 'min:0'],
            'memo'              => ['nullable', 'string', 'max:500'],
            'reference'         => ['nullable', 'string', 'max:255'],
            'status'            => ['nullable', 'in:pending_review,pending_match,approved,rejected,credited,reversed,posted'],
            'idempotency_key'   => ['nullable', 'string', 'max:255'],
        ]);

        $account = Account::findOrFail($validated['account_id']);

        $transaction = DB::transaction(function () use ($account, $validated) {
            $status = $validated['status'] ?? 'posted';
            $isDebit = in_array($validated['type'], ['debit', 'payment_debit', 'transfer']);

            $transaction = Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(uniqid()),
                'from_account_id'   => $isDebit ? $account->id : null,
                'to_account_id'     => !$isDebit ? $account->id : null,
                'type'              => $validated['type'],
                'amount'            => $validated['amount'],
                'currency_id'       => $account->currency_id,
                'fee'               => 0,
                'net_amount'        => $validated['amount'],
                'status'            => $status,
                'memo'              => $validated['memo'] ?? null,
                'reference'         => $validated['reference'] ?? ('MNL-' . strtoupper(uniqid())),
                'idempotency_key'   => $validated['idempotency_key'] ?? bin2hex(random_bytes(8)),
                'operator_id'       => Auth::id(),
                'operator_type'     => Auth::getProvider() ? get_class(Auth::user()) : null,
                'credited_at'       => $status === 'posted' ? now() : null,
            ]);

            TransactionLeg::create([
                'transaction_id'  => $transaction->id,
                'account_id'      => $account->id,
                'side'            => $isDebit ? 'debit' : 'credit',
                'amount'          => $validated['amount'],
                'currency_id'     => $account->currency_id,
            ]);

            if ($isDebit) {
                $account->decrement('balance', $validated['amount']);
            } else {
                $account->increment('balance', $validated['amount']);
            }

            return $transaction;
        });

        return redirect()->route('admin.users.show', $account->user_id ?? User::find($validated['user_id'])->id ?? 1)->with('status', 'Transaction created successfully.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        $transactionId = $transaction->id;

        DB::transaction(function () use ($transaction) {
            $fromAccount = $transaction->fromAccount;
            $toAccount = $transaction->toAccount;
            $amount = $transaction->amount;

            if ($fromAccount) {
                $fromAccount->increment('balance', $amount);
            }
            if ($toAccount) {
                $toAccount->decrement('balance', $amount);
            }

            TransactionLeg::where('transaction_id', $transaction->id)->delete();
            $transaction->delete();
        });

        return back()->with('status', 'Transaction deleted and balances reverted.');
    }

    public function reverse(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $this->ledger->createReversal(
            transactionId: $transaction->id,
            operatorId: Auth::id(),
            reason: $validated['reason']
        );

        return back()->with('status', 'Transaction reversed.');
    }

    public function export(Request $request)
    {
        return response('Export handled by LedgerService', 501);
    }
}
