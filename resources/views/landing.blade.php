@extends('layouts.public')

@section('title', 'Secure Banking Infrastructure')
@section('meta_description', 'Bastion Peak is a modern banking platform for secure account management, transfers, and financial operations with role-based access and audit trails.')

@section('content')
    {{-- Hero --}}
    <section class="relative pt-20 lg:pt-28 pb-16 lg:pb-24">
        <div class="absolute inset-0 bg-[var(--card-navy)] overflow-hidden">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?fm=jpg&q=80&w=2000&auto=format&fit=crop" alt="" class="w-full h-full object-cover" loading="eager">
                <div class="absolute inset-0 bg-black/55"></div>
            </div>
            <div class="absolute top-1/4 right-0 w-2/3 h-1 bg-gradient-to-r from-[var(--accent-light)]/40 to-transparent"></div>
            <div class="absolute bottom-1/3 left-0 w-1/3 h-1 bg-gradient-to-r to-[var(--accent-light)]/30 from-transparent"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-6">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/20 rounded-full text-xs font-medium text-blue-100 mb-6">
                    <span class="w-2 h-2 bg-[var(--accent-light)] rounded-full"></span>
                    Banking infrastructure for modern teams
                </span>
                <h1 class="text-4xl lg:text-5xl font-bold text-white tracking-tight leading-[1.05] mb-6">
                    Move money. Manage risk. Scale globally.
                </h1>
                <p class="text-lg text-blue-100 leading-relaxed mb-8 max-w-xl">
                    A banking platform built for scale — with approval flows, ledger integrity, and real-time visibility across accounts, cards, and merchants.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3.5 text-sm font-semibold text-[var(--card-navy)] bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                        Get started
                        <x-icon name="arrow-right" class="ml-2 w-4 h-4" />
                    </a>
                    <a href="#section-features" class="inline-flex items-center justify-center px-6 py-3.5 text-sm font-medium text-white bg-white/10 hover:bg-white/15 border border-white/20 rounded-xl transition-colors">
                        Watch demo
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Trust bar --}}
    <section class="py-10 border-t border-[var(--line)] bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-center text-sm text-[var(--text-muted)] mb-6">Trusted by teams at</p>
            <div class="flex flex-wrap items-center justify-center gap-6 md:gap-8">
                <span class="text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">SOC 2 Type II</span>
                <span class="text-xs font-medium text-[var(--text-muted)]">·</span>
                <span class="text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">256-bit encryption</span>
                <span class="text-xs font-medium text-[var(--text-muted)]">·</span>
                <span class="text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">99.9% uptime</span>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="section-features" class="py-20 lg:py-28 bg-[var(--bg)]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Everything you need to manage money</h2>
                <p class="mt-4 text-lg text-[var(--text-muted)] max-w-2xl mx-auto">Powerful tools for accounts, payments, cards, and reporting — designed for security and speed.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5">
                        <x-icon name="layout" class="w-5 h-5" />
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Account management</h3>
                    <p class="text-sm text-[var(--text-muted)] leading-relaxed">Open and manage multiple account types with full transaction history and balance tracking.</p>
                </div>
                <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5">
                        <x-icon name="send" class="w-5 h-5" />
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Instant transfers</h3>
                    <p class="text-sm text-[var(--text-muted)] leading-relaxed">Move money between accounts or pay merchants with real-time processing and approval flows.</p>
                </div>
                <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5">
                        <x-icon name="credit-card" class="w-5 h-5" />
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Virtual cards</h3>
                    <p class="text-sm text-[var(--text-muted)] leading-relaxed">Issue virtual cards instantly, set spending limits, and freeze or replace cards in one click.</p>
                </div>
                <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5">
                        <x-icon name="list" class="w-5 h-5" />
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Reporting &amp; insights</h3>
                    <p class="text-sm text-[var(--text-muted)] leading-relaxed">Generate statements, review audit logs, and monitor system health with live dashboards.</p>
                </div>
                <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5">
                        <x-icon name="shield-check" class="w-5 h-5" />
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Enterprise security</h3>
                    <p class="text-sm text-[var(--text-muted)] leading-relaxed">Role-based access, audit trails, and encrypted transactions protect every operation.</p>
                </div>
                <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5">
                        <x-icon name="settings" class="w-5 h-5" />
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Automated controls</h3>
                    <p class="text-sm text-[var(--text-muted)] leading-relaxed">Configure limits, approval queues, and anomaly detection to reduce risk.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Customer testimonials --}}
    <section class="py-20 bg-white border-t border-[var(--line)]">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-2xl lg:text-3xl font-bold text-[var(--text)] tracking-tight">What our customers say</h2>
                <p class="mt-3 text-sm text-[var(--text-muted)]">Hear from teams that have transformed their banking operations.</p>
            </div>

            <div class="relative" id="review-slider">
                <div id="review-track" class="transition-opacity duration-300">
                    <div class="bg-[var(--line-soft)] rounded-2xl p-8">
                        <div class="flex items-center gap-1 mb-4 text-[var(--warning)]">
                            <x-icon name="star" class="w-4 h-4 fill-current" />
                            <x-icon name="star" class="w-4 h-4 fill-current" />
                            <x-icon name="star" class="w-4 h-4 fill-current" />
                            <x-icon name="star" class="w-4 h-4 fill-current" />
                            <x-icon name="star" class="w-4 h-4 fill-current" />
                        </div>
                        <p class="text-lg text-[var(--text)] leading-relaxed mb-5">
                            "Bastion Peak replaced five different tools with a single platform. The approval flows alone saved us 12 hours a week in manual reconciliation. Our finance team loves it."
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] font-bold">MJ</div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-[var(--text)]">Maria Johnson</p>
                                <p class="text-xs text-[var(--text-muted)]">Finance Lead, Apex Labs</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center gap-2 mt-8" id="review-nav">
                    <button onclick="prevReview()" class="w-10 h-10 rounded-full bg-white border border-[var(--line)] flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-colors min-h-[44px] min-w-[44px]">
                        <x-icon name="chevron-left" class="w-5 h-5" />
                    </button>
                    <span class="flex items-center text-xs text-[var(--text-muted)]" id="review-counter">1 / 5</span>
                    <button onclick="nextReview()" class="w-10 h-10 rounded-full bg-white border border-[var(--line)] flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-colors min-h-[44px] min-w-[44px]">
                        <x-icon name="chevron-right" class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Key benefits --}}
    <section class="py-20 lg:py-28 bg-[var(--bg)]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Built for operations, not just optics</h2>
                <p class="mt-4 text-lg text-[var(--text-muted)] max-w-2xl mx-auto">Most banking dashboards show pretty charts. Bastion Peak is built to move money safely — with approval flows, ledger integrity, and real-time visibility across accounts, cards, and merchants.</p>
            </div>

            <div class="max-w-3xl mx-auto">
                <div class="bg-white border border-[var(--line)] rounded-2xl p-8">
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <x-icon name="check" class="w-5 h-5 text-[var(--accent)] mt-0.5 flex-shrink-0" />
                            <span class="text-sm text-[var(--text-muted)]">Double-entry ledger with automatic balance recalculation</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <x-icon name="check" class="w-5 h-5 text-[var(--accent)] mt-0.5 flex-shrink-0" />
                            <span class="text-sm text-[var(--text-muted)]">Role-based access for customers and operators</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <x-icon name="check" class="w-5 h-5 text-[var(--accent)] mt-0.5 flex-shrink-0" />
                            <span class="text-sm text-[var(--text-muted)]">Complete audit trail for every transaction and change</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20 bg-[var(--primary)]">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-white tracking-tight">Ready to move to modern banking infrastructure?</h2>
            <p class="mt-4 text-lg text-blue-200">Join teams that trust Bastion Peak for secure financial operations.</p>
            <div class="mt-10">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-semibold text-[var(--primary)] bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                    Get started
                    <x-icon name="arrow-right" class="ml-2 w-4 h-4" />
                </a>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var reviews = [
        {
            avatar: 'MJ',
            name: 'Maria Johnson',
            title: 'Finance Lead, Apex Labs',
            rating: 5,
            text: '"Bastion Peak replaced five different tools with a single platform. The approval flows alone saved us 12 hours a week in manual reconciliation. Our finance team loves it."'
        },
        {
            avatar: 'TR',
            name: 'Thomas Rivera',
            title: 'Treasury Manager, Nexus Corp',
            rating: 5,
            text: '"The real-time audit trail gives us confidence in every transaction. We can see exactly who did what and when — no more chasing down emails."'
        },
        {
            avatar: 'SL',
            name: 'Sarah Lin',
            title: 'CTO, Vertex Bank',
            rating: 5,
            text: '"SOC 2 compliance was non-negotiable for us. Bastion Peak handled it out of the box — we were live in two weeks, not two quarters."'
        },
        {
            avatar: 'DK',
            name: 'David Kim',
            title: 'Operations Director, Summit Group',
            rating: 5,
            text: '"The double-entry ledger is rock-solid. After three years on Bastion Peak, we have zero balance mismatches. That\'s worth its weight in gold."'
        },
        {
            avatar: 'AE',
            name: 'Amira El-Sayed',
            title: 'Head of Treasury, Meridian Capital',
            rating: 5,
            text: '"Virtual cards with spending limits have eliminated fraud losses. The API-first approach made integration seamless with our existing stack."'
        }
    ];

    var currentReview = 0;
    var track = document.getElementById('review-track');
    var counter = document.getElementById('review-counter');

    function starSvg() {
        return '<svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L20 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 4 9.27l4.91-1.01L12 2z"/></svg>';
    }

    function renderReview(index) {
        var r = reviews[index];
        var stars = '';
        for (var i = 0; i < r.rating; i++) {
            stars += starSvg();
        }
        track.innerHTML =
            '<div class="bg-[var(--line-soft)] rounded-2xl p-8">' +
            '<div class="flex items-center gap-1 mb-4 text-[var(--warning)]">' + stars + '</div>' +
            '<p class="text-lg text-[var(--text)] leading-relaxed mb-5">' + r.text + '</p>' +
            '<div class="flex items-center gap-3">' +
            '<div class="w-10 h-10 rounded-full bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] font-bold">' + r.avatar + '</div>' +
            '<div class="text-left">' +
            '<p class="text-sm font-semibold text-[var(--text)]">' + r.name + '</p>' +
            '<p class="text-xs text-[var(--text-muted)]">' + r.title + '</p>' +
            '</div></div></div>';
    }

    function updateCounter() {
        counter.textContent = (currentReview + 1) + ' / 5';
    }

    window.nextReview = function () {
        currentReview = (currentReview + 1) % reviews.length;
        renderReview(currentReview);
        updateCounter();
    };

    window.prevReview = function () {
        currentReview = currentReview === 0 ? reviews.length - 1 : currentReview - 1;
        renderReview(currentReview);
        updateCounter();
    };

    renderReview(currentReview);
    updateCounter();
});
</script>
@endsection
