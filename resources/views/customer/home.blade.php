@extends('layouts.customer')

@section('title', 'Home')

@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
@endphp

@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-6" id="customer-dashboard">
    {{-- Greeting --}}
    <div class="flex items-center justify-between w-full">
        <div class="min-w-0 flex-1">
            <p class="text-xs text-[var(--text-muted)] sm:text-sm">{{ $greeting }}</p>
            <h1 class="text-lg sm:text-xl font-bold text-[var(--text)] tracking-tight truncate">{{ $user->name }}</h1>
        </div>
        <a href="{{ route('profile') }}" class="flex-shrink-0 ml-3" aria-label="Open profile">
            <x-avatar :user="$user" size="md" />
        </a>
    </div>

    {{-- Wallet Balance --}}
    <div>
        @if(count($walletBalances) > 0)
            <div id="wallet-slider" class="relative overflow-hidden">
                <div id="wallet-track" class="flex transition-transform duration-500 ease-out">
                    @foreach($walletBalances as $code => $wallet)
                        <div class="w-full flex-shrink-0" data-currency="{{ $code }}">
                            <div class="card-balance h-44 sm:h-48">
                                <div class="relative z-10 flex h-full flex-col justify-between">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-xs font-medium uppercase tracking-wider text-white/60">Wallet</p>
                                            <p class="font-semibold text-white">{{ $wallet['name'] }}</p>
                                        </div>
                                        <span class="text-sm font-bold text-white/90 bg-white/10 px-3 py-1 rounded-lg backdrop-blur-sm border border-white/10">{{ $wallet['symbol'] }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-white/60 mb-1 uppercase tracking-wider font-medium">Available balance</p>
                                        <p class="text-3xl sm:text-4xl font-bold tracking-tight text-white text-balance" data-balance-sensitive>
                                            {{ number_format((float) $wallet['balance'], $wallet['decimals']) }}
                                            <span class="text-sm text-white/60 block mt-1 font-medium">{{ $wallet['symbol'] }}</span>
                                        </p>
                                        <p class="balance-hidden text-3xl sm:text-4xl font-bold tracking-tight text-white text-balance">••••••</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center justify-center gap-2 mt-3">
                @foreach($walletBalances as $code => $wallet)
                    <button onclick="switchWallet('{{ $code }}')"
                            class="wallet-dot h-1.5 rounded-full transition-all {{ ($walletCurrency === $code || ($loop->first && !$walletCurrency)) ? 'bg-[var(--primary)] w-5' : 'bg-[var(--line)] w-1.5' }}"
                            aria-label="Switch to {{ $wallet['name'] }}"></button>
                @endforeach
            </div>
        @else
            <div id="wallet-slider" class="relative overflow-hidden">
                <div id="wallet-track" class="flex transition-transform duration-500 ease-out">
                    @foreach($accounts as $account)
                        <div class="w-full flex-shrink-0" data-currency="{{ $account->currency->code }}">
                            <div class="card-balance h-44 sm:h-48">
                                <div class="relative z-10 flex h-full flex-col justify-between">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-xs font-medium uppercase tracking-wider text-white/60">Account</p>
                                            <p class="font-semibold text-white">{{ $account->name }}</p>
                                        </div>
                                        <span class="text-sm font-bold text-white/90 bg-white/10 px-3 py-1 rounded-lg backdrop-blur-sm border border-white/10">{{ $account->currency->symbol }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-white/60 mb-1 uppercase tracking-wider font-medium">Available balance</p>
                                        <p class="text-3xl sm:text-4xl font-bold tracking-tight text-white text-balance" data-balance-sensitive>
                                            {{ number_format((float) $account->balance, $account->currency->decimals ?? 2) }}
                                            <span class="text-sm text-white/60 block mt-1 font-medium">{{ $account->currency->symbol }}</span>
                                        </p>
                                        <p class="balance-hidden text-3xl sm:text-4xl font-bold tracking-tight text-white text-balance">••••••</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center justify-center gap-2 mt-3">
                @foreach($accounts as $account)
                    <button onclick="switchWallet('{{ $account->currency->code }}')"
                            class="wallet-dot h-1.5 rounded-full transition-all {{ $loop->first ? 'bg-[var(--primary)] w-5' : 'bg-[var(--line)] w-1.5' }}"
                            aria-label="Switch to {{ $account->name }}"></button>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div>
        <h2 class="section-title">Quick actions</h2>
        <div class="grid grid-cols-4 gap-2 sm:gap-3">
            @foreach([
                ['route' => 'transfer', 'icon' => 'send', 'label' => 'Send'],
                ['route' => 'receive', 'icon' => 'download', 'label' => 'Receive'],
                ['route' => 'cards', 'icon' => 'credit-card', 'label' => 'Cards'],
                ['route' => 'deposit', 'icon' => 'wallet', 'label' => 'Add money'],
            ] as $action)
                <a href="{{ route($action['route']) }}" class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl hover:border-[var(--accent)] hover:shadow-md transition-all group">
                    <span class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                        <x-icon name="{{ $action['icon'] }}" class="w-5 h-5" />
                    </span>
                    <span class="text-xs font-semibold text-[var(--text)] text-center leading-tight">{{ $action['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Pending --}}
    @if($inProgress->count() > 0)
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="section-title mb-0">Pending</h2>
                <a href="{{ route('activity', ['filter' => 'in_review']) }}" class="text-xs font-semibold text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">View all</a>
            </div>
            <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl divide-y divide-[var(--line)] overflow-hidden">
                @foreach($inProgress as $txn)
                    <a href="{{ route('activity', ['filter' => 'in_review']) }}" class="flex items-center gap-3 p-4 hover:bg-[var(--line-soft)] transition-colors">
                        <span class="txn-icon w-10 h-10" style="background: var(--warning-bg); color: var(--warning); border-color: rgba(217, 119, 6, 0.15);">
                            <x-icon name="clock" class="w-4 h-4" />
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[var(--text)] truncate">{{ $txn->memo ?: $txn->type->customerLabel() }}</p>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ $txn->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-semibold text-balance text-[var(--text)]">{{ number_format((float) $txn->amount, $txn->currency->decimals ?? 2) }} {{ $txn->currency->symbol ?? '' }}</p>
                            <x-status-pill :status="$txn->status" :label="$txn->customerStatusLabel()" />
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Recent Transactions --}}
    <section>
        <div class="flex items-center justify-between mb-3">
            <h2 class="section-title mb-0">Recent transactions</h2>
            <a href="{{ route('activity') }}" class="text-xs font-semibold text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">View all</a>
        </div>
        <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl divide-y divide-[var(--line)] overflow-hidden">
            @forelse($recentActivity as $txn)
                <x-txn-row :transaction="$txn" variant="mobile" />
            @empty
                <div class="empty-state">
                    <x-icon name="list-tree" class="w-10 h-10 mx-auto mb-3 text-[var(--text-muted)]" />
                    <p class="text-sm text-[var(--text-muted)]">No activity yet.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Financial Services --}}
    <section>
        <h2 class="section-title">Financial services</h2>
        <a href="{{ route('financial-services') }}" class="flex items-center gap-3 p-4 bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl hover:border-[var(--accent)] hover:shadow-md transition-all">
            <span class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] flex-shrink-0">
                <x-icon name="trending-up" class="w-5 h-5" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-[var(--text)]">Explore financial services</p>
                <p class="text-xs text-[var(--text-muted)]">Loans, insurance and investment products</p>
            </div>
            <x-icon name="chevron-right" class="w-5 h-5 text-[var(--text-muted)] flex-shrink-0" />
        </a>
    </section>

    {{-- Support & resources --}}
    <section>
        <h2 class="section-title">Support &amp; resources</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach([
                ['route' => 'support', 'icon' => 'headphones', 'title' => 'Help & support', 'desc' => 'Get in touch with our team'],
                ['route' => 'learn', 'icon' => 'book-open', 'title' => 'Learn banking', 'desc' => 'Guides on money and security'],
                ['route' => 'recommendations', 'icon' => 'sparkles', 'title' => 'Recommendations', 'desc' => 'Tailored financial services'],
                ['route' => 'policies', 'icon' => 'shield-check', 'title' => 'Policies & terms', 'desc' => 'Legal and privacy information'],
            ] as $item)
                <a href="{{ route($item['route']) }}" class="flex items-center gap-3 p-4 bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl hover:border-[var(--accent)] hover:shadow-md transition-all">
                    <span class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] flex-shrink-0">
                        <x-icon name="{{ $item['icon'] }}" class="w-5 h-5" />
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-[var(--text)]">{{ $item['title'] }}</p>
                        <p class="text-xs text-[var(--text-muted)]">{{ $item['desc'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var track = document.getElementById('wallet-track');
    if (!track) return;

    var slides = Array.from(track.querySelectorAll('[data-currency]'));
    if (slides.length <= 1) return;

    var currentIndex = 0;
    var startX = 0;
    var currentX = 0;
    var isDragging = false;

    function goTo(index) {
        if (index < 0 || index >= slides.length) return;
        currentIndex = index;
        track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
        updateDots();
    }

    function updateDots() {
        document.querySelectorAll('.wallet-dot').forEach(function (btn, index) {
            if (index === currentIndex) {
                btn.classList.add('bg-[var(--primary)]', 'w-5');
                btn.classList.remove('bg-[var(--line)]', 'w-1.5');
            } else {
                btn.classList.remove('bg-[var(--primary)]', 'w-5');
                btn.classList.add('bg-[var(--line)]', 'w-1.5');
            }
        });
    }

    window.switchWallet = function (code) {
        var target = slides.find(function (slide) {
            return slide.getAttribute('data-currency') === code;
        });
        if (!target) return;
        goTo(slides.indexOf(target));
    };

    function endDrag() {
        if (!isDragging) return;
        isDragging = false;
        var diff = startX - currentX;
        if (Math.abs(diff) > 50) {
            if (diff > 0 && currentIndex < slides.length - 1) {
                goTo(currentIndex + 1);
            } else if (diff < 0 && currentIndex > 0) {
                goTo(currentIndex - 1);
            }
        }
    }

    track.addEventListener('touchstart', function (e) {
        startX = e.touches[0].clientX;
        currentX = startX;
        isDragging = true;
    }, { passive: true });

    track.addEventListener('touchmove', function (e) {
        if (!isDragging) return;
        currentX = e.touches[0].clientX;
    }, { passive: true });

    track.addEventListener('touchend', endDrag);

    track.addEventListener('mousedown', function (e) {
        e.preventDefault();
        startX = e.clientX;
        currentX = startX;
        isDragging = true;
    });

    track.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        currentX = e.clientX;
    });

    track.addEventListener('mouseup', endDrag);
    track.addEventListener('mouseleave', function () { isDragging = false; });

    updateDots();
});
</script>
@endsection
