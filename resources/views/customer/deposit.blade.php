@extends('layouts.customer')

@section('title', 'Deposit')

@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-6" id="deposit-app">
    <header class="page-header">
            <a href="{{ route('transfer') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
                <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
            </a>
        <h1 class="text-xl font-semibold">Deposit</h1>
    </header>

        @if(request('status') === 'submitted')
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div class="bg-white dark:bg-[var(--surface)] rounded-2xl shadow-xl w-full max-w-md mx-4 p-8 text-center animate-scale-in">
                    <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <x-icon name="circle-check" class="w-10 h-10 text-green-500" />
                    </div>
                    <h2 class="text-xl font-semibold text-[var(--text)] mb-2">Processing deposit</h2>
                    <p class="text-[var(--text-muted)] mb-6">Your deposit request has been submitted successfully. It is being reviewed and your account will be credited once approved.</p>
                    <a href="{{ route('activity') }}" class="inline-block w-full px-6 py-2.5 bg-[var(--accent)] text-white rounded-xl text-sm font-medium hover:opacity-90 transition-opacity">View activity</a>
                    <button onclick="window.location.href='{{ route('home') }}'" class="mt-3 text-xs text-[var(--text-muted)] hover:text-[var(--text)]">← Back to dashboard</button>
                </div>
            </div>
        @else
        @if($depositMethods->isNotEmpty())
            {{-- Step 1: Select a deposit method --}}
            <div id="methods-list" class="space-y-4">
                <div>
                    <h2 class="text-sm font-semibold text-[var(--text-muted)]">Select deposit method</h2>
                    <p class="text-xs text-[var(--text-muted)] mt-1">Choose how you want to deposit funds</p>
                </div>
                <div class="space-y-3">
                    @foreach($depositMethods as $method)
                        <div class="border border-[var(--line)] rounded-xl p-4 cursor-pointer hover:border-[var(--accent)] hover:bg-[var(--line-soft)] transition-all"
                             onclick="showPaymentForm('{{ $method->code }}', '{{ $method->account_id }}', '{{ addslashes($method->name) }}', '{{ addslashes($method->account_name) }}', '{{ addslashes($method->currency->symbol) }}', '{{ addslashes($method->currency->code) }}', '{{ addslashes($method->pivot->address ?? '') }}', '{{ addslashes($method->pivot->instructions ?? '') }}', '{{ $method->pivot->qr_path ?? '' }}', '{{ addslashes($method->type) }}')">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)]">
                    @if($method->type === 'crypto')
                        <x-icon name="bitcoin" class="w-5 h-5" />
                    @else
                        <x-icon name="landmark" class="w-5 h-5" />
                    @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-[var(--text)]">{{ $method->name }}</p>
                                        <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ $method->account_name }} ({{ $method->currency->code }})</p>
                                        @if($method->pivot->address)
                                            <p class="text-xs text-[var(--text-muted)] mt-1 font-mono">{{ Str::limit($method->pivot->address, 40) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-xs text-[var(--text-muted)] bg-[var(--line-soft)] px-2.5 py-1 rounded-full">{{ ucfirst($method->type) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white border border-[var(--line)] rounded-2xl p-8 text-center">
                <p class="text-[var(--text-muted)]">No deposit methods are currently available. Please contact support.</p>
            </div>
        @endif

        {{-- Step 2: Payment form (hidden by default, shown after selecting a method) --}}
        <div id="payment-form-container" class="hidden">
            <form id="deposit-form" method="POST" action="{{ route('deposit.submit') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <input type="hidden" name="idempotency" value="{{ $idempotency }}">
                <input type="hidden" id="form_account_id" name="account_id" value="">
                <input type="hidden" id="form_deposit_method" name="deposit_method" value="">

                {{-- Display the deposit details }}
                <div class="bg-[var(--surface-raised)] border border-[var(--line)] rounded-2xl p-5">
                    <h3 class="text-sm font-semibold text-[var(--text-muted)] mb-3">Send payment to</h3>
                    <div id="deposit-details" class="space-y-3">
                        <p id="detail-method-name" class="font-medium text-[var(--text)]"></p>
                        <p id="detail-method-account" class="text-xs text-[var(--text-muted)]"></p>
                        <div id="detail-address" class="hidden">
                            <p class="text-xs text-[var(--text-muted)] mb-1">Address</p>
                            <div class="inline-flex items-center gap-2 p-2 bg-white border border-[var(--line)] rounded-lg font-mono text-xs text-[var(--text)] relative">
                                <span id="copy-address-value"></span>
                                <button type="button" onclick="copyAddressText()" class="p-1 text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors">
                                    <x-icon name="copy" class="w-4 h-4" />
                                </button>
                                <span id="copy-tooltip" class="hidden absolute -bottom-6 left-0 px-2 py-1 bg-gray-800 text-white text-xs rounded">Copied!</span>
                            </div>
                        </div>
                        <div id="detail-qr" class="hidden">
                            <p class="text-xs text-[var(--text-muted)] mb-1">QR Code</p>
                            <img id="qr-image" src="" alt="QR Code" class="w-32 h-32 object-contain border border-[var(--line)] rounded-lg">
                        </div>
                        <div id="detail-instructions" class="hidden">
                            <p class="text-xs text-[var(--text-muted)] mb-1">Instructions</p>
                            <p class="text-sm text-[var(--text)] whitespace-pre-line"></p>
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
                    <label for="note" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Reference <span class="text-[var(--text-muted)] font-normal">(optional)</span></label>
                    <input type="text" id="note" name="note" maxlength="500"
                           class="input-field"
                           placeholder="Payment reference or note">
                </div>

                <div>
                    <p class="block text-sm font-semibold text-[var(--text)] mb-2">Proof of payment</p>
                    <div class="border-2 border-dashed border-[var(--line)] rounded-xl p-6 text-center hover:border-[var(--accent)] transition-colors">
                        <x-icon name="file-image" class="w-8 h-8 mx-auto mb-2 text-[var(--text-muted)]" />
                        <p class="text-sm text-[var(--text-muted)]">Take photo or Choose file</p>
                        <p class="text-xs text-[var(--text-muted)] mt-1">JPG, PNG, or PDF, up to 5 MB</p>
                        <input type="file" name="proof" accept="image/jpeg,image/jpg,image/png,application/pdf,.jpg,.jpeg,.png,.pdf" class="mt-3 text-sm">
                    </div>
                </div>

                @if($errors->any())
                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="backToList()" class="flex-1 py-3.5 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm font-medium text-[var(--text)] hover:bg-[var(--line-soft)] transition-colors">Back</button>
                    <button type="submit" class="flex-1 py-3.5 btn-primary">Send payment</button>
                </div>
            </form>
        </div>
    @endif
</div>

<script>
function showPaymentForm(code, accountId, name, accountName, currencySymbol, currencyCode, address, instructions, qrPath, type) {
    document.getElementById('form_deposit_method').value = code;
    document.getElementById('form_account_id').value = accountId;
    document.getElementById('detail-method-name').textContent = name;
    document.getElementById('detail-method-account').textContent = accountName + ' (' + currencyCode + ')';

    var addressEl = document.getElementById('detail-address');
    var qrEl = document.getElementById('detail-qr');
    var instructionsEl = document.getElementById('detail-instructions');

    if (address) {
        document.getElementById('copy-address-value').textContent = address;
        addressEl.classList.remove('hidden');
    } else {
        addressEl.classList.add('hidden');
        document.getElementById('copy-address-value').textContent = '';
    }

    if (qrPath) {
        document.getElementById('qr-image').src = '{{ asset('storage/') }}' + qrPath;
        qrEl.classList.remove('hidden');
    } else {
        qrEl.classList.add('hidden');
    }

    if (instructions) {
        instructionsEl.querySelector('p').textContent = instructions;
        instructionsEl.classList.remove('hidden');
    } else {
        instructionsEl.classList.add('hidden');
    }

    document.getElementById('methods-list').classList.add('hidden');
    document.getElementById('payment-form-container').classList.remove('hidden');
}

function backToList() {
    document.getElementById('payment-form-container').classList.add('hidden');
    document.getElementById('methods-list').classList.remove('hidden');
}

function copyAddressText() {
    var el = document.getElementById('copy-address-value');
    var text = el.textContent;
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        var tooltip = document.getElementById('copy-tooltip');
        tooltip.classList.remove('hidden');
        setTimeout(() => tooltip.classList.add('hidden'), 2000);
    });
}
</script>
@endsection
