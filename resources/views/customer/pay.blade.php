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

        <div data-account-field>
            <label for="from_account_id" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Debit account</label>
            <x-custom-select
                name="from_account_id"
                :options="$accounts->mapWithKeys(fn($a) => [$a->id => $a->name . ' (' . ($a->currency?->symbol ?? '') . ')' ])"
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
                        <input type="text" id="recipient_name" name="recipient_name" data-required maxlength="255" class="input-field" placeholder="Full name of beneficiary">
                    </div>
                    <div>
                        <label for="bank_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Bank name</label>
                        <input type="text" id="bank_name" name="metadata[bank_name]" data-required maxlength="255" class="input-field" placeholder="Receiving bank name">
                    </div>
                    <div>
                        <label for="routing_number" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Routing number</label>
                        <input type="text" id="routing_number" name="metadata[routing_number]" maxlength="50" class="input-field" placeholder="ABA / CHIPS routing number">
                    </div>
                    <div>
                        <label for="account_number" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Account number</label>
                        <input type="text" id="account_number" name="metadata[account_number]" data-required maxlength="50" class="input-field" placeholder="Recipient bank account number">
                    </div>
                    <div>
                        <label for="swift_code" class="block text-sm font-semibold text-[var(--text)] mb-1.5">SWIFT/BIC code <span class="text-[var(--text-muted)] font-normal">(optional)</span></label>
                        <input type="text" id="swift_code" name="metadata[swift_code]" maxlength="20" class="input-field" placeholder="SWIFT/BIC code if applicable">
                    </div>
                    <div>
                        <label for="recipient_detail" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Recipient details</label>
                        <textarea id="recipient_detail" name="recipient_detail" data-required rows="2" maxlength="1000" class="input-field" placeholder="Full beneficiary address, bank address, purpose of payment"></textarea>
                    </div>
                    <div>
                        <label for="purpose" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Purpose of payment</label>
                        <input type="text" id="purpose" name="purpose" data-required maxlength="500" class="input-field" placeholder="e.g. Invoice payment, investment, etc.">
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
                            data-required
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
                            data-required
                        />
                    </div>
                    <div>
                        <label for="wallet_address" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Recipient wallet address</label>
                        <input type="text" id="wallet_address" name="metadata[wallet_address]" data-required maxlength="200" class="input-field" placeholder="Recipient cryptocurrency wallet address">
                    </div>
                    <div>
                        <label for="recipient_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Recipient name</label>
                        <input type="text" id="recipient_name" name="recipient_name" data-required maxlength="255" class="input-field" placeholder="Full name of beneficiary">
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
                        <input type="text" id="bank_name" name="metadata[bank_name]" data-required maxlength="255" class="input-field" placeholder="Receiving bank name">
                    </div>
                    <div>
                        <label for="swift_code" class="block text-sm font-semibold text-[var(--text)] mb-1.5">SWIFT/BIC code</label>
                        <input type="text" id="swift_code" name="metadata[swift_code]" data-required maxlength="20" class="input-field" placeholder="SWIFT/BIC code">
                    </div>
                    <div>
                        <label for="iban" class="block text-sm font-semibold text-[var(--text)] mb-1.5">IBAN <span class="text-[var(--text-muted)] font-normal">(EUR accounts)</span></label>
                        <input type="text" id="iban" name="metadata[iban]" maxlength="50" class="input-field" placeholder="IBAN (for SEPA transfers)">
                    </div>
                    <div>
                        <label for="account_number" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Account number / Sort code</label>
                        <input type="text" id="account_number" name="metadata[account_number]" data-required maxlength="50" class="input-field" placeholder="Account number and/or sort code">
                    </div>
                    <div>
                        <label for="recipient_detail" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Beneficiary address</label>
                        <textarea id="recipient_detail" name="recipient_detail" data-required rows="2" maxlength="1000" class="input-field" placeholder="Full beneficiary address"></textarea>
                    </div>
                    <div>
                        <label for="purpose" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Purpose of payment</label>
                        <input type="text" id="purpose" name="purpose" data-required maxlength="500" class="input-field" placeholder="e.g. Invoice payment, investment, etc.">
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label for="amount" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Amount</label>
            <div class="input-symbol-prefix">
                <span class="symbol">$</span>
                <input type="text" inputmode="decimal" id="amount" name="amount" required
                       class="input-field pr-4 py-3"
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
            <div class="p-3 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)]">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mt-6">
            <button type="submit" id="submit-btn" class="w-full py-3.5 btn-primary disabled:opacity-60 disabled:cursor-not-allowed">Submit transfer request</button>
        </div>
    </form>
</div>

{{-- Processing overlay: thick green progress bar --}}
<div id="processing-overlay" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-3xl shadow-xl w-full max-w-sm p-8 text-center animate-scale-in">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-[var(--success-bg)] border-4 border-[var(--success)]/20 flex items-center justify-center">
            <svg class="w-10 h-10 text-[var(--success)] animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3.5"></circle>
                <path class="opacity-90" fill="currentColor" d="M12 3a9 9 0 0 1 9 9h-3.5A5.5 5.5 0 0 0 12 6.5V3z"></path>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-[var(--text)] mb-2">Processing transfer</h2>
        <p class="text-sm text-[var(--text-muted)] mb-6">Please do not close or refresh this page while we process your request.</p>
        <div class="bp-progress-track">
            <div class="bp-progress-fill"></div>
        </div>
        <p class="text-xs text-[var(--text-muted)] mt-3">Securely transmitting your request</p>
    </div>
</div>

@if(request('status') === 'submitted')
    <div id="success-popup" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-3xl shadow-xl w-full max-w-md p-8 text-center animate-scale-in">
            <div class="w-24 h-24 mx-auto bg-[var(--success-bg)] rounded-full flex items-center justify-center mb-6 border-4 border-[var(--success)]/10">
                <svg class="w-12 h-12 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-width="2.5" stroke="currentColor" d="M5 13l4 4 7-7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-[var(--text)] mb-3">Transfer request submitted</h2>
            <p class="text-[var(--text-muted)] mb-6">Your transfer request has been sent for review. Your balance will update once it is processed.</p>
            <a href="{{ route('activity') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-[var(--accent)] text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity">
                <x-icon name="activity" class="w-4 h-4 mr-2" />
                View activity
            </a>
            <button type="button" onclick="document.getElementById('success-popup').remove()" class="mt-4 text-sm text-[var(--text-muted)] hover:text-[var(--text)] transition-colors">
                Continue browsing
            </button>
        </div>
    </div>
@endif

<style>
    .bp-progress-track {
        width: 100%;
        height: 10px;
        border-radius: 9999px;
        background: var(--line);
        overflow: hidden;
    }
    .bp-progress-fill {
        height: 100%;
        width: 0%;
        border-radius: 9999px;
        background: linear-gradient(90deg, var(--success) 0%, #34D399 50%, var(--success) 100%);
        box-shadow: 0 0 12px rgba(5, 150, 105, 0.55);
        animation: bp-grow 9s ease-out forwards;
    }
    @keyframes bp-grow {
        from { width: 6%; }
        to { width: 100%; }
    }
</style>
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
        var activeForm = document.querySelector('.method-form#method-form-' + method);
        activeForm.classList.remove('hidden');
        activeForm.classList.add('block');
        document.querySelector('[data-method="' + method + '"]').classList.remove('border-[var(--line)]', 'text-[var(--text-muted)]');
        document.querySelector('[data-method="' + method + '"]').classList.add('border-[var(--accent)]', 'bg-[var(--accent-subtle)]', 'text-[var(--accent)]');
    }

    function clearMethodErrors() {
        document.querySelectorAll('.bp-field-error').forEach(el => el.remove());
    }

    function showFieldError(container, message) {
        var div = document.createElement('div');
        div.className = 'bp-field-error text-sm text-[var(--danger)] mt-1';
        div.textContent = message;
        container.appendChild(div);
    }

    function validateBeforeSubmit() {
        clearMethodErrors();

        var method = document.getElementById('selected_method').value;
        var activeForm = document.getElementById('method-form-' + method);
        if (!activeForm) return true;

        var firstInvalid = null;

        var accountInput = document.querySelector('input[name="from_account_id"]');
        if (accountInput && !accountInput.value) {
            firstInvalid = accountInput.closest('.custom-select');
            showFieldError(accountInput.closest('[data-account-field]') || accountInput.closest('.relative'), 'Please select a debit account.');
        }

        activeForm.querySelectorAll('[data-required]').forEach(el => {
            var value = (el.value || '').trim();
            if (el.type === 'hidden') {
                var trigger = el.closest('.custom-select');
                if (trigger) {
                    var label = trigger.parentElement;
                    if (!value && firstInvalid === null) firstInvalid = label;
                    if (!value) showFieldError(label, 'Please choose an option.');
                }
            } else if (!value) {
                if (firstInvalid === null) firstInvalid = el;
                showFieldError(el.parentElement, 'This field is required.');
            }
        });

        if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('payment-form');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            var amountInput = document.getElementById('amount');
            if (amountInput) {
                amountInput.value = amountInput.value.replace(/,/g, '.').replace(/[^0-9.]/g, '');
            }

            if (!validateBeforeSubmit()) {
                e.preventDefault();
                return;
            }

            var overlay = document.getElementById('processing-overlay');
            var fill = overlay.querySelector('.bp-progress-fill');
            fill.style.animation = 'none';
            void fill.offsetWidth;
            fill.style.animation = '';

            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            document.body.style.overflow = 'hidden';
            var btn = document.getElementById('submit-btn');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Processing…';
            }
        });
    });

    selectMethod(@json($selectedMethod));
</script>
@endsection
