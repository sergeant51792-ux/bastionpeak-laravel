@extends('layouts.customer')

@section('title', 'Transfer')
@section('content')
<div class="space-y-6">
    <header class="page-header">
        <a href="{{ route('transfer') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <h1 class="text-xl font-semibold">Send money</h1>
    </header>

    <form method="POST" action="{{ route('pay.submit') }}" enctype="multipart/form-data" class="space-y-5" id="payment-form">
        @csrf
        <input type="hidden" name="idempotency" value="{{ $idempotency }}">
        <input type="hidden" id="selected_method" name="payment_method" value="{{ $selectedMethod }}">

        <div>
            <label for="from_account_id" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Debit account</label>
            <x-custom-select
                name="from_account_id"
                :options="$accounts->mapWithKeys(fn($a) => [$a->id => $a->name . ' (' . $a->currency->symbol . ')' ])"
                placeholder="Select account"
                required
            />
        </div>

        <div>
            <label class="block text-sm font-semibold text-[var(--text)] mb-1.5">Transfer method</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3">
                <button type="button" onclick="selectMethod('wire')" data-method="wire" class="method-btn py-3 px-4 rounded-xl border text-center transition-all {{ $selectedMethod === 'wire' ? 'border-[var(--accent)] bg-[var(--accent-subtle)] text-[var(--accent)]' : 'border-[var(--line)] text-[var(--text-muted)] hover:border-[var(--accent)] hover:bg-[var(--accent-subtle)] hover:text-[var(--accent)]' }}">
                    Wire transfer
                </button>
                <button type="button" onclick="selectMethod('crypto')" data-method="crypto" class="method-btn py-3 px-4 rounded-xl border text-center transition-all {{ $selectedMethod === 'crypto' ? 'border-[var(--accent)] bg-[var(--accent-subtle)] text-[var(--accent)]' : 'border-[var(--line)] text-[var(--text-muted)] hover:border-[var(--accent)] hover:bg-[var(--accent-subtle)] hover:text-[var(--accent)]' }}">
                    Cryptocurrency
                </button>
                <button type="button" onclick="selectMethod('swift')" data-method="swift" class="method-btn py-3 px-4 rounded-xl border text-center transition-all {{ $selectedMethod === 'swift' ? 'border-[var(--accent)] bg-[var(--accent-subtle)] text-[var(--accent)]' : 'border-[var(--line)] text-[var(--text-muted)] hover:border-[var(--accent)] hover:bg-[var(--accent-subtle)] hover:text-[var(--accent)]' }}">
                    SWIFT / UK & EU modes
                </button>
            </div>
        </div>

        <div id="method-form-wire" class="method-form {{ $selectedMethod === 'wire' ? 'block' : 'hidden' }} space-y-4">
            <div class="bg-[var(--surface-raised)] rounded-xl p-4">
                <h3 class="text-sm font-semibold text-[var(--text)] mb-3">Wire transfer details</h3>
                <div class="space-y-4">
                    <div>
                        <label for="recipient_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Beneficiary name</label>
                        <input type="text" id="recipient_name" name="recipient_name" maxlength="255" class="input-field" placeholder="Full name of beneficiary">
                    </div>
                    <div>
                        <label for="bank_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Bank name</label>
                        <input type="text" id="bank_name" name="metadata[bank_name]" maxlength="255" class="input-field" placeholder="Receiving bank name">
                    </div>
                    <div>
                        <label for="routing_number" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Routing number</label>
                        <input type="text" id="routing_number" name="metadata[routing_number]" maxlength="50" class="input-field" placeholder="ABA / CHIPS routing number">
                    </div>
                    <div>
                        <label for="account_number" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Account number</label>
                        <input type="text" id="account_number" name="metadata[account_number]" maxlength="50" class="input-field" placeholder="Recipient bank account number">
                    </div>
                    <div>
                        <label for="swift_code" class="block text-sm font-semibold text-[var(--text)] mb-1.5">SWIFT/BIC code <span class="text-[var(--text-muted)] font-normal">(optional)</span></label>
                        <input type="text" id="swift_code" name="metadata[swift_code]" maxlength="20" class="input-field" placeholder="SWIFT/BIC code if applicable">
                    </div>
                    <div>
                        <label for="recipient_detail" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Recipient details</label>
                        <textarea id="recipient_detail" name="recipient_detail" rows="2" maxlength="1000" class="input-field" placeholder="Full beneficiary address, bank address, purpose of payment"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div id="method-form-crypto" class="method-form {{ $selectedMethod === 'crypto' ? 'block' : 'hidden' }} space-y-4">
            <div class="bg-[var(--surface-raised)] rounded-xl p-4">
                <h3 class="text-sm font-semibold text-[var(--text)] mb-3">Cryptocurrency transfer</h3>
                <div class="space-y-4">
                    <div>
                        <label for="crypto_currency" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Cryptocurrency</label>
                        <x-custom-select
                            id="crypto_currency"
                            name="metadata[crypto_currency]"
                            :options="[
                                'USDT' => 'USDT (Tether)',
                                'BTC' => 'BTC (Bitcoin)',
                                'ETH' => 'ETH (Ethereum)',
                                'USDC' => 'USDC (USD Coin)',
                                'XRP' => 'XRP (Ripple)',
                                'ADA' => 'ADA (Cardano)',
                            ]"
                            placeholder="Select cryptocurrency"
                            required
                        />
                    </div>
                    <div>
                        <label for="network" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Network</label>
                        <x-custom-select
                            id="network"
                            name="metadata[network]"
                            :options="[
                                'TRC20' => 'TRC20 (Tron)',
                                'ERC20' => 'ERC20 (Ethereum)',
                                'BEP20' => 'BEP20 (BNB Smart Chain)',
                                'Bitcoin' => 'Bitcoin Network',
                                'Ethereum' => 'Ethereum Network',
                            ]"
                            placeholder="Select network"
                            required
                        />
                    </div>
                    <div>
                        <label for="wallet_address" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Recipient wallet address</label>
                        <input type="text" id="wallet_address" name="metadata[wallet_address]" required maxlength="200" class="input-field" placeholder="Recipient cryptocurrency wallet address">
                    </div>
                    <div>
                        <label for="recipient_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Recipient name</label>
                        <input type="text" id="recipient_name" name="recipient_name" required maxlength="255" class="input-field" placeholder="Full name of beneficiary">
                    </div>
                    <div>
                        <label for="recipient_detail" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Notes <span class="text-[var(--text-muted)] font-normal">(optional)</span></label>
                        <textarea id="recipient_detail" name="recipient_detail" rows="2" maxlength="1000" class="input-field" placeholder="Additional details or memo"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div id="method-form-swift" class="method-form {{ $selectedMethod === 'swift' ? 'block' : 'hidden' }} space-y-4">
            <div class="bg-[var(--surface-raised)] rounded-xl p-4">
                <h3 class="text-sm font-semibold text-[var(--text)] mb-3">SWIFT / International transfer</h3>
                <div class="space-y-4">
                    <div>
                        <label for="recipient_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Beneficiary name</label>
                        <input type="text" id="recipient_name" name="recipient_name" required maxlength="255" class="input-field" placeholder="Full name of beneficiary">
                    </div>
                    <div>
                        <label for="bank_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Bank name</label>
                        <input type="text" id="bank_name" name="metadata[bank_name]" required maxlength="255" class="input-field" placeholder="Receiving bank name">
                    </div>
                    <div>
                        <label for="swift_code" class="block text-sm font-semibold text-[var(--text)] mb-1.5">SWIFT/BIC code</label>
                        <input type="text" id="swift_code" name="metadata[swift_code]" required maxlength="20" class="input-field" placeholder="SWIFT/BIC code">
                    </div>
                    <div>
                        <label for="iban" class="block text-sm font-semibold text-[var(--text)] mb-1.5">IBAN <span class="text-[var(--text-muted)] font-normal">(EUR accounts)</span></label>
                        <input type="text" id="iban" name="metadata[iban]" maxlength="50" class="input-field" placeholder="IBAN (for SEPA transfers)">
                    </div>
                    <div>
                        <label for="account_number" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Account number / Sort code</label>
                        <input type="text" id="account_number" name="metadata[account_number]" required maxlength="50" class="input-field" placeholder="Account number and/or sort code">
                    </div>
                    <div>
                        <label for="recipient_detail" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Beneficiary address</label>
                        <textarea id="recipient_detail" name="recipient_detail" rows="2" required maxlength="1000" class="input-field" placeholder="Full beneficiary address"></textarea>
                    </div>
                    <div>
                        <label for="purpose" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Purpose of payment</label>
                        <input type="text" id="purpose" name="purpose" required maxlength="500" class="input-field" placeholder="e.g. Invoice payment, investment, etc.">
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label for="amount" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Amount</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--text-muted)] text-sm">$</span>
                <input type="text" inputmode="decimal" id="amount" name="amount" required
                       class="input-field pl-8 pr-4 py-3"
                       placeholder="0.00">
            </div>
        </div>

        <div>
            <label for="proof" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Supporting document <span class="text-[var(--text-muted)] font-normal">(optional)</span></label>
            <div class="border-2 border-dashed border-[var(--line)] rounded-xl p-6 text-center hover:border-[var(--accent)] transition-colors">
                <x-icon name="file-text" class="w-8 h-8 mx-auto mb-2 text-[var(--text-muted)]" />
                <p class="text-sm text-[var(--text-muted)]">Take photo or Choose file</p>
                <p class="text-xs text-[var(--text-muted)] mt-1">JPG, PNG or PDF, up to 5 MB</p>
                <input type="file" name="proof" accept="image/jpeg,image/jpg,image/png,application/pdf,.jpg,.jpeg,.png,.pdf" class="mt-3 text-sm">
            </div>
        </div>

        @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <button type="submit" class="w-full py-3.5 btn-primary">Submit transfer request</button>
    </form>
</div>

<script>
function selectMethod(method) {
    document.getElementById('selected_method').value = method;
    document.querySelectorAll('.method-form').forEach(form => {
        form.classList.add('hidden');
        form.classList.remove('block');
    });
    document.querySelectorAll('.method-btn').forEach(btn => {
        btn.classList.remove('border-[var(--accent)]', 'bg-[var(--accent-subtle)]', 'text-[var(--accent)]');
        btn.classList.add('border-[var(--line)]', 'text-[var(--text-muted)]');
    });
    document.querySelector(`.method-form#method-form-${method}`).classList.remove('hidden');
    document.querySelector(`.method-form#method-form-${method}`).classList.add('block');
    document.querySelector(`[data-method="${method}"]`).classList.remove('border-[var(--line)]', 'text-[var(--text-muted)]');
    document.querySelector(`[data-method="${method}"]`).classList.add('border-[var(--accent)]', 'bg-[var(--accent-subtle)]', 'text-[var(--accent)]');
}
</script>
@endsection
