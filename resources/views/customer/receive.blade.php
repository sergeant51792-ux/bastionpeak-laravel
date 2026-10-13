@extends('layouts.customer')

@section('title', 'Receive')

@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-6" id="receive-page">
    <header class="page-header">
        <a href="{{ route('home') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <h1 class="text-xl font-semibold text-[var(--text)]">Receive money</h1>
    </header>

    @foreach($accounts as $account)
        @php
            $depositMethods = $account->depositMethods()->wherePivot('is_active', true)->get();
        @endphp
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-[var(--text-muted)]">{{ $account->name }} ({{ $account->currency->code }})</h2>
            </div>

            @if($depositMethods->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($depositMethods as $method)
                        <button type="button"
                                onclick="openDepositModal({{ $account->id }}, {{ $method->id }})"
                                class="text-left border border-[var(--line)] rounded-xl p-4 hover:border-[var(--accent)] hover:bg-[var(--line-soft)] transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] shrink-0">
                                    @if($method->type === 'crypto')
                                        <x-icon name="bitcoin" class="w-5 h-5" />
                                    @elseif($method->type === 'giftcard' || $method->type === 'gift_card')
                                        <x-icon name="gift" class="w-5 h-5" />
                                    @else
                                        <x-icon name="landmark" class="w-5 h-5" />
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-[var(--text)]">{{ $method->name }}</p>
                                    <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ ucfirst($method->type) }}</p>
                                </div>
                                <x-icon name="chevron-right" class="w-5 h-5 text-[var(--text-muted)] group-hover:text-[var(--accent)] transition-colors" />
                            </div>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="border border-dashed border-[var(--line)] rounded-xl p-5 text-center">
                    <x-icon name="wallet" class="w-8 h-8 mx-auto mb-2 text-[var(--text-muted)]" />
                    <p class="text-sm text-[var(--text-muted)]">No deposit methods configured for this account.</p>
                </div>
            @endif
        </div>
    @endforeach

</div>

{{-- Deposit Modal --}}
<div id="deposit-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 id="modal-title" class="text-lg font-semibold text-[var(--text)]">Receive via Bank Transfer</h2>
                    <button type="button" onclick="closeDepositModal()" class="p-2 text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--line-soft)] rounded-lg transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Step 1: Amount Input --}}
                <div id="modal-step-amount" class="space-y-5">
                    <div>
                        <label for="modal-amount" class="block text-sm font-semibold text-[var(--text)] mb-2">Amount</label>
                        <div class="relative">
                            <span id="modal-symbol" class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--text-muted)] text-sm font-medium">$</span>
                            <input type="text" inputmode="decimal" id="modal-amount" name="amount"
                                   class="input-field pl-8 pr-4 py-3" placeholder="0.00">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-sm">
                        <input type="checkbox" id="modal-any-amount" class="rounded border-[var(--line)] text-[var(--accent)] focus:ring-[var(--accent)]">
                        <label for="modal-any-amount" class="text-[var(--text-muted)]">Any amount</label>
                    </div>

                    <button onclick="showDepositDetails()" class="w-full py-3 btn-primary text-sm font-medium min-h-[44px]">
                        Continue
                    </button>
                </div>

                {{-- Step 2: Details with QR/Image --}}
                <div id="modal-step-details" class="hidden space-y-5">
                    <div class="text-center">
                        <div id="modal-qr-container" class="mb-4">
                            <img id="modal-qr-image" src="" alt="QR Code or Deposit Image" class="w-48 h-48 object-contain mx-auto border border-[var(--line)] rounded-xl bg-white p-2">
                        </div>
                        <div id="modal-address-container" class="hidden mb-4">
                            <div class="flex items-center justify-center gap-2 p-3 bg-[var(--line-soft)] rounded-xl">
                                <span id="modal-address" class="font-mono text-sm text-[var(--text)] break-all"></span>
                                <button onclick="copyModalAddress()" class="p-1 text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Copy address">
                                    <x-icon name="copy" class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <span id="modal-copied" class="hidden fixed top-4 right-1/2 translate-x-1/2 px-3 py-1.5 bg-gray-800 text-white text-xs rounded-lg shadow-lg z-50">Copied!</span>
                    </div>

                    <div id="modal-instructions-container" class="hidden">
                        <p class="text-xs text-[var(--text-muted)] mb-1 uppercase tracking-wider font-medium">Instructions</p>
                        <p id="modal-instructions" class="text-sm text-[var(--text)] leading-relaxed whitespace-pre-line"></p>
                    </div>

                    <div id="modal-account-info" class="flex justify-center gap-6 pt-3 border-t border-[var(--line)]">
                        <div class="text-center">
                            <p class="text-xs text-[var(--text-muted)]">Account</p>
                            <p id="modal-account-name" class="text-sm font-semibold text-[var(--text)]"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-[var(--text-muted)]">Currency</p>
                            <p id="modal-currency" class="text-sm font-semibold text-[var(--text)]"></p>
                        </div>
                    </div>

                    <button onclick="showProcessingState()" class="w-full py-3 btn-primary text-sm font-medium min-h-[44px]">
                        I've sent the payment
                    </button>
                </div>

                {{-- Step 3: Processing (green state) --}}
                <div id="modal-step-processing" class="hidden text-center py-8">
                    <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <div class="w-12 h-12 border-4 border-green-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Processing</h3>
                    <p class="text-sm text-[var(--text-muted)] mb-5">We're verifying your payment. This may take up to 10 minutes.</p>
                    <div class="w-full bg-[var(--line-soft)] rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full animate-pulse" style="width: 70%"></div>
                    </div>
                    <p class="text-xs text-[var(--text-muted)] mt-3">Progress: 0%</p>
                    <button onclick="closeDepositModal()" class="mt-5 text-sm font-medium text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const DEPOSIT_METHODS = @json($depositMethodsData ?? []);

function openDepositModal(accountId, methodId) {
    const modal = document.getElementById('deposit-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Reset modal steps
    document.getElementById('modal-step-amount').classList.remove('hidden');
    document.getElementById('modal-step-details').classList.add('hidden');
    document.getElementById('modal-step-processing').classList.add('hidden');

    // Reset amount
    document.getElementById('modal-amount').value = '';
    document.getElementById('modal-any-amount').checked = false;

    // Populate from data
    const method = DEPOSIT_METHODS.find(m => m.id === methodId);
    if (!method) return;

    document.getElementById('modal-title').textContent = method.name;
    document.getElementById('modal-symbol').textContent = method.currency_symbol;
    document.getElementById('modal-account-name').textContent = method.account_name;
    document.getElementById('modal-currency').textContent = method.currency_code;
    document.getElementById('modal-address').textContent = method.address || '';
    document.getElementById('modal-address-container').classList.toggle('hidden', !method.address);

    const qrImage = document.getElementById('modal-qr-image');
    if (method.qr_path) {
        qrImage.src = STORAGE_URL + '/' + method.qr_path;
        document.getElementById('modal-qr-container').classList.remove('hidden');
    } else {
        qrImage.src = '';
        document.getElementById('modal-qr-container').classList.add('hidden');
    }

    const instructionsEl = document.getElementById('modal-instructions');
    if (method.instructions) {
        instructionsEl.textContent = method.instructions;
        document.getElementById('modal-instructions-container').classList.remove('hidden');
    } else {
        document.getElementById('modal-instructions-container').classList.add('hidden');
        instructionsEl.textContent = '';
    }

    // Store method for later use
    modal.dataset.methodId = methodId;
    modal.dataset.accountId = accountId;
    modal.dataset.methodCode = method.code;
    modal.dataset.amount = '';
}

function closeDepositModal() {
    const modal = document.getElementById('deposit-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

function showDepositDetails() {
    const modal = document.getElementById('deposit-modal');
    const amount = document.getElementById('modal-amount').value;
    const anyAmount = document.getElementById('modal-any-amount').checked;

    if (!anyAmount && (!amount || parseFloat(amount) <= 0)) {
        // Show error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 mb-3';
        errorDiv.textContent = 'Please enter a valid amount or select "Any amount".';
        const amountDiv = document.getElementById('modal-step-amount');
        amountDiv.insertBefore(errorDiv, amountDiv.firstChild);
        setTimeout(() => errorDiv.remove(), 3000);
        return;
    }

    modal.dataset.amount = anyAmount ? '' : amount;

    document.getElementById('modal-step-amount').classList.add('hidden');
    document.getElementById('modal-step-details').classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function copyModalAddress() {
    const text = document.getElementById('modal-address').textContent;
    if (!text) return;
    navigator.clipboard.writeText(text).then(function () {
        const tooltip = document.getElementById('modal-copied');
        tooltip.classList.remove('hidden');
        setTimeout(function () { tooltip.classList.add('hidden'); }, 2000);
    });
}

function showProcessingState() {
    document.getElementById('modal-step-details').classList.add('hidden');
    document.getElementById('modal-step-processing').classList.remove('hidden');
}

// Close modal on Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeDepositModal();
    }
});
</script>
@endsection
