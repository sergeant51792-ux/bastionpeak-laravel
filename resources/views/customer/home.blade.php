@extends('layouts.customer')

@section('title', 'Home')

@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-6" id="customer-dashboard">
    <div class="flex items-center justify-between w-full">
        <div class="min-w-0 flex-1">
            <p class="text-xs text-[var(--text-muted)] sm:text-sm">Welcome back</p>
            <h1 class="text-lg sm:text-xl font-bold text-[var(--text)] tracking-tight truncate">{{ $user->name }}</h1>
        </div>
        <a href="{{ route('profile') }}" class="relative flex-shrink-0 ml-3">
            @if($user->profile_picture_url)
                <img src="{{ $user->profile_picture_url }}" alt="Profile" class="w-10 sm:w-12 h-10 sm:h-12 rounded-full object-cover ring-2 sm:ring-[var(--primary)]/20 ring-1 ring-[var(--primary)]/10">
            @else
                <div class="w-10 sm:w-12 h-10 sm:h-12 rounded-full bg-[var(--primary)] flex items-center justify-center text-white font-bold text-sm sm:text-xl">
                    {{ strtoupper(substr($user->name ?? $user->email, 0, 2)) }}
                </div>
            @endif
        </a>
    </div>

    {{-- Active Card --}}
    @if($activeCard)
        <div class="relative">
            <div class="card-chip rounded-3xl p-6 text-white overflow-hidden" style="background: radial-gradient(120% 120% at 20% 30%, #3b82f6 0%, #1e40af 40%, #0f172a 100%);">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-white/20 to-transparent rounded-full -translate-y-1/2 translate-x-1/4"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-white/10 to-transparent rounded-full -translate-x-1/3 translate-y-1/3"></div>
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12">
                        <x-icon name="credit-card" class="w-12 h-12" />
                    </div>
                        <span class="text-xs font-medium bg-white/15 px-2.5 py-1 rounded-full backdrop-blur-sm">{{ $activeCard->card_type === 'virtual' ? 'Virtual' : 'Physical' }}</span>
                    </div>
                    <div class="font-mono text-xl tracking-wider mb-4" style="font-variant-numeric: tabular-nums;">
                        {{ $activeCard->card_number_masked }}
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <p class="text-xs text-white/60 uppercase tracking-wider">Card holder</p>
                            <p class="font-medium">{{ $user->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-white/60 uppercase tracking-wider">Expires</p>
                            <p class="font-medium">{{ $activeCard->valid_to ? $activeCard->valid_to->format('m/y') : '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Wallet Balance --}}
    <div>
        @if(count($walletBalances) > 0)
            <div id="wallet-slider" class="relative overflow-hidden">
                <div id="wallet-track" class="flex transition-transform duration-500 ease-out">
                    @foreach($walletBalances as $code => $wallet)
                        <div class="w-full flex-shrink-0" data-currency="{{ $code }}">
                            <div class="card-balance h-44 sm:h-48">
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between mb-4">
                                     <div>
                                            <p class="text-sm text-white/70 font-medium">{{ $wallet['name'] }}</p>
                                            <p class="font-semibold text-sm text-white">Main wallet</p>
                                        </div>
                                        <span class="text-sm font-bold text-white/90 bg-white/15 px-3 py-1 rounded-lg backdrop-blur-sm border border-white/10">{{ $wallet['symbol'] }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-white/60 mb-1 uppercase tracking-wider font-medium">Available balance</p>
                                        <p class="text-3xl sm:text-4xl font-bold tracking-tight text-white text-balance" data-balance-sensitive>
                                            {{ number_format((float) $wallet['balance'], $wallet['decimals']) }}
                                            <span class="text-sm text-white/60 block mt-1 font-medium">{{ $wallet['symbol'] }}</span>
                                        </p>
                                        <p class="balance-hidden text-3xl sm:text-4xl font-bold tracking-tight text-white text-balance">
                                            ••••
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center justify-center gap-2 mt-3">
                @foreach($walletBalances as $code => $wallet)
                    <button onclick="switchWallet('{{ $code }}')" class="wallet-dot h-2 rounded-full transition-all {{ ($walletCurrency === $code || ($loop->first && !$walletCurrency)) ? 'bg-white w-4' : 'bg-white/40 w-2' }}" aria-label="Switch to {{ $wallet['name'] }}"></button>
                @endforeach
            </div>
        @else
            <div id="wallet-slider" class="relative overflow-hidden">
                <div id="wallet-track" class="flex transition-transform duration-500 ease-out">
                    @foreach($accounts as $account)
                        <div class="w-full flex-shrink-0" data-currency="{{ $account->currency->code }}">
                            <div class="card-balance h-44 sm:h-48">
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <p class="text-sm text-white/70 font-medium">{{ $account->currency->name ?? $account->currency->code }}</p>
                                            <p class="font-semibold text-sm text-white">{{ $account->name }}</p>
                                        </div>
                                        <span class="text-sm font-bold text-white/90 bg-white/15 px-3 py-1 rounded-lg backdrop-blur-sm border border-white/10">{{ $account->currency->symbol }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-white/60 mb-1 uppercase tracking-wider font-medium">Available balance</p>
                                        <p class="text-3xl sm:text-4xl font-bold tracking-tight text-white text-balance" data-balance-sensitive>
                                            {{ number_format((float) $account->balance, $account->currency->decimals ?? 2) }}
                                            <span class="text-sm text-white/60 block mt-1 font-medium">{{ $account->currency->symbol }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center justify-center gap-2 mt-3">
                @foreach($accounts as $account)
                    <button onclick="switchWallet('{{ $account->currency->code }}')" class="wallet-dot h-2 rounded-full transition-all {{ $loop->first ? 'bg-white w-4' : 'bg-white/40 w-2' }}" aria-label="Switch to {{ $account->name }}"></button>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="mt-6">
        <h2 class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-3">Quick actions</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('transfer') }}" class="flex flex-col items-center gap-2 p-4 bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl hover:border-[var(--accent)] hover:shadow-md transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                    <x-icon name="send" class="w-5 h-5" />
                </div>
                <span class="text-xs font-semibold text-[var(--text)]">Send</span>
            </a>
            <a href="{{ route('receive') }}" class="flex flex-col items-center gap-2 p-4 bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl hover:border-[var(--accent)] hover:shadow-md transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                    <x-icon name="download" class="w-5 h-5" />
                </div>
                <span class="text-xs font-semibold text-[var(--text)]">Receive</span>
            </a>
            <a href="{{ route('cards') }}" class="flex flex-col items-center gap-2 p-4 bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl hover:border-[var(--accent)] hover:shadow-md transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                    <x-icon name="credit-card" class="h-5 w-5" />
                </div>
                <span class="text-xs font-semibold text-[var(--text)]">My cards</span>
            </a>
            <a href="{{ route('deposit') }}" class="flex flex-col items-center gap-2 p-4 bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl hover:border-[var(--accent)] hover:shadow-md transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                    <x-icon name="wallet" class="w-5 h-5" />
                </div>
                <span class="text-xs font-semibold text-[var(--text)]">Deposit</span>
            </a>
        </div>
    </div>

    {{-- Financial Services --}}
    <section class="mt-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="section-title">
                <x-icon name="trending-up" class="w-4 h-4 text-[var(--accent)]" />
                Financial services
            </h2>
            <a href="{{ route('financial-services') }}" class="text-xs font-semibold text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">View all</a>
        </div>
        <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-4 text-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)]">
                    <x-icon name="trending-up" class="w-6 h-6" />
                </div>
                <p class="font-semibold text-[var(--text)]">Explore financial services</p>
                <p class="text-xs text-[var(--text-muted)]">Loans, insurance, and investment products tailored to your needs</p>
                <a href="{{ route('financial-services') }}" class="text-xs font-semibold text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">Get started →</a>
            </div>
        </div>
    </section>

    {{-- Pending --}}
    @if($inProgress->count() > 0)
        <section>
            <h2 class="section-title flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                Pending
            </h2>
            <div class="bg-white border border-[var(--line)] rounded-2xl divide-y divide-[var(--line)]">
                @foreach($inProgress as $txn)
                    <a href="#" class="flex items-center justify-between p-4 hover:bg-[var(--line-soft)] transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                                <x-icon name="clock" class="w-4 h-4" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[var(--text)]">{{ $txn->memo ?? ($txn->type instanceof \App\Enums\TransactionType ? $txn->type->label() : '—') }}</p>
                                <p class="text-xs text-[var(--text-muted)]">{{ $txn->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <span @class(['badge', 'bg-amber-50 text-amber-700 border border-amber-200' => $txn->status === \App\Enums\TransactionStatus::PendingReview, 'bg-blue-50 text-blue-700 border border-blue-200' => $txn->status === \App\Enums\TransactionStatus::PendingMatch])>
                            {{ $txn->status instanceof \App\Enums\TransactionStatus ? $txn->status->label() : ($txn->status ?? '—') }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Recent Transactions --}}
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="section-title mb-0">Recent transactions</h2>
            <a href="{{ route('activity') }}" class="text-xs font-semibold text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">View all</a>
        </div>
        <div class="bg-white border border-[var(--line)] rounded-2xl divide-y divide-[var(--line)]">
            @forelse($recentActivity as $txn)
                <a href="#" class="flex items-center justify-between p-4 hover:bg-[var(--line-soft)] transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[var(--line-soft)] flex items-center justify-center text-[var(--text-muted)] group-hover:bg-[var(--accent-subtle)] group-hover:text-[var(--accent)] transition-all">
                            <x-icon name="external-link" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--text)]">{{ $txn->memo ?? ($txn->type instanceof \App\Enums\TransactionType ? $txn->type->label() : '—') }}</p>
                            <p class="text-xs text-[var(--text-muted)]">{{ $txn->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-balance {{ in_array($txn->type->value ?? $txn->type, ['deposit_credit', 'credit', 'refund']) ? 'text-emerald-600' : 'text-[var(--text)]' }}">
                        {{ in_array($txn->type->value ?? $txn->type, ['deposit_credit', 'credit', 'refund']) ? '+' : '-' }}{{ number_format((float) $txn->amount, $txn->currency->decimals ?? 2) }} {{ $txn->currency->symbol ?? '' }}
                    </span>
                </a>
            @empty
                <div class="empty-state">
                    <p class="text-sm text-[var(--text-muted)]">No activity yet.</p>
                </div>
            @endforelse
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
        var buttons = document.querySelectorAll('.wallet-dot');
        buttons.forEach(function (btn, index) {
            if (index === currentIndex) {
                btn.classList.add('bg-white', 'w-4');
                btn.classList.remove('bg-white/40', 'w-2');
            } else {
                btn.classList.remove('bg-white', 'w-4');
                btn.classList.add('bg-white/40', 'w-2');
            }
        });
    }

    window.switchWallet = function (code) {
        var target = slides.find(function (slide) {
            return slide.getAttribute('data-currency') === code;
        });
        if (!target) return;
        var index = slides.indexOf(target);
        goTo(index);
    };

    track.addEventListener('touchstart', function (e) {
        startX = e.touches[0].clientX;
        currentX = startX;
        isDragging = true;
    }, { passive: true });

    track.addEventListener('touchmove', function (e) {
        if (!isDragging) return;
        currentX = e.touches[0].clientX;
    }, { passive: true });

    track.addEventListener('touchend', function () {
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
    });

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

    track.addEventListener('mouseup', function () {
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
    });

    track.addEventListener('mouseleave', function () {
        if (!isDragging) return;
        isDragging = false;
    });

    updateDots();
});
</script>
@endsection
