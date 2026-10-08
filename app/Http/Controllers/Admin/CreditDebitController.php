<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LedgerService;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionLeg;
use Illuminate\Support\Facades\DB;

class CreditDebitController extends Controller
{
    public function __construct(protected LedgerService $ledger) {}

    public function credit(Request $request, Account $account): RedirectResponse
    {
        $validated = $request->validate([
            'amount'  => ['required', 'numeric', 'min:0.01'],
            'reason'  => ['required', 'string', 'max:500'],
            'proof'   => ['nullable', 'file', 'max:5120'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('uploads/proofs', 'public');
        }

        DB::transaction(function () use ($account, $validated, $proofPath) {
            $transaction = Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(uniqid()),
                'from_account_id'   => null,
                'to_account_id'     => $account->id,
                'type'              => 'credit',
                'amount'            => $validated['amount'],
                'currency_id'       => $account->currency_id,
                'status'            => 'posted',
                'fee'               => 0,
                'net_amount'        => $validated['amount'],
                'memo'              => 'Credit: ' . $validated['reason'],
                'reference'         => 'ADMIN-CREDIT-' . bin2hex(random_bytes(6)),
                'idempotency_key'   => 'admin-credit-' . $account->id . '-' . time(),
                'operator_id'       => Auth::id(),
                'operator_type'     => Auth::getProvider() ? get_class(Auth::user()) : null,
                'proof_file_path'   => $proofPath,
                'credited_at'       => now(),
            ]);

            TransactionLeg::create([
                'transaction_id'  => $transaction->id,
                'account_id'      => $account->id,
                'side'            => 'credit',
                'amount'          => $validated['amount'],
                'currency_id'     => $account->currency_id,
            ]);

            $account->increment('balance', $validated['amount']);
        });

        return back()->with('status', "Credited {$validated['amount']} to {$account->name}.");
    }

    public function debit(Request $request, Account $account): RedirectResponse
    {
        $validated = $request->validate([
            'amount'  => ['required', 'numeric', 'min:0.01'],
            'reason'  => ['required', 'string', 'max:500'],
            'proof'   => ['nullable', 'file', 'max:5120'],
        ]);

        if (bccomp((string) $validated['amount'], (string) $account->balance, 18) > 0) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('uploads/proofs', 'public');
        }

        DB::transaction(function () use ($account, $validated, $proofPath) {
            $transaction = Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(uniqid()),
                'from_account_id'   => $account->id,
                'to_account_id'     => null,
                'type'              => 'debit',
                'amount'            => $validated['amount'],
                'currency_id'       => $account->currency_id,
                'status'            => 'posted',
                'fee'               => 0,
                'net_amount'        => $validated['amount'],
                'memo'              => 'Debit: ' . $validated['reason'],
                'reference'         => 'ADMIN-DEBIT-' . bin2hex(random_bytes(6)),
                'idempotency_key'   => 'admin-debit-' . $account->id . '-' . time(),
                'operator_id'       => Auth::id(),
                'operator_type'     => Auth::getProvider() ? get_class(Auth::user()) : null,
                'proof_file_path'   => $proofPath,
                'credited_at'       => now(),
            ]);

            TransactionLeg::create([
                'transaction_id'  => $transaction->id,
                'account_id'      => $account->id,
                'side'            => 'debit',
                'amount'          => $validated['amount'],
                'currency_id'     => $account->currency_id,
            ]);

            $account->decrement('balance', $validated['amount']);
        });

        return back()->with('status', "Debited {$validated['amount']} from {$account->name}.");
    }
}
