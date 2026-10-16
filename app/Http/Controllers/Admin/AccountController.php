<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Models\DepositMethod;

class AccountController extends Controller
{
    public function __construct(
        protected \App\Services\LedgerService      $ledger,
        protected \App\Services\NotificationService $notifications,
    ) {}

    public function show(Request $request, Account $account): \Illuminate\View\View
    {
        $account->load('currency', 'user');

        $accountDepositMethods = $account->depositMethods()
            ->withPivot(['address', 'qr_path', 'instructions', 'metadata', 'is_active'])
            ->get();

        $globalDepositMethods = DepositMethod::where('is_active', true)
            ->where('is_global', true)
            ->get();

        $depositMethods = $globalDepositMethods->merge($accountDepositMethods);

        return view('admin.account-detail', [
            'account'            => $account,
            'currency'           => $account->currency,
            'limits'             => $account->limits()->get(),
            'lockHistory'        => \App\Models\AuditLog::where('target_type', 'account')
                ->where('target_id', $account->id)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get(),
            'ledger'             => \App\Models\Transaction::where(function ($q) use ($account) {
                $q->where('from_account_id', $account->id)->orWhere('to_account_id', $account->id);
            })->with('currency')->orderByDesc('created_at')->limit(50)->get(),
            'depositMethods'     => $depositMethods,
        ]);
    }

    public function updateDepositDetails(Request $request, Account $account): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'deposit_address' => ['nullable', 'string', 'max:500'],
            'deposit_instructions' => ['nullable', 'string', 'max:2000'],
            'deposit_qr' => ['nullable', 'file', 'max:2048'],
        ]);

        $data = [
            'deposit_address' => $validated['deposit_address'] ?? null,
            'deposit_instructions' => $validated['deposit_instructions'] ?? null,
        ];

        if ($request->hasFile('deposit_qr')) {
            if ($account->deposit_qr_path) {
                $fullPath = storage_path('app/public/' . $account->deposit_qr_path);
                if (is_file($fullPath)) { unlink($fullPath); }
            }
            $data['deposit_qr_path'] = $this->storeUploadedFile($request->file('deposit_qr'), 'deposit-qr');
        }

        $account->update($data);

        return back()->with('status', 'Deposit details updated successfully.');
    }

    public function updateStatus(Request $request, Account $account): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,frozen,locked,closed'],
            'notes'  => ['nullable', 'string'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($account, $validated) {
            $account->update([
                'status' => $validated['status'],
                'notes'  => $validated['notes'] ?? $account->notes,
            ]);
            \App\Models\AuditLog::create([
                'admin_id'    => Auth::id(),
                'action'      => 'account_status_changed',
                'target_type' => 'account',
                'target_id'   => $account->id,
                'old_value'   => ['status' => $account->getOriginal('status')],
                'new_value'   => ['status' => $validated['status']],
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        });

        return back()->with('status', 'Account status updated successfully.');
    }

    public function updateBalance(Request $request, Account $account): RedirectResponse
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
                'memo'              => 'Balance adjustment: ' . ($diff > 0 ? 'Credit' : 'Debit') . ' of ' . abs($diff) . ' via account settings',
                'reference'         => 'BAL-ADJ-' . strtoupper(uniqid()),
                'idempotency_key'   => 'bal-adj-' . $account->id . '-' . md5((string)time()),
                'operator_id'       => Auth::id(),
                'operator_type'     => \App\Models\User::class,
                'credited_at'       => now(),
            ]);
        }

        return back()->with('status', 'Account balance updated successfully.');
    }

    public function recalculate(Request $request, Account $account): RedirectResponse
    {
        $oldBalance = (float) $account->balance;

        $newBalance = $this->ledger->recalculateBalance($account->id);

        \App\Models\AuditLog::create([
            'admin_id'    => Auth::id(),
            'action'      => 'account_rebalanced',
            'target_type' => 'account',
            'target_id'   => $account->id,
            'old_value'   => ['balance' => $oldBalance],
            'new_value'   => ['balance' => $newBalance],
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return back()->with('status', 'Account balance recalculated from ledger: ' . $newBalance . ' ' . $account->currency->code);
    }

    public function updateLimits(Request $request, Account $account): RedirectResponse
    {
        $validated = $request->validate([
            'daily_cap'           => ['nullable', 'numeric', 'min:0'],
            'monthly_cap'         => ['nullable', 'numeric', 'min:0'],
            'balance_cap'         => ['nullable', 'numeric', 'min:0'],
        ]);

        $account->update([
            'per_transaction_cap' => $validated['per_transaction_cap'] ?? $account->per_transaction_cap,
            'daily_cap'           => $validated['daily_cap'] ?? $account->daily_cap,
            'monthly_cap'         => $validated['monthly_cap'] ?? $account->monthly_cap,
            'balance_cap'         => $validated['balance_cap'] ?? $account->balance_cap,
        ]);

        \App\Models\AuditLog::create([
            'admin_id'    => Auth::id(),
            'action'      => 'account_limits_updated',
            'target_type' => 'account',
            'target_id'   => $account->id,
            'old_value'   => ['caps' => $account->getOriginal('caps')],
            'new_value'   => ['caps' => $validated],
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return back()->with('status', 'Account limits updated successfully.');
    }
}
