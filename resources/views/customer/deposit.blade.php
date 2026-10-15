@extends('layouts.customer')

@section('title', 'Add money')

@php
    $methodsData = $depositMethods->map(function ($method) {
        return [
            'id' => $method->id,
            'code' => $method->code,
            'name' => $method->name,
            'account_id' => $method->account_id,
            'account_name' => $method->account_name,
            'currency_symbol' => $method->currency->symbol ?? '$',
            'currency_code' => $method->currency->code ?? 'USD',
            'address' => $method->pivot->address ?? '',
            'instructions' => $method->pivot->instructions ?? '',
            'qr_path' => $method->pivot->qr_path ?? '',
        ];
    })->keyBy('id');
@endphp

@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-6" id="deposit-app">
    <header class="page-header">
        <a href="{{ route('home') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <h1 class="text-xl font-semibold">Add money</h1>
    </header>

    @if(request('status') === 'submitted')
        @php
            $txn = session('submitted_transaction_id')
                ? \App\Models\Transaction::find(session('submitted_transaction_id'))
                : null;
        @endphp
        <div id="success-popup" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-3xl shadow-xl w-full max-w-md mx-4 p-8 text-center animate-scale-in">
                <div class="w-24 h-24 mx-auto bg-[var(--success-bg)] rounded-full flex items-center justify-center mb-6 border-4 border-[var(--success)]/10">
                    <svg class="w-12 h-12 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-width="2.5" stroke="currentColor" d="M5 13l4 4 7-7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[var(--text)] mb-3">{{ __('Request submitted') }}</h2>
                <p class="text-[var(--text-muted)] mb-6">Your transaction request has been sent for review. Your balance will update once it is processed.</p>

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
                                <span class="text-xs font-semibold text-[var(--text)] tabular-nums">+{{ number_format((float) $txn->amount, $txn->currency->decimals ?? 2) }} {{ $txn->currency->symbol ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <a href="{{ route('activity') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-[var(--accent)] text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity">
                    <x-icon name="activity" class="w-4 h-4 mr-2" />
                    {{ __('View activity') }}
                </a>
                <button onclick="window.location.href='{{ route('home') }}'" class="mt-4 text-sm text-[var(--text-muted)] hover:text-[var(--text)] transition-colors">
                    ← {{ __('Back to dashboard') }}
                </button>
            </div>
        </div>
    @elseif($depositMethods->isEmpty())
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-8 text-center">
            <x-icon name="wallet" class="w-10 h-10 mx-auto mb-3 text-[var(--text-muted)]" />
            <p class="text-[var(--text-muted)]">No add money methods are available right now. Please try again later.</p>
        </div>
    @else
        {{-- Step 1: choose a category --}}
        <div id="category-step" class="space-y-4">
            <div>
                <h2 class="text-sm font-semibold text-[var(--text-muted)]">How would you like to add money?</h2>
                <p class="text-xs text-[var(--text-muted)] mt-1">Choose a category to continue</p>
            </div>
            <div class="space-y-3">
                @foreach($categories as $category)
                    <button type="button"
                            onclick="selectCategory('{{ $category['key'] }}')"
                            class="w-full text-left border border-[var(--line)] rounded-xl p-4 hover:border-[var(--accent)] hover:bg-[var(--line-soft)] transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] shrink-0">
                                <x-icon name="{{ $category['icon'] }}" class="w-5 h-5" />
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-[var(--text)]">{{ $category['label'] }}</p>
                                <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ $category['description'] }}</p>
                            </div>
                            <x-icon name="chevron-right" class="w-5 h-5 text-[var(--text-muted)]" />
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Step 2: choose a method within the selected category --}}
        <div id="method-step" class="hidden space-y-4">
            <button type="button" onclick="backToCategories()" class="text-sm font-medium text-[var(--accent)] flex items-center gap-1.5">
                <x-icon name="arrow-left" class="w-4 h-4" /> Categories
            </button>
            <div>
                <h2 id="method-step-title" class="text-sm font-semibold text-[var(--text-muted)]">Select an option</h2>
            </div>

            @foreach($categories as $category)
                <div id="method-list-{{ $category['key'] }}" class="space-y-3 hidden">
                    @foreach($category['methods'] as $method)
                        <button type="button"
                                onclick="showPaymentForm({{ $method->id }})"
                                class="w-full text-left border border-[var(--line)] rounded-xl p-4 hover:border-[var(--accent)] hover:bg-[var(--line-soft)] transition-all">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center shrink-0">
                                        @if($method->icon_image_path)
                                            <img src="{{ url('storage-file/' .$method->icon_image_path) }}" alt="{{ $method->name }}" class="w-6 h-6 object-contain">
                                        @else
                                            <x-icon name="{{ $method->category === 'crypto' ? 'bitcoin' : ($method->category === 'giftcard' ? 'gift' : 'landmark') }}" class="w-5 h-5" />
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-[var(--text)]">{{ $method->name }}</p>
                                        <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                            {{ $method->pivot->address ? Str::limit($method->pivot->address, 42) : ($method->description ?: $method->account_name) }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xs text-[var(--text-muted)] bg-[var(--line-soft)] px-2.5 py-1 rounded-full shrink-0">{{ $method->currency->code ?? 'USD' }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Step 3: details + amount --}}
        <div id="payment-form-container" class="hidden space-y-5">
            <button type="button" onclick="backToMethods()" class="text-sm font-medium text-[var(--accent)] flex items-center gap-1.5">
                <x-icon name="arrow-left" class="w-4 h-4" /> Back
            </button>

            <form id="deposit-form" method="POST" action="{{ route('deposit.submit') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <input type="hidden" name="idempotency" value="{{ $idempotency }}">
                <input type="hidden" id="form_account_id" name="account_id" value="">
                <input type="hidden" id="form_deposit_method" name="deposit_method" value="">

                <div class="bg-[var(--surface-raised)] border border-[var(--line)] rounded-2xl p-5">
                    <h3 class="text-sm font-semibold text-[var(--text-muted)] mb-3">Send to</h3>
                    <div class="space-y-3">
                        <p id="detail-method-name" class="font-medium text-[var(--text)]"></p>
                        <p id="detail-method-account" class="text-xs text-[var(--text-muted)]"></p>
                        <div id="detail-address" class="hidden">
                            <p class="text-xs text-[var(--text-muted)] mb-1">Address</p>
                                <div class="inline-flex items-center gap-2 p-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg font-mono text-xs text-[var(--text)] relative max-w-full">
                                <span id="copy-address-value" class="break-all"></span>
                                <button type="button" onclick="copyAddressText()" class="p-1 text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors shrink-0">
                                    <x-icon name="copy" class="w-4 h-4" />
                                </button>
                                <span id="copy-tooltip" class="hidden absolute -bottom-7 left-0 px-2 py-1 bg-[var(--text)] text-white text-xs rounded">Copied!</span>
                            </div>
                        </div>
                        <div id="detail-qr" class="hidden">
                            <p class="text-xs text-[var(--text-muted)] mb-1">QR Code</p>
                            <img id="qr-image" src="" alt="QR Code" class="w-32 h-32 object-contain border border-[var(--line)] rounded-lg">
                        </div>
                        <div id="detail-instructions" class="hidden">
                            <p class="text-xs text-[var(--text-muted)] mb-1">Instructions</p>
                            <p id="detail-instructions-text" class="text-sm text-[var(--text)] whitespace-pre-line"></p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="amount" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Amount</label>
                    <div class="input-symbol-prefix">
                        <span id="amount-symbol" class="symbol">$</span>
                        <input type="text" inputmode="decimal" id="amount" name="amount" required
                               class="input-field pr-4 py-3" placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label for="note" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Reference <span class="text-[var(--text-muted)] font-normal">(optional)</span></label>
                    <input type="text" id="note" name="note" maxlength="500" class="input-field" placeholder="Payment reference or note">
                </div>

                <div>
                    <p class="block text-sm font-semibold text-[var(--text)] mb-2">Proof of payment</p>
                    <div class="border-2 border-dashed border-[var(--line)] rounded-xl p-6 text-center hover:border-[var(--accent)] transition-colors">
                        <x-icon name="file-image" class="w-8 h-8 mx-auto mb-2 text-[var(--text-muted)]" />
                        <p class="text-sm text-[var(--text-muted)]">Take photo or choose file</p>
                        <p class="text-xs text-[var(--text-muted)] mt-1">JPG, PNG, or PDF, up to 5 MB</p>
                        <input type="file" name="proof" accept="image/jpeg,image/jpg,image/png,application/pdf,.jpg,.jpeg,.png,.pdf" class="mt-3 text-sm">
                    </div>
                </div>

                @if($errors->any())
                    <div class="p-3 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)]">
                        {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit" class="w-full py-3.5 btn-primary">Continue</button>
            </form>
        </div>
    @endif
</div>

<script>
const METHODS = @json($methodsData);
const STORAGE_URL = @json(url('storage-file'));

function selectCategory(key) {
    document.querySelectorAll('[id^="method-list-"]').forEach(function (el) {
        el.classList.add('hidden');
    });
    const target = document.getElementById('method-list-' + key);
    if (target) target.classList.remove('hidden');

    document.getElementById('category-step').classList.add('hidden');
    document.getElementById('payment-form-container').classList.add('hidden');
    document.getElementById('method-step').classList.remove('hidden');
}

function showPaymentForm(id) {
    const method = METHODS[id];
    if (!method) return;

    document.getElementById('form_deposit_method').value = method.code;
    document.getElementById('form_account_id').value = method.account_id;
    document.getElementById('detail-method-name').textContent = method.name;
    document.getElementById('detail-method-account').textContent = method.account_name + ' (' + method.currency_code + ')';
    document.getElementById('amount-symbol').textContent = method.currency_symbol;

    const addressEl = document.getElementById('detail-address');
    if (method.address) {
        document.getElementById('copy-address-value').textContent = method.address;
        addressEl.classList.remove('hidden');
    } else {
        addressEl.classList.add('hidden');
        document.getElementById('copy-address-value').textContent = '';
    }

    const qrEl = document.getElementById('detail-qr');
    if (method.qr_path) {
        document.getElementById('qr-image').src = STORAGE_URL + '/' + method.qr_path;
        qrEl.classList.remove('hidden');
    } else {
        qrEl.classList.add('hidden');
    }

    const instructionsEl = document.getElementById('detail-instructions');
    if (method.instructions) {
        document.getElementById('detail-instructions-text').textContent = method.instructions;
        instructionsEl.classList.remove('hidden');
    } else {
        instructionsEl.classList.add('hidden');
    }

    document.getElementById('method-step').classList.add('hidden');
    document.getElementById('payment-form-container').classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function backToCategories() {
    document.getElementById('method-step').classList.add('hidden');
    document.getElementById('category-step').classList.remove('hidden');
}

function backToMethods() {
    document.getElementById('payment-form-container').classList.add('hidden');
    document.getElementById('method-step').classList.remove('hidden');
}

function copyAddressText() {
    const text = document.getElementById('copy-address-value').textContent;
    if (!text) return;
    navigator.clipboard.writeText(text).then(function () {
        const tooltip = document.getElementById('copy-tooltip');
        tooltip.classList.remove('hidden');
        setTimeout(function () { tooltip.classList.add('hidden'); }, 2000);
    });
}
</script>
@endsection
