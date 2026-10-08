<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PaymentService;

class PayController extends Controller
{
    public function __construct(protected PaymentService $payments) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();
        $selectedMethod = $request->query('method', 'wire');

        return view('customer.pay', [
            'user'       => $user,
            'accounts'   => $user->accounts()->where('status', 'active')->with('currency')->get(),
            'idempotency' => bin2hex(random_bytes(32)),
            'selectedMethod' => $selectedMethod,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'from_account_id' => ['required', 'exists:accounts,id'],
            'payment_method'  => ['required', 'string', 'max:100'],
            'recipient_name'  => ['required', 'string', 'max:255'],
            'recipient_detail' => ['nullable', 'string', 'max:1000'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'purpose'         => ['nullable', 'string', 'max:500'],
            'proof'           => ['nullable', 'file', 'max:5120'],
            'idempotency'     => ['required', 'string', 'size:64'],
            'metadata'        => ['nullable', 'array'],
        ]);

        $account = \App\Models\Account::where('id', $validated['from_account_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('uploads/proofs', 'public');
        }

        $this->payments->submitRequest(
            userId: $user->id,
            fromAccountId: $account->id,
            toMerchantId: null,
            amount: (float) $validated['amount'],
            purpose: $validated['purpose'] ?? $validated['payment_method'] . ' payment',
            proofFile: $proofPath,
            paymentMethod: $validated['payment_method'],
            recipientName: $validated['recipient_name'],
            recipientDetail: $validated['recipient_detail'],
            metadata: $request->input('metadata'),
        );

        return redirect()->route('transfer', ['status' => 'submitted'])->with('status', 'Payment request submitted. It will be reviewed before processing.');
    }
}
