<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
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
        abort_if(! $user instanceof User, 403);

        $selectedMethod = $request->query('method', 'wire');

        return view('customer.pay', [
            'user'            => $user,
            'accounts'        => $user->accounts()->where('status', 'active')->with('currency')->get(),
            'debitAccount'    => $this->resolveDebitAccount($user, $request),
            'idempotency'     => bin2hex(random_bytes(32)),
            'selectedMethod'  => $selectedMethod,
            'transferBlocked'      => $user->isTransferBlocked(),
            'transferBlockMessage' => $user->transfer_block_reason,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user instanceof User, 403);

        if ($user->isTransferBlocked()) {
            // Send the customer back to the transfer form, where the admin's
            // message is shown. A bare 403 page would hide it entirely.
            return redirect()
                ->route('pay')
                ->with('transfer_blocked', true);
        }

        $validated = $request->validate([
            'from_account_id' => ['nullable', 'integer'],
            'payment_method'  => ['required', 'string', 'max:100'],
            'recipient_name'  => ['required', 'string', 'max:255'],
            'recipient_detail' => ['nullable', 'string', 'max:1000'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'purpose'         => ['nullable', 'string', 'max:500'],
            'proof'           => ['nullable', 'file', 'max:5120'],
            'idempotency'     => ['required', 'string', 'size:64'],
            'metadata'        => ['nullable', 'array'],
        ]);

        // Persist any proof now so it survives the OTP round-trip.
        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $this->storeUploadedFile($request->file('proof'), 'uploads/proofs');
        }
        $validated['proof_path'] = $proofPath;

        // An authenticator code is required before a transfer can be sent.
        if ($user->hasTotp() && ! TwoFactorController::isApproved('transfer')) {
            return TwoFactorController::requireOtp($request, 'transfer', $validated);
        }

        return $this->processStashedTransfer($request->merge($validated));
    }

    /**
     * Send the verified transfer. Called either directly (no authenticator) or
     * from the OTP challenge once the code has been confirmed.
     */
    public function processStashedTransfer(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user instanceof User, 403);

        $data = [
            'from_account_id'   => $request->input('from_account_id'),
            'payment_method'    => $request->input('payment_method'),
            'recipient_name'    => $request->input('recipient_name'),
            'recipient_detail'  => $request->input('recipient_detail'),
            'amount'            => $request->input('amount'),
            'purpose'           => $request->input('purpose'),
            'idempotency'       => $request->input('idempotency'),
            'metadata'          => $request->input('metadata'),
            'proof_path'        => $request->input('proof_path'),
        ];

        $validator = validator($data, [
            'payment_method'  => ['required', 'string', 'max:100'],
            'recipient_name'  => ['required', 'string', 'max:255'],
            'recipient_detail' => ['nullable', 'string', 'max:1000'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'purpose'         => ['nullable', 'string', 'max:500'],
            'idempotency'     => ['required', 'string', 'size:64'],
            'metadata'        => ['nullable', 'array'],
            'proof_path'      => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('pay')->withErrors($validator)->withInput();
        }

        $account = $this->resolveDebitAccount($user, $request, isset($data['from_account_id']) ? (int) $data['from_account_id'] : null);

        if (! $account) {
            return back()->withErrors([
                'amount' => 'No active account is available to debit. Please contact support.',
            ])->withInput();
        }

        $this->payments->submitRequest(
            userId: $user->id,
            fromAccountId: $account->id,
            toMerchantId: null,
            amount: (float) $data['amount'],
            purpose: $data['purpose'] ?? $data['payment_method'] . ' payment',
            proofFile: $data['proof_path'] ?? null,
            paymentMethod: $data['payment_method'],
            recipientName: $data['recipient_name'],
            recipientDetail: $data['recipient_detail'],
            metadata: $request->input('metadata'),
            idempotencyKey: $data['idempotency'],
        );

        return redirect()->route('pay', ['status' => 'submitted'])->with('status', 'Transfer request submitted successfully.');
    }

    /**
     * Resolve the account a transfer will be debited from.
     *
     * Uses an explicitly submitted account when it is valid, otherwise the
     * account the customer currently has active, falling back to their first
     * active account.
     */
    protected function resolveDebitAccount(User $user, Request $request, ?int $preferredAccountId = null): ?Account
    {
        if ($preferredAccountId) {
            $preferred = $user->accounts()
                ->where('id', $preferredAccountId)
                ->where('status', 'active')
                ->with('currency')
                ->first();

            if ($preferred) {
                return $preferred;
            }
        }

        $accounts = $user->accounts()->with('currency')->orderBy('type')->orderBy('created_at')->get();
        $activeAccounts = $accounts->where('status', 'active');

        if ($activeAccounts->isEmpty()) {
            return null;
        }

        $chosen = $accounts->get((int) $request->cookie('bp_active_account', 0));

        return ($chosen && $chosen->status === 'active') ? $chosen : $activeAccounts->first();
    }
}
