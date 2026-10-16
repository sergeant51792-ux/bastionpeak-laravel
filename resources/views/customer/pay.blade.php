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

    @if($transferBlocked ?? false)
        <div class="border border-[var(--danger)]/30 bg-[var(--danger-bg)] rounded-2xl p-6 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-[var(--danger)]/10 flex items-center justify-center text-[var(--danger)] mb-4">
                <x-icon name="lock" class="w-7 h-7" />
            </div>
            <h2 class="text-lg font-semibold text-[var(--text)]">Transfers locked</h2>
            <p class="text-sm text-[var(--text-muted)] mt-2 max-w-md mx-auto">{{ $transferBlockMessage ?? 'The transfer feature on your account is temporarily locked. Contact support for assistance.' }}</p>
            <a href="{{ route('messages') }}" class="mt-5 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[var(--accent)] text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity">
                <x-icon name="message-square" class="w-4 h-4" />
                Contact support
            </a>
        </div>
    @else

    <form method="POST" action="{{ route('pay.submit') }}" enctype="multipart/form-data" class="space-y-5" id="payment-form" novalidate>
        @csrf
        <input type="hidden" name="idempotency" value="{{ $idempotency }}">
        <input type="hidden" id="selected_method" name="payment_method" value="{{ $selectedMethod }}">

        @if($debitAccount)
            <div class="flex items-center justify-between gap-3 p-4 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl">
                <div class="min-w-0">
                    <p class="text-xs text-[var(--text-muted)] mb-0.5">Debited from</p>
                    <p class="text-sm font-semibold text-[var(--text)] truncate">
                        {{ $debitAccount->name }}
                        <span class="font-normal text-[var(--text-muted)]">&middot; {{ $debitAccount->currency?->code }}</span>
                    </p>
                </div>
                <div class="text-right flex-shrink-0" data-balance-sensitive>
                    <p class="text-xs text-[var(--text-muted)] mb-0.5">Balance</p>
                    <p class="text-sm font-semibold text-[var(--text)] tabular-nums">
                        {{ number_format((float) $debitAccount->balance, $debitAccount->currency?->decimals ?? 2) }} {{ $debitAccount->currency?->symbol ?? '' }}
                    </p>
                </div>
            </div>
        @else
            <div class="p-4 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)]">
                No active account is available to debit. Please contact support.
            </div>
        @endif

        <div>
            <label class="block text-sm font-semibold text-[var(--text)] mb-1.5">Transfer method</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3">
                <button type="button" onclick="selectMethod('wire')" data-method="wire" class="method-btn min-h-[44px] flex items-center justify-center py-3 px-3 sm:px-4 rounded-xl border text-sm text-center transition-all {{ $selectedMethod === 'wire' ? 'border-[var(--accent)] bg-[var(--accent-subtle)] text-[var(--accent)]' : 'border-[var(--line)] text-[var(--text-muted)] hover:border-[var(--accent)] hover:bg-[var(--accent-subtle)] hover:text-[var(--accent)]' }}">
                    Wire transfer
                </button>
                <button type="button" onclick="selectMethod('crypto')" data-method="crypto" class="method-btn min-h-[44px] flex items-center justify-center py-3 px-3 sm:px-4 rounded-xl border text-sm text-center transition-all {{ $selectedMethod === 'crypto' ? 'border-[var(--accent)] bg-[var(--accent-subtle)] text-[var(--accent)]' : 'border-[var(--line)] text-[var(--text-muted)] hover:border-[var(--accent)] hover:bg-[var(--accent-subtle)] hover:text-[var(--accent)]' }}">
                    Cryptocurrency
                </button>
                <button type="button" onclick="selectMethod('swift')" data-method="swift" class="method-btn min-h-[44px] flex items-center justify-center py-3 px-3 sm:px-4 rounded-xl border text-sm text-center transition-all col-span-2 sm:col-span-1 {{ $selectedMethod === 'swift' ? 'border-[var(--accent)] bg-[var(--accent-subtle)] text-[var(--accent)]' : 'border-[var(--line)] text-[var(--text-muted)] hover:border-[var(--accent)] hover:bg-[var(--accent-subtle)] hover:text-[var(--accent)]' }}">
                    SWIFT / UK &amp; EU modes
                </button>
            </div>
        </div>

        <div id="method-form-wire" class="method-form {{ $selectedMethod === 'wire' ? 'block' : 'hidden' }} space-y-4">
            <div class="bg-[var(--surface-raised)] rounded-xl p-4 sm:p-5">
                <h3 class="text-sm font-semibold text-[var(--text)] mb-3">Wire transfer details</h3>
                <div class="space-y-4">
                    <div>
                        <label for="wire_recipient_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Beneficiary name</label>
                        <input type="text" id="wire_recipient_name" name="recipient_name" data-required maxlength="255" class="input-field" placeholder="Full name of beneficiary" inputmode="text" autocomplete="off" autocapitalize="words" spellcheck="false">
                    </div>
                    <div>
                        <label for="wire_bank_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Bank name</label>
                        <input type="text" id="wire_bank_name" name="metadata[bank_name]" data-required maxlength="255" class="input-field" placeholder="Receiving bank name" inputmode="text" autocomplete="off" autocapitalize="words" spellcheck="false">
                    </div>
                    <div>
                        <label for="wire_routing_number" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Routing number</label>
                        <input type="text" id="wire_routing_number" name="metadata[routing_number]" maxlength="50" class="input-field" placeholder="ABA / CHIPS routing number" inputmode="text" autocomplete="off" autocapitalize="characters" spellcheck="false">
                    </div>
                    <div>
                        <label for="wire_account_number" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Account number</label>
                        <input type="text" id="wire_account_number" name="metadata[account_number]" data-required maxlength="50" class="input-field" placeholder="Recipient bank account number" inputmode="text" autocomplete="off" autocapitalize="characters" spellcheck="false">
                    </div>
                    <div>
                        <label for="wire_swift_code" class="block text-sm font-semibold text-[var(--text)] mb-1.5">SWIFT/BIC code <span class="text-[var(--text-muted)] font-normal">(optional)</span></label>
                        <input type="text" id="wire_swift_code" name="metadata[swift_code]" maxlength="20" class="input-field" placeholder="SWIFT/BIC code if applicable" inputmode="text" autocomplete="off" autocapitalize="characters" spellcheck="false">
                    </div>
                    <div>
                        <label for="wire_recipient_detail" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Recipient details</label>
                        <textarea id="wire_recipient_detail" name="recipient_detail" data-required rows="2" maxlength="1000" class="input-field" placeholder="Full beneficiary address, bank address, purpose of payment" autocomplete="off"></textarea>
                    </div>
                    <div>
                        <label for="wire_purpose" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Purpose of payment</label>
                        <input type="text" id="wire_purpose" name="purpose" data-required maxlength="500" class="input-field" placeholder="e.g. Invoice payment, investment, etc." inputmode="text" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        <div id="method-form-crypto" class="method-form {{ $selectedMethod === 'crypto' ? 'block' : 'hidden' }} space-y-4">
            <div class="bg-[var(--surface-raised)] rounded-xl p-4 sm:p-5">
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
                            :required
                        />
                    </div>
                    <div>
                        <label for="crypto_network" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Network</label>
                        <x-custom-select
                            id="crypto_network"
                            name="metadata[network]"
                            :options="[
                                'TRC20' => 'TRC20 (Tron)',
                                'ERC20' => 'ERC20 (Ethereum)',
                                'BEP20' => 'BEP20 (BNB Smart Chain)',
                                'Bitcoin' => 'Bitcoin Network',
                                'Ethereum' => 'Ethereum Network',
                            ]"
                            placeholder="Select network"
                            :required
                        />
                    </div>
                    <div>
                        <label for="crypto_wallet_address" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Recipient wallet address</label>
                        <input type="text" id="crypto_wallet_address" name="metadata[wallet_address]" data-required maxlength="200" class="input-field" placeholder="Recipient cryptocurrency wallet address" inputmode="text" autocomplete="off" autocapitalize="none" spellcheck="false">
                    </div>
                    <div>
                        <label for="crypto_recipient_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Recipient name</label>
                        <input type="text" id="crypto_recipient_name" name="recipient_name" data-required maxlength="255" class="input-field" placeholder="Full name of beneficiary" inputmode="text" autocomplete="off" autocapitalize="words" spellcheck="false">
                    </div>
                    <div>
                        <label for="crypto_recipient_detail" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Notes <span class="text-[var(--text-muted)] font-normal">(optional)</span></label>
                        <textarea id="crypto_recipient_detail" name="recipient_detail" rows="2" maxlength="1000" class="input-field" placeholder="Additional details or memo" autocomplete="off"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div id="method-form-swift" class="method-form {{ $selectedMethod === 'swift' ? 'block' : 'hidden' }} space-y-4">
            <div class="bg-[var(--surface-raised)] rounded-xl p-4 sm:p-5">
                <h3 class="text-sm font-semibold text-[var(--text)] mb-3">SWIFT / International transfer</h3>
                <div class="space-y-4">
                    <div>
                        <label for="swift_recipient_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Beneficiary name</label>
                        <input type="text" id="swift_recipient_name" name="recipient_name" data-required maxlength="255" class="input-field" placeholder="Full name of beneficiary" inputmode="text" autocomplete="off" autocapitalize="words" spellcheck="false">
                    </div>
                    <div>
                        <label for="swift_bank_name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Bank name</label>
                        <input type="text" id="swift_bank_name" name="metadata[bank_name]" data-required maxlength="255" class="input-field" placeholder="Receiving bank name" inputmode="text" autocomplete="off" autocapitalize="words" spellcheck="false">
                    </div>
                    <div>
                        <label for="swift_swift_code" class="block text-sm font-semibold text-[var(--text)] mb-1.5">SWIFT/BIC code</label>
                        <input type="text" id="swift_swift_code" name="metadata[swift_code]" data-required maxlength="20" class="input-field" placeholder="SWIFT/BIC code" inputmode="text" autocomplete="off" autocapitalize="characters" spellcheck="false">
                    </div>
                    <div>
                        <label for="swift_iban" class="block text-sm font-semibold text-[var(--text)] mb-1.5">IBAN <span class="text-[var(--text-muted)] font-normal">(EUR accounts)</span></label>
                        <input type="text" id="swift_iban" name="metadata[iban]" maxlength="50" class="input-field" placeholder="IBAN (for SEPA transfers)" inputmode="text" autocomplete="off" autocapitalize="characters" spellcheck="false">
                    </div>
                    <div>
                        <label for="swift_account_number" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Account number / Sort code</label>
                        <input type="text" id="swift_account_number" name="metadata[account_number]" data-required maxlength="50" class="input-field" placeholder="Account number and/or sort code" inputmode="text" autocomplete="off" autocapitalize="characters" spellcheck="false">
                    </div>
                    <div>
                        <label for="swift_recipient_detail" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Beneficiary address</label>
                        <textarea id="swift_recipient_detail" name="recipient_detail" data-required rows="2" maxlength="1000" class="input-field" placeholder="Full beneficiary address" autocomplete="off"></textarea>
                    </div>
                    <div>
                        <label for="swift_purpose" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Purpose of payment</label>
                        <input type="text" id="swift_purpose" name="purpose" data-required maxlength="500" class="input-field" placeholder="e.g. Invoice payment, investment, etc." inputmode="text" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label for="amount" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Amount</label>
            <div class="input-symbol-prefix">
                <span class="symbol">{{ $debitAccount?->currency?->symbol ?? '$' }}</span>
                <input type="text" inputmode="decimal" id="amount" name="amount" data-required
                       class="input-field pr-4 py-3"
                       placeholder="0.00" autocomplete="off">
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
            <button type="submit" id="submit-btn" class="w-full py-3.5 btn-primary disabled:opacity-60 disabled:cursor-not-allowed" @disabled(! $debitAccount)>Submit transfer request</button>
        </div>
    </form>
    @endif
</div>

{{-- Processing overlay: mature modern green bold thick --}}
<div id="processing-overlay" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-3xl shadow-2xl w-full max-w-md mx-4 p-8 text-center animate-scale-in">
        <div class="w-28 h-28 mx-auto mb-6 rounded-full bg-gradient-to-br from-[var(--success-bg)] to-[var(--success-bg)]/50 border-8 border-[var(--success)]/20 flex items-center justify-center">
            <svg class="w-12 h-12 text-[var(--success)] animate-pulse" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <path class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></path>
                <path class="opacity-90" fill="currentColor" d="M12 3a9 9 0 0 1 9 9h-3.5A5.5 5.5 0 0 0 12 6.5V3z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-[var(--text)] mb-2">Processing your transfer</h2>
        <p class="text-sm text-[var(--text-muted)] mb-6">Please wait while we securely transmit your request.</p>
        <div class="bp-progress-track">
            <div class="bp-progress-fill"></div>
        </div>
        <p class="text-xs text-[var(--text-muted)] mt-3 font-medium">Securely transmitting your request</p>
    </div>
</div>

@if(request('status') === 'submitted')
     @php
         $txn = session('submitted_transaction_id')
             ? \App\Models\Transaction::find(session('submitted_transaction_id'))
             : null;
     @endphp
    <div id="success-popup" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-3xl shadow-xl w-full max-w-md mx-4 p-8 text-center animate-scale-in">
            <div class="w-24 h-24 mx-auto bg-[var(--success-bg)] rounded-full flex items-center justify-center mb-6 border-4 border-[var(--success)]/10">
                <svg class="w-12 h-12 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-width="2.5" stroke="currentColor" d="M5 13l4 4 7-7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-[var(--text)] mb-3">{{ __('Transfer request submitted') }}</h2>
            <p class="text-[var(--text-muted)] mb-6">Your transfer request has been sent for review. Your balance will update once it is processed.</p>

            @if($txn)
                <div class="bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl p-4 mb-6 text-left">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs text-[var(--text-muted)] uppercase font-semibold">{{ __('Receipt') }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-[var(--warning-bg)] text-[var(--warning)]">
                            {{ $txn->customerStatusLabel() }}
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-xs text-[var(--text-muted)]">{{ __('Reference') }}</span>
                            <span class="text-xs font-mono text-[var(--text)]">{{ $txn->transaction_id ?? 'TXN-' . strtoupper(substr((string) $txn->id, 0, 8)) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-[var(--text-muted)]">{{ __('Date &amp; time') }}</span>
                            <span class="text-xs text-[var(--text)] tabular-nums">{{ $txn->created_at->format('j M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-[var(--text-muted)]">{{ __('Amount') }}</span>
                            <span class="text-xs font-semibold text-[var(--text)] tabular-nums">âˆ’{{ number_format((float) $txn->amount, $txn?->currency?->decimals ?? 2) }} {{ $txn?->currency?->symbol ?? '' }}</span>
                        </div>
                    </div>
                </div>
            @endif

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

    /* Money input reads as the primary field on the page */
    .input-symbol-prefix input#amount {
        font-size: 1.0625rem;
        font-weight: 600;
    }
    .input-symbol-prefix:focus-within {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .input-symbol-prefix input#amount:focus {
        outline: none;
        border: none;
        box-shadow: none;
    }
</style>
<script>
    function selectMethod(method) {
        document.getElementById('selected_method').value = method;
        document.querySelectorAll('.method-form').forEach(function (form) {
            form.classList.add('hidden');
            form.classList.remove('block');
            form.querySelectorAll('input, textarea, select').forEach(function (input) {
                input.disabled = true;
            });
        });
        document.querySelectorAll('.method-btn').forEach(function (btn) {
            btn.classList.remove('border-[var(--accent)]', 'bg-[var(--accent-subtle)]', 'text-[var(--accent)]');
            btn.classList.add('border-[var(--line)]', 'text-[var(--text-muted)]');
        });
        var activeForm = document.querySelector('.method-form#method-form-' + method);
        activeForm.classList.remove('hidden');
        activeForm.classList.add('block');
        activeForm.querySelectorAll('input, textarea, select').forEach(function (input) {
            input.disabled = false;
        });
        var activeBtn = document.querySelector('[data-method="' + method + '"]');
        if (activeBtn) {
            activeBtn.classList.remove('border-[var(--line)]', 'text-[var(--text-muted)]');
            activeBtn.classList.add('border-[var(--accent)]', 'bg-[var(--accent-subtle)]', 'text-[var(--accent)]');
        }
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

        var amountInput = document.getElementById('amount');
        if (amountInput) {
            var amountValue = (amountInput.value || '').trim();
            var amountNum = parseFloat(amountValue.replace(/,/g, '.'));
            if (!amountValue || isNaN(amountNum) || amountNum <= 0) {
                var amountContainer = amountInput.closest('.input-symbol-prefix') || amountInput.parentElement;
                showFieldError(amountContainer.parentElement, 'Please enter a valid amount.');
                if (firstInvalid === null) firstInvalid = amountContainer;
            }
        }

        activeForm.querySelectorAll('input[data-required], textarea[data-required], select[data-required]').forEach(function (el) {
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
                btn.textContent = 'Processingâ€¦';
            }
        });
    });

    selectMethod(@json($selectedMethod));
</script>
@endsection

