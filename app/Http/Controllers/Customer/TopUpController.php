<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Services\DepositService;

class TopUpController extends Controller
{
    public function __construct(protected DepositService $deposits) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();

        return view('customer.top-up', [
            'user'        => $user,
            'accounts'    => $user->accounts()->where('status', 'active')->with('currency')->get(),
            'idempotency' => bin2hex(random_bytes(32)),
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'account_id'   => ['required', Rule::exists('accounts', 'id')->where('user_id', $user->id)->where('status', 'active')],
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'payment_method'  => ['required', 'in:wire,card,crypto,other'],
            'note'         => ['nullable', 'string', 'max:500'],
            'proof'        => ['nullable', 'file', 'max:5120'],
            'idempotency'  => ['required', 'string', 'size:64'],
        ]);

        $account = \App\Models\Account::findOrFail($validated['account_id']);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('uploads/proofs', 'public');
        }

        $this->deposits->submitRequest(
            userId: $user->id,
            accountId: $account->id,
            amount: (float) $validated['amount'],
            note: ($validated['payment_method'] ?? 'other') . ' - ' . ($validated['note'] ?? ''),
            proofFile: $proofPath,
            depositMethod: $validated['payment_method'] ?? null,
        );

        return redirect()->route('transfer')->with('status', 'Deposit request submitted. It will be reviewed before crediting your account.');
    }
}
