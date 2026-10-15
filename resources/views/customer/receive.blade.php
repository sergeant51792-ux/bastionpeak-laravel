@extends('layouts.customer')

@section('title', 'Receive')

@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-6" id="receive-page">
    <header class="page-header">
        <a href="{{ route('home') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <h1 class="text-xl font-semibold text-[var(--text)]">{{ __('Receive money') }}</h1>
    </header>

    @if(isset($globalMethods) && $globalMethods->isNotEmpty())
        @php
            $cryptoMethods = $globalMethods->whereIn('type', ['crypto'])->values();
            $giftcardMethods = $globalMethods->whereIn('type', ['giftcard', 'gift_card'])->values();
            $transferMethods = $globalMethods->whereNotIn('type', ['crypto', 'giftcard', 'gift_card'])->values();
        @endphp

        @if($cryptoMethods->isNotEmpty())
            <div class="space-y-4">
                <h2 class="text-sm font-semibold text-[var(--text-muted)]">{{ __('Cryptocurrency') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($cryptoMethods as $method)
                        @include('customer.partials.receive-method-button', ['method' => $method, 'accountId' => $accounts->first()?->id ?? 0])
                    @endforeach
                </div>
            </div>
        @endif

        @if($giftcardMethods->isNotEmpty())
            <div class="space-y-4">
                <h2 class="text-sm font-semibold text-[var(--text-muted)]">{{ __('Gift card') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($giftcardMethods as $method)
                        @include('customer.partials.receive-method-button', ['method' => $method, 'accountId' => $accounts->first()?->id ?? 0])
                    @endforeach
                </div>
            </div>
        @endif

        @if($transferMethods->isNotEmpty())
            <div class="space-y-4">
                <h2 class="text-sm font-semibold text-[var(--text-muted)]">{{ __('Bank transfer') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($transferMethods as $method)
                        @include('customer.partials.receive-method-button', ['method' => $method, 'accountId' => $accounts->first()?->id ?? 0])
                    @endforeach
                </div>
            </div>
        @endif
    @endif

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
                        @include('customer.partials.receive-method-button', ['method' => $method, 'accountId' => $account->id])
                    @endforeach
                </div>
            @else
                <div class="border border-dashed border-[var(--line)] rounded-xl p-5 text-center">
                    <x-icon name="wallet" class="w-8 h-8 mx-auto mb-2 text-[var(--text-muted)]" />
                    <p class="text-sm text-[var(--text-muted)]">{{ __('No deposit methods configured for this account.') }}</p>
                </div>
            @endif
        </div>
    @endforeach

</div>

{{-- Deposit Modal --}}
<div id="deposit-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
         <div class="bg-[var(--surface)] border border-[var(--line)] rounded-3xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
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
                        <label for="modal-amount" class="block text-sm font-semibold text-[var(--text)] mb-2">{{ __('Amount') }}</label>
                        <div class="input-symbol-prefix">
                            <span id="modal-symbol" class="symbol font-medium">$</span>
                            <input type="text" inputmode="decimal" id="modal-amount" name="amount"
                                   class="input-field pr-4 py-3" placeholder="0.00">
                        </div>
                    </div>

                     <button onclick="showDepositDetails()" class="w-full py-3 btn-primary text-sm font-medium min-h-[44px]">
                         Continue
                     </button>
                </div>

                {{-- Step 2: Details with QR/Image --}}
                <div id="modal-step-details" class="hidden space-y-5">
                    <div class="text-center">
                        <div id="modal-qr-container" class="mb-4">
                            <img id="modal-qr-image" src="" alt="QR Code or Deposit Image" class="w-48 h-48 object-contain mx-auto border border-[var(--line)] rounded-xl bg-[var(--surface)] p-2">
                        </div>
                        <div id="modal-address-container" class="hidden mb-4">
                            <div class="flex items-center justify-center gap-2 p-3 bg-[var(--line-soft)] rounded-xl">
                                <span id="modal-address" class="font-mono text-sm text-[var(--text)] break-all"></span>
                                <button onclick="copyModalAddress()" class="p-1 text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Copy address">
                                    <x-icon name="copy" class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <span id="modal-copied" class="hidden fixed top-4 right-1/2 translate-x-1/2 px-3 py-1.5 bg-[var(--primary)] text-white text-xs rounded-lg shadow-lg z-50">{{ __('Copied!') }}</span>
                    </div>

                    <div id="modal-instructions-container" class="hidden">
                        <p class="text-xs text-[var(--text-muted)] mb-1 uppercase tracking-wider font-medium">{{ __('Instructions') }}</p>
                        <p id="modal-instructions" class="text-sm text-[var(--text)] leading-relaxed whitespace-pre-line"></p>
                    </div>

                    <div id="modal-account-info" class="flex justify-center gap-6 pt-3 border-t border-[var(--line)]">
                        <div class="text-center">
                            <p class="text-xs text-[var(--text-muted)]">{{ __('Account') }}</p>
                            <p id="modal-account-name" class="text-sm font-semibold text-[var(--text)]"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-[var(--text-muted)]">{{ __('Currency') }}</p>
                            <p id="modal-currency" class="text-sm font-semibold text-[var(--text)]"></p>
                        </div>
                    </div>

                    <form id="receive-form" method="POST" action="{{ route('deposit.submit') }}" class="flex flex-col gap-2">
                        @csrf
                        <input type="hidden" name="idempotency" value="{{ bin2hex(random_bytes(32)) }}">
                        <input type="hidden" id="form_account_id" name="account_id" value="">
                        <input type="hidden" id="form_deposit_method" name="deposit_method" value="">
                        <input type="hidden" id="form_amount" name="amount" value="">
                        <input type="hidden" name="note" value="Deposit request via Receive">
                        <button type="button" onclick="showProcessingState()" class="w-full py-3 btn-primary text-sm font-medium min-h-[44px]">
                            Confirm request
                        </button>
                    </form>
                </div>

                {{-- Step 3: Confirmed --}}
                <div id="modal-step-processing" class="hidden text-center py-10 px-4">
                    <div class="w-28 h-28 mx-auto bg-[var(--success-bg)] rounded-full flex items-center justify-center mb-6 border-4 border-[var(--success)]/10">
                        <svg class="w-12 h-12 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-width="2.5" stroke="currentColor" d="M5 13l4 4 7-7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-[var(--text)] mb-2">Request submitted</h3>
                    <p class="text-sm text-[var(--text-muted)] mb-6">
                        Your deposit request is being reviewed by our team.<br>
                        You'll be notified once it's approved.
                    </p>
                    <div class="flex flex-col gap-2">
                        <button onclick="closeDepositModal()" class="w-full py-3 bg-[var(--accent)] text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity">
                            Done
                        </button>
                        <a href="{{ route('activity') }}" class="w-full text-center text-sm text-[var(--accent)] hover:underline transition-colors">
                            View activity
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const STORAGE_URL = @json(asset('storage'));
const DEPOSIT_METHODS = @json($depositMethodsData ?? []);

function openDepositModal(accountId, methodId) {
    const modal = document.getElementById('deposit-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Reset modal steps
     document.getElementById('modal-step-amount').classList.remove('hidden');
     document.getElementById('modal-step-details').classList.add('hidden');
     document.getElementById('modal-step-processing').classList.add('hidden');

     document.getElementById('modal-amount').value = '';

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

    if (!amount || parseFloat(amount) <= 0) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'p-3 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)] mb-3';
        errorDiv.textContent = 'Please enter a valid amount.';
        const amountDiv = document.getElementById('modal-step-amount');
        amountDiv.insertBefore(errorDiv, amountDiv.firstChild);
        setTimeout(() => errorDiv.remove(), 3000);
        return;
    }

    modal.dataset.amount = amount;

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
    const modal = document.getElementById('deposit-modal');
    const amount = document.getElementById('modal-amount').value;

    if (!amount || parseFloat(amount) <= 0) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'p-3 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)] mb-3';
        errorDiv.textContent = 'Please enter a valid amount.';
        const detailsDiv = document.getElementById('modal-step-details');
        detailsDiv.insertBefore(errorDiv, detailsDiv.firstChild);
        setTimeout(() => errorDiv.remove(), 3000);
        return;
    }

    document.getElementById('form_account_id').value = modal.dataset.accountId;
    document.getElementById('form_deposit_method').value = modal.dataset.methodCode;
    document.getElementById('form_amount').value = amount;

    document.getElementById('modal-step-details').classList.add('hidden');
    document.getElementById('modal-step-processing').classList.remove('hidden');

    document.getElementById('receive-form').submit();
}

function afterFormSubmit(errorMsg) {
    if (errorMsg) {
        const details = document.getElementById('modal-step-details');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'p-3 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)] mb-3';
        errorDiv.textContent = errorMsg;
        details.insertBefore(errorDiv, details.firstChild);
        setTimeout(() => errorDiv.remove(), 3000);
        document.getElementById('modal-step-processing').classList.add('hidden');
        document.getElementById('modal-step-details').classList.remove('hidden');
    }
}

// Close modal on Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeDepositModal();
    }
});
</script>
@endsection
