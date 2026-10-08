<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DepositService;
use App\Services\PaymentService;
use App\Services\LedgerService;
use App\Services\NotificationService;
use App\Models\Transaction;

class ApprovalController extends Controller
{
    public function __construct(
        protected DepositService    $deposits,
        protected PaymentService    $payments,
        protected NotificationService $notifications,
    ) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $tab = $request->query('tab', 'deposits');

        $query = Transaction::query();

        if ($tab === 'deposits') {
            $query->where('type', 'deposit_credit')
                ->whereIn('status', ['pending_review', 'pending_match']);
        } else {
            $query->where('type', 'payment_debit')
                ->where('status', 'pending_review');
        }

        $queue = $query->with(['fromUser', 'toUser', 'fromAccount', 'toAccount.depositMethods', 'currency'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $openId = $request->query('open');
        $openTransaction = null;

        if ($openId) {
            $openTransaction = Transaction::with(['fromUser', 'toUser', 'fromAccount', 'toAccount.depositMethods', 'merchant', 'currency'])
                ->findOrFail($openId);
        }

        return view('admin.approvals', [
            'queue'          => $queue,
            'tab'            => $tab,
            'openTransaction' => $openTransaction,
        ]);
    }

    public function approve(Request $request, Transaction $transaction): RedirectResponse
    {
        $operatorId = Auth::id();

        if ($transaction->type === TransactionType::DepositCredit) {
            $this->deposits->approve($transaction->id, $operatorId, []);
        } elseif ($transaction->type === TransactionType::PaymentDebit) {
            $this->payments->approve($transaction->id, $operatorId);
        }

        $typeLabel = $transaction->type instanceof \App\Enums\TransactionType ? $transaction->type->label() : ucfirst(str_replace('_', ' ', $transaction->type));
        return back()->with('status', $typeLabel . ' approved.');
    }

    public function reject(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $operatorId = Auth::id();

        if ($transaction->type === TransactionType::DepositCredit) {
            $this->deposits->reject($transaction->id, $operatorId, $validated['reason']);
        } elseif ($transaction->type === TransactionType::PaymentDebit) {
            $this->payments->reject($transaction->id, $operatorId, $validated['reason']);
        }

        $typeLabel = $transaction->type instanceof TransactionType ? $transaction->type->label() : ucfirst(str_replace('_', ' ', $transaction->type));
        return back()->with('status', $typeLabel . ' rejected.');
    }

    public function batchApprove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'     => ['required', 'array', 'min:1'],
            'ids.*'   => ['integer', 'exists:transactions,id'],
            'type'    => ['required', 'in:deposits,payments'],
        ]);

        if ($validated['type'] === 'deposits') {
            $this->deposits->batchApprove($validated['ids'], Auth::id());
        } else {
            foreach ($validated['ids'] as $id) {
                $txn = Transaction::findOrFail($id);
                $this->payments->approve($txn->id, Auth::id());
            }
        }

        return back()->with('status', count($validated['ids']) . ' requests approved.');
    }

    public function batchDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:transactions,id'],
        ]);

        $count = Transaction::whereIn('id', $validated['ids'])
            ->whereIn('status', ['pending_review', 'pending_match'])
            ->delete();

        return back()->with('status', $count . ' requests deleted.');
    }
}
