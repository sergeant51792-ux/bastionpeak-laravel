<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Services\DepositService;

class DepositController extends Controller
{
    public function __construct(protected DepositService $deposits) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();
        $activeAccounts = $user->accounts()->where('status', 'active')->with('currency')->get();

        $depositMethods = collect();
        foreach ($activeAccounts as $account) {
            $methods = $account->depositMethods()
                ->wherePivot('is_active', true)
                ->get()
                ->map(function ($method) use ($account) {
                    $method->account_id = $account->id;
                    $method->account_name = $account->name;
                    $method->currency = $account->currency;
                    return $method;
                });
            $depositMethods = $depositMethods->merge($methods);
        }

        return view('customer.deposit', [
            'user'             => $user,
            'accounts'         => $activeAccounts,
            'depositMethods'   => $depositMethods->unique('id'),
            'idempotency'      => bin2hex(random_bytes(32)),
        ]);
    }

    public function submit(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'account_id'   => ['required', 'exists:accounts,id'],
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'deposit_method'  => ['required', 'string', 'max:100'],
            'note'         => ['nullable', 'string', 'max:500'],
            'proof'        => ['nullable', 'file', 'max:5120'],
            'idempotency'  => ['required', 'string', 'size:64'],
        ]);

        $account = Account::where('id', $validated['account_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('uploads/proofs', 'public');
        }

        $this->deposits->submitRequest(
            userId: $user->id,
            accountId: $account->id,
            amount: (float) $validated['amount'],
            note: $validated['deposit_method'] . ' - ' . ($validated['note'] ?? 'Deposit request'),
            proofFile: $proofPath,
            depositMethod: $validated['deposit_method'] ?? null,
            metadata: [
                'deposit_method_slug' => $validated['deposit_method'],
                'user_note' => $validated['note'] ?? null,
                'proof_file' => $proofPath,
            ],
        );

        return redirect()->route('deposit', ['status' => 'submitted'])->with('status', 'Deposit request submitted. It will be reviewed before crediting your account.');
    }
}
