<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionLeg;
use App\Models\Message;
use App\Models\AuditLog;
use App\Services\NotificationService;
use App\Services\LedgerService;

class UserController extends Controller
{
    public function __construct(protected NotificationService $notifications, protected LedgerService $ledger) {}

    public function index(Request $request): \Illuminate\View\View
    {
        $query = User::whereHas('roles', fn ($q) => $q->where('name', 'Customer'));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->with('accounts.currency')->orderByDesc('created_at')->paginate(20);

        return view('admin.users', [
            'users' => $users,
            'filters'   => $request->only('search', 'status'),
        ]);
    }

    public function show(Request $request, User $user): \Illuminate\View\View
    {
        $tab = $request->query('tab', 'overview');
        $accountIds = $user->accounts()->pluck('accounts.id');

        return view('admin.user-detail', [
            'user'         => $user,
            'tab'          => $tab,
            'accounts'     => $user->accounts()->with('currency')->get(),
            'transactions' => Transaction::where(function ($q) use ($accountIds) {
                $q->whereIn('from_account_id', $accountIds)->orWhereIn('to_account_id', $accountIds);
            })->with('currency')->orderByDesc('created_at')->limit(50)->get(),
            'requests'     => Transaction::where(function ($q) use ($accountIds) {
                $q->whereIn('from_account_id', $accountIds)->orWhereIn('to_account_id', $accountIds);
            })->whereIn('status', ['pending_review', 'pending_match'])
                ->with('currency')
                ->get(),
            'messages'     => Message::where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            })->orderByDesc('created_at')->limit(20)->get(),
            'activityLog'  => \App\Models\AuditLog::where('target_type', 'user')
                ->where('target_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get(),
        ]);
    }

    public function accounts(Request $request, User $user): \Illuminate\View\View
    {
        return view('admin.user-detail', [
            'user'      => $user,
            'tab'       => 'accounts',
            'accounts'  => $user->accounts()->with('currency')->get(),
        ]);
    }

    public function transactions(Request $request, User $user): \Illuminate\View\View
    {
        $accountIds = $user->accounts()->pluck('accounts.id');
        $transactions = Transaction::where(function ($q) use ($accountIds) {
            $q->whereIn('from_account_id', $accountIds)->orWhereIn('to_account_id', $accountIds);
        })->with('currency')->orderByDesc('created_at')->paginate(20);

        return view('admin.user-detail', [
            'user'        => $user,
            'tab'         => 'transactions',
            'transactions' => $transactions,
        ]);
    }

    public function updateUser(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'                 => ['nullable', 'string', 'max:30'],
            'address'               => ['nullable', 'string', 'max:255'],
            'city'                  => ['nullable', 'string', 'max:100'],
            'state'                 => ['nullable', 'string', 'max:100'],
            'country'               => ['nullable', 'string', 'max:100'],
            'postal_code'           => ['nullable', 'string', 'max:20'],
            'status'                => ['required', 'in:active,frozen,locked,closed'],
        ]);

        $user->update($validated);

        return back()->with('status', 'User updated successfully.');
    }

    public function block(Request $request, User $user): RedirectResponse
    {
        $user->update(['status' => 'locked']);
        AuditLog::create([
            'admin_id' => Auth::id(),
            'action' => 'user_block',
            'target_type' => 'user',
            'target_id' => $user->id,
            'new_value' => ['reason' => $request->input('reason', 'Blocked by admin')],
        ]);
        $this->notifications->sendToUser(
            $user->id,
            'security_alert',
            'Account blocked',
            'Your account has been blocked by an administrator.'
        );
        return back()->with('status', 'User account blocked.');
    }

    public function freeze(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update(['status' => 'frozen']);
        AuditLog::create([
            'admin_id' => Auth::id(),
            'action' => 'user_freeze',
            'target_type' => 'user',
            'target_id' => $user->id,
            'new_value' => ['reason' => $validated['reason'] ?? 'Frozen by admin'],
        ]);
        $this->notifications->sendToUser(
            $user->id,
            'security_alert',
            'Account frozen',
            'Your account has been temporarily frozen.'
        );
        return back()->with('status', 'User account frozen.');
    }

    public function changePassword(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ]);

        if ($validated['password'] !== $validated['password_confirmation']) {
            return back()->withErrors(['password' => 'Passwords do not match.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'Password updated successfully.');
    }

    public function sendMessage(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'subject'  => ['required', 'string', 'max:255'],
            'body'     => ['required', 'string', 'max:5000'],
            'thread_id' => ['nullable', 'string'],
        ]);

        $threadId = $validated['thread_id'] ?? $this->findOrCreateThread(Auth::id(), $user->id);

        Message::create([
            'thread_id'    => $threadId,
            'sender_id'    => Auth::id(),
            'recipient_id' => $user->id,
            'subject'      => $validated['subject'],
            'body'         => $validated['body'],
        ]);

        $this->notifications->sendToUser(
            $user->id,
            'admin_message',
            $validated['subject'],
            $validated['body'],
            ['in_app', 'email']
        );

        return back()->with('status', 'Message sent successfully.');
    }

    private function findOrCreateThread(int $senderId, int $recipientId): string
    {
        $existing = Message::where(function ($q) use ($senderId, $recipientId) {
            $q->where(function ($q2) use ($senderId, $recipientId) {
                $q2->where('sender_id', $senderId)->where('recipient_id', $recipientId);
            })->orWhere(function ($q2) use ($senderId, $recipientId) {
                $q2->where('sender_id', $recipientId)->where('recipient_id', $senderId);
            });
        })->value('thread_id');

        if ($existing) {
            return $existing;
        }

        return 'admin-msg-' . min($senderId, $recipientId) . '-' . max($senderId, $recipientId) . '-' . bin2hex(random_bytes(4));
    }

    public function updateTransaction(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'amount'     => ['nullable', 'numeric', 'min:0'],
            'memo'       => ['nullable', 'string', 'max:500'],
            'status'     => ['nullable', 'string', 'in:pending_review,pending_match,approved,rejected,credited,reversed,posted'],
            'created_at' => ['nullable', 'date'],
        ]);

        $data = array_filter($validated, fn($v) => $v !== null && $v !== '');

        if (isset($data['created_at'])) {
            $data['created_at'] = $data['created_at'];
        }

        if (isset($data['amount']) && (float) $data['amount'] !== (float) $transaction->amount) {
            $oldAmount = (float) $transaction->amount;
            $newAmount = (float) $data['amount'];
            $diff = $newAmount - $oldAmount;

            DB::transaction(function () use ($transaction, $diff) {
                if ($transaction->to_account_id) {
                    $transaction->toAccount?->increment('balance', $diff);
                }
                if ($transaction->from_account_id) {
                    $transaction->fromAccount?->decrement('balance', $diff);
                }
            });
        }

        $transaction->update($data);

        if ($transaction->to_account_id && $transaction->toAccount?->user_id) {
            $this->notifications->sendToUser(
                $transaction->toAccount->user_id,
                'transaction_update',
                'Transaction updated',
                'Transaction ' . $transaction->transaction_id . ' has been processed.'
            );
        }
        if ($transaction->from_account_id && $transaction->fromAccount?->user_id && $transaction->fromAccount->user_id !== ($transaction->toAccount->user_id ?? null)) {
            $this->notifications->sendToUser(
                $transaction->fromAccount->user_id,
                'transaction_update',
                'Transaction updated',
                'Transaction ' . $transaction->transaction_id . ' has been processed.'
            );
        }

        return back()->with('status', 'Transaction updated successfully.');
    }

    public function updateAccountBalance(Request $request, Account $account): RedirectResponse
    {
        $validated = $request->validate([
            'balance' => ['required', 'numeric', 'min:0'],
            'name'    => ['nullable', 'string', 'max:255'],
        ]);

        $oldBalance = (float) $account->balance;
        $newBalance = (float) $validated['balance'];
        $diff = $newBalance - $oldBalance;

        $account->update([
            'balance' => $validated['balance'],
            'name'    => $validated['name'] ?? $account->name,
        ]);

        if (abs($diff) > 0.001) {
            \App\Models\Transaction::create([
                'transaction_id'    => 'ADJ-' . strtoupper(uniqid()),
                'from_account_id'   => $account->id,
                'to_account_id'     => $account->id,
                'type'              => $diff > 0 ? 'deposit_credit' : 'debit',
                'amount'            => abs($diff),
                'currency_id'       => $account->currency_id,
                'fee'               => 0,
                'net_amount'        => abs($diff),
                'status'            => 'posted',
                'memo'              => 'Balance adjustment: ' . ($diff > 0 ? 'Credit' : 'Debit') . ' of ' . abs($diff) . ' by admin',
                'reference'         => 'BAL-ADJ-' . strtoupper(uniqid()),
                'idempotency_key'   => 'bal-adj-' . $account->id . '-' . md5((string)time()),
                'operator_id'       => Auth::id(),
                'operator_type'     => Auth::getProvider() ? get_class(Auth::user()) : null,
                'credited_at'       => now(),
            ]);
        }

        return back()->with('status', 'Account balance updated successfully.');
    }

    public function credit(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'amount'     => ['required', 'numeric', 'min:0.01'],
            'reason'     => ['required', 'string', 'max:500'],
        ]);

        $account = Account::where('id', $validated['account_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        DB::transaction(function () use ($account, $validated) {
            $transaction = Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(uniqid()),
                'from_account_id'   => null,
                'to_account_id'     => $account->id,
                'type'              => 'credit',
                'amount'            => (float) $validated['amount'],
                'currency_id'       => $account->currency_id,
                'status'            => 'posted',
                'fee'               => 0,
                'net_amount'        => (float) $validated['amount'],
                'memo'              => 'Credit: ' . $validated['reason'],
                'reference'         => 'ADMIN-CREDIT-' . bin2hex(random_bytes(6)),
                'idempotency_key'   => 'admin-credit-' . $account->id . '-' . time() . '-' . uniqid(),
                'operator_id'       => Auth::id(),
                'operator_type'     => Auth::getProvider() ? get_class(Auth::user()) : null,
                'credited_at'       => now(),
            ]);

            TransactionLeg::create([
                'transaction_id'  => $transaction->id,
                'account_id'      => $account->id,
                'side'            => 'credit',
                'amount'          => (float) $validated['amount'],
                'currency_id'     => $account->currency_id,
            ]);

            $account->increment('balance', (float) $validated['amount']);
        });

        return back()->with('status', "Credited {$validated['amount']} to {$account->name}.");
    }

    public function debit(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'amount'     => ['required', 'numeric', 'min:0.01'],
            'reason'     => ['required', 'string', 'max:500'],
        ]);

        $account = Account::where('id', $validated['account_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (bccomp((string) $validated['amount'], (string) $account->balance, 18) > 0) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        DB::transaction(function () use ($account, $validated) {
            $transaction = Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(uniqid()),
                'from_account_id'   => $account->id,
                'to_account_id'     => null,
                'type'              => 'debit',
                'amount'            => (float) $validated['amount'],
                'currency_id'       => $account->currency_id,
                'status'            => 'posted',
                'fee'               => 0,
                'net_amount'        => (float) $validated['amount'],
                'memo'              => 'Debit: ' . $validated['reason'],
                'reference'         => 'ADMIN-DEBIT-' . bin2hex(random_bytes(6)),
                'idempotency_key'   => 'admin-debit-' . $account->id . '-' . time() . '-' . uniqid(),
                'operator_id'       => Auth::id(),
                'operator_type'     => Auth::getProvider() ? get_class(Auth::user()) : null,
            ]);

            TransactionLeg::create([
                'transaction_id'  => $transaction->id,
                'account_id'      => $account->id,
                'side'            => 'debit',
                'amount'          => (float) $validated['amount'],
                'currency_id'     => $account->currency_id,
            ]);

            $account->decrement('balance', (float) $validated['amount']);
        });

        return back()->with('status', "Debited {$validated['amount']} from {$account->name}.");
    }

    public function create(): \Illuminate\View\View
    {
        return view('admin.new-user', [
            'currencies' => \App\Models\Currency::all(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'password'              => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'type'                  => ['required', 'in:main,sub'],
            'currency'              => ['required', 'string', 'max:3'],
            'opening_balance'       => ['nullable', 'numeric', 'min:0'],
            'per_transaction_cap'   => ['nullable', 'numeric', 'min:0'],
            'daily_cap'             => ['nullable', 'numeric', 'min:0'],
            'monthly_cap'           => ['nullable', 'numeric', 'min:0'],
            'balance_cap'           => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validated['password'] !== $validated['password_confirmation']) {
            return back()->withErrors(['password' => 'Passwords do not match.']);
        }

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => bcrypt($validated['password']),
            'status'   => 'active',
        ]);

        $user->assignRole('Customer');

        $currency = \App\Models\Currency::where('code', strtoupper($validated['currency']))->first();
        if (!$currency) {
            $currency = \App\Models\Currency::first();
        }

        $account = \App\Models\Account::create([
            'user_id'                => $user->id,
            'currency_id'            => $currency->id,
            'type'                   => $validated['type'],
            'name'                   => $validated['type'] === 'main' ? 'Main wallet' : 'Sub-account',
            'account_number'         => str_replace(' ', '', '10 ' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT) . ' ' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT) . ' ' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT)),
            'balance'                => (float) ($validated['opening_balance'] ?? 0),
            'per_transaction_cap'    => (float) ($validated['per_transaction_cap'] ?? 0),
            'daily_cap'              => (float) ($validated['daily_cap'] ?? 0),
            'monthly_cap'            => (float) ($validated['monthly_cap'] ?? 0),
            'balance_cap'            => (float) ($validated['balance_cap'] ?? 0),
            'status'                 => 'active',
            'opened_at'              => now(),
        ]);

        if ((float) ($validated['opening_balance'] ?? 0) > 0) {
            Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(uniqid()),
                'from_account_id'   => $account->id,
                'to_account_id'     => $account->id,
                'type'              => 'credit',
                'amount'            => (float) $validated['opening_balance'],
                'currency_id'       => $currency->id,
                'fee'               => 0,
                'net_amount'        => (float) $validated['opening_balance'],
                'status'            => 'posted',
                'memo'              => 'Opening balance',
                'reference'         => 'OPEN-' . strtoupper(uniqid()),
                'operator_id'       => Auth::id(),
            ]);
        }

        $this->notifications->sendToUser($user->id, 'welcome', 'Welcome to Bastion Peak', 'Your account has been created.');

        return redirect()->route('admin.users.index')->with('status', 'User created successfully.');
    }
}
