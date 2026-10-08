@extends('layouts.customer')

@section('title', 'Receive')
@section('content')
<div class="px-3 sm:px-4 lg:px-6 space-y-6" id="receive-page">
    <header class="page-header">
        <a href="{{ route('home') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Receive money</h1>
    </header>

    <div>
        <label for="account" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Account</label>
        <x-custom-select
            name="account"
            :options="$accounts->mapWithKeys(fn($a) => [$a->id => $a->name . ' (' . $a->currency->symbol . ')' ])"
            placeholder="Select account"
            :value="$selectedAccount?->id"
            required
        />
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var selects = document.querySelectorAll('[data-custom-select] input[type="hidden"]');
            selects.forEach(function (input) {
                input.addEventListener('change', function () {
                    if (this.value) {
                        const url = new URL(window.location);
                        url.searchParams.set('account', this.value);
                        window.location.href = url.toString();
                    }
                });
            });

            function copyAddress(el) {
                var text = el.textContent.trim();
                navigator.clipboard.writeText(text).then(function() {
                    var tooltip = el.nextElementSibling;
                    if (tooltip && tooltip.classList) {
                        tooltip.classList.remove('hidden');
                        setTimeout(function() { tooltip.classList.add('hidden'); }, 1500);
                    }
                });
            }

            window.copyDepositAddress = copyAddress;
        });
    </script>
    @endpush

    @if($selectedAccount)
        <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl divide-y divide-[var(--line)]">
            <div class="p-5">
                <p class="text-xs text-[var(--text-muted)] mb-1 uppercase tracking-wider font-medium">Account title</p>
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-[var(--text)]">{{ $selectedAccount->name }}</span>
                    <button onclick="copyText('{{ $selectedAccount->name }}')" class="text-[var(--text-muted)] hover:text-[var(--text)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Copy">
                        <x-icon name="copy" class="w-4 h-4" />
                    </button>
                </div>
            </div>
            <div class="p-5">
                <p class="text-xs text-[var(--text-muted)] mb-1 uppercase tracking-wider font-medium">Account number</p>
                <div class="flex items-center justify-between">
                    <span class="font-mono text-sm text-[var(--text)]">{{ $selectedAccount->account_number }}</span>
                    <button onclick="copyText('{{ $selectedAccount->account_number }}')" class="text-[var(--text-muted)] hover:text-[var(--text)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Copy">
                        <x-icon name="copy" class="w-4 h-4" />
                    </button>
                </div>
            </div>
            <div class="p-5">
                <p class="text-xs text-[var(--text-muted)] mb-1 uppercase tracking-wider font-medium">Currency</p>
                <span class="text-sm font-medium text-[var(--text)]">{{ $selectedAccount->currency->code }} ({{ $selectedAccount->currency->symbol }})</span>
            </div>
        </div>

        {{-- Deposit Methods (same as deposit page) --}}
        @php
            $depositMethods = $selectedAccount->depositMethods()->wherePivot('is_active', true)->get();
        @endphp
        @if($depositMethods->isNotEmpty())
            <div class="bg-[var(--accent-subtle)] border border-[var(--accent)]/10 rounded-2xl p-5 space-y-5">
                <h2 class="text-sm font-semibold text-[var(--text)] mb-2">Deposit methods</h2>
                @foreach($depositMethods as $method)
                    <div class="space-y-4 last:pb-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)]">
                                    @if($method->type === 'crypto')
                                        <x-icon name="bitcoin" class="w-5 h-5" />
                                    @else
                                        <x-icon name="landmark" class="w-5 h-5" />
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-[var(--text)]">{{ $method->name }}</p>
                                    <p class="text-xs text-[var(--text-muted)]">{{ ucfirst($method->type) }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-[var(--text-muted)] bg-[var(--line-soft)] px-2.5 py-1 rounded-full">{{ ucfirst($method->type) }}</span>
                        </div>

                        @if($method->pivot->address)
                            <div>
                                <p class="text-xs text-[var(--text-muted)] mb-1 uppercase tracking-wider font-medium">Address</p>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-sm text-[var(--text)] break-all">{{ $method->pivot->address }}</span>
                                    <button onclick="copyText('{{ $method->pivot->address }}')" class="text-[var(--text-muted)] hover:text-[var(--text)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Copy">
                                        <x-icon name="copy" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        @endif

                        @if($method->pivot->qr_path)
                            <div>
                                <p class="text-xs text-[var(--text-muted)] mb-1 uppercase tracking-wider font-medium">QR code</p>
                                <img src="{{ asset('storage/' . $method->pivot->qr_path) }}" alt="QR Code" class="w-32 h-32 object-contain border border-[var(--line)] rounded-xl">
                            </div>
                        @endif

                        @if($method->pivot->metadata && isset($method->pivot->metadata['account_name']))
                            <div>
                                <p class="text-xs text-[var(--text-muted)] mb-1 uppercase tracking-wider font-medium">Account name</p>
                                <span class="font-medium text-[var(--text)]">{{ $method->pivot->metadata['account_name'] }}</span>
                            </div>
                        @endif

                        @if($method->pivot->instructions)
                            <div>
                                <p class="text-xs text-[var(--text-muted)] mb-1 uppercase tracking-wider font-medium">Instructions</p>
                                <p class="text-sm text-[var(--text)] leading-relaxed whitespace-pre-line">{{ $method->pivot->instructions }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-[var(--accent-subtle)] border border-[var(--accent)]/10 rounded-2xl p-5">
                <h2 class="text-sm font-semibold text-[var(--text)] mb-2">Deposit instructions</h2>
                <p class="text-sm text-[var(--text-muted)] leading-relaxed">Your deposit details will appear here once configured for your account.</p>
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-8 text-center">
            <x-icon name="file-text" class="w-12 h-12 mx-auto mb-3 text-[var(--text-muted)]" />
            <p class="text-sm text-[var(--text-muted)]">Select an account to view deposit details.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function copyText(text) {
    navigator.clipboard.writeText(text).then(function() {
        var existing = document.querySelector('.copy-tooltip');
        if (existing) existing.remove();
        var tooltip = document.createElement('span');
        tooltip.className = 'copy-tooltip fixed top-4 right-1/2 translate-x-1/2 px-3 py-1.5 bg-gray-800 text-white text-xs rounded-lg shadow-lg z-50';
        tooltip.textContent = 'Copied!';
        document.body.appendChild(tooltip);
        setTimeout(function() { tooltip.remove(); }, 1500);
    });
}
</script>
@endpush
@endsection
