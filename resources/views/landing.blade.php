<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bastion Peak — Secure Banking Infrastructure</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="description" content="Bastion Peak is a modern banking platform for secure account management, transfers, and financial operations.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(30, 58, 138, 0.85)), url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?fm=jpg&q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
        .about-bg {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.88), rgba(30, 58, 138, 0.8)), url('https://images.unsplash.com/photo-1554469384-e58fac16e23a?fm=jpg&q=80&w=1200&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-white text-[var(--text)]">
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-[var(--line)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <x-bp-logo :width="28" :height="28" />
                <span class="font-semibold text-[var(--primary)] tracking-tight">Bastion Peak</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('signin') }}" class="px-4 py-2 text-sm font-medium text-[var(--text)] hover:text-[var(--primary)] transition-colors">Log in</a>
                <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-semibold text-white bg-[var(--primary)] hover:bg-[var(--primary-dark)] rounded-xl transition-colors shadow-sm">Get started</a>
            </div>
        </div>
    </nav>

    <main>
        <section class="hero-bg pt-32 pb-20 lg:pt-44 lg:pb-32">
            <div class="max-w-7xl mx-auto px-6">
                <div class="max-w-3xl">
                    <h1 class="text-4xl lg:text-6xl font-bold text-white tracking-tight leading-[1.1]">
                        Banking infrastructure for modern teams
                    </h1>
                    <p class="mt-6 text-lg text-blue-100 leading-relaxed max-w-2xl">
                        Secure account management, instant transfers, and complete financial visibility — all in one platform built for scale.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3.5 text-sm font-semibold text-[var(--primary)] bg-white hover:bg-blue-50 rounded-xl transition-colors shadow-lg">
                            Get started
                            <x-icon name="arrow-right" class="ml-2 w-4 h-4" />
                        </a>
                        <a href="#section-features" class="inline-flex items-center justify-center px-6 py-3.5 text-sm font-semibold text-white bg-[var(--primary)]/40 hover:bg-[var(--primary)]/60 border border-[var(--primary)]/30 rounded-xl transition-colors backdrop-blur-sm">
                            Learn more
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Trust indicators --}}
        <section class="py-12 bg-white border-t border-[var(--line)]">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                    <div>
                        <p class="text-3xl lg:text-4xl font-bold text-[var(--primary)] tracking-tight">99.9%</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Uptime SLA</p>
                    </div>
                    <div>
                        <p class="text-3xl lg:text-4xl font-bold text-[var(--primary)] tracking-tight">256-bit</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Encryption</p>
                    </div>
                    <div>
                        <p class="text-3xl lg:text-4xl font-bold text-[var(--primary)] tracking-tight">&lt;2s</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Transfer speed</p>
                    </div>
                    <div>
                        <p class="text-3xl lg:text-4xl font-bold text-[var(--primary)] tracking-tight">SOC 2</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Certified</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="section-features" class="py-20 lg:py-28 bg-[var(--bg)]">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Everything you need to manage money</h2>
                    <p class="mt-4 text-lg text-[var(--text-muted)] max-w-2xl mx-auto">Powerful tools for accounts, payments, cards, and reporting — designed for security and speed.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5 group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                            <x-icon name="layout" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Account management</h3>
                        <p class="text-sm text-[var(--text-muted)] leading-relaxed">Open and manage multiple account types with full transaction history and balance tracking.</p>
                    </div>
                    <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5 group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                            <x-icon name="send" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Instant transfers</h3>
                        <p class="text-sm text-[var(--text-muted)] leading-relaxed">Move money between accounts or pay merchants with real-time processing and approval.</p>
                    </div>
                    <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5 group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                            <x-icon name="credit-card" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Virtual cards</h3>
                        <p class="text-sm text-[var(--text-muted)] leading-relaxed">Issue virtual cards instantly, set spending limits, and freeze or replace cards in one click.</p>
                    </div>
                    <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5 group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                            <x-icon name="list" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Reporting &amp; insights</h3>
                        <p class="text-sm text-[var(--text-muted)] leading-relaxed">Generate statements, review audit logs, and monitor system health with live dashboards.</p>
                    </div>
                    <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5 group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                            <x-icon name="shield-check" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Enterprise security</h3>
                        <p class="text-sm text-[var(--text-muted)] leading-relaxed">Role-based access, audit trails, and encrypted transactions protect every operation.</p>
                    </div>
                    <div class="bg-white border border-[var(--line)] p-8 rounded-2xl hover:border-[var(--accent)] hover:shadow-lg transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5 group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                            <x-icon name="shield-check" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Automated controls</h3>
                        <p class="text-sm text-[var(--text-muted)] leading-relaxed">Configure limits, approval queues, and anomaly detection to reduce risk.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Reviews Slider --}}
        <section class="py-20 bg-white border-t border-[var(--line)]">
            <div class="max-w-4xl mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-2xl lg:text-3xl font-bold text-[var(--text)] tracking-tight">What our customers say</h2>
                    <p class="mt-3 text-sm text-[var(--text-muted)]">Hear from teams that have transformed their banking operations.</p>
                </div>

                <div class="relative" id="review-slider">
                    <div id="review-track" class="transition-transform duration-500 ease-in-out">
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
                </div>

                <div class="flex justify-center gap-2 mt-8">
                    <button onclick="prevReview()" class="w-10 h-10 rounded-full bg-white border border-[var(--line)] flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-colors min-h-[44px] min-w-[44px]">
                        <x-icon name="chevron-left" class="w-5 h-5" />
                    </button>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs text-[var(--text-muted)">1 / 5</span>
                    </div>
                    <button onclick="nextReview()" class="w-10 h-10 rounded-full bg-white border border-[var(--line)] flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-colors min-h-[44px] min-w-[44px]">
                        <x-icon name="chevron-right" class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </section>

        {{-- Plain images grid --}}
        <section class="py-20 bg-[var(--bg)]">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Built for operations, not just optics</h2>
                    <p class="mt-4 text-lg text-[var(--text-muted)] max-w-2xl mx-auto">Most banking dashboards show pretty charts. Bastion Peak is built to move money safely — with approval flows, ledger integrity, and real-time visibility across accounts, cards, and merchants.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <div class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden h-48">
                        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?fm=jpg&q=80&w=400&auto=format&fit=crop" alt="Dashboard overview" class="w-full h-full object-cover">
                    </div>
                    <div class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden h-48">
                        <img src="https://images.unsplash.com/photo-1554469384-e58fac16e23a?fm=jpg&q=80&w=400&auto=format&fit=crop" alt="Financial insights" class="w-full h-full object-cover">
                    </div>
                    <div class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden h-48">
                        <img src="https://images.unsplash.com/photo-1551288326-c0a93f84f3c1?fm=jpg&q=80&w=400&auto=format&fit=crop" alt="Transaction monitoring" class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="bg-white border border-[var(--line)] rounded-2xl p-8">
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-sm text-[var(--text-muted)]">
                            <x-icon name="check" class="w-5 h-5 text-[var(--accent)] mt-0.5 flex-shrink-0" />
                            <span>Double-entry ledger with automatic balance recalculation</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-[var(--text-muted)]">
                            <x-icon name="check" class="w-5 h-5 text-[var(--accent)] mt-0.5 flex-shrink-0" />
                            <span>Role-based access for customers and operators</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-[var(--text-muted)]">
                            <x-icon name="check" class="w-5 h-5 text-[var(--accent)] mt-0.5 flex-shrink-0" />
                            <span>Complete audit trail for every transaction and change</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="py-20 bg-[var(--accent-subtle)]">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <h2 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Ready to move to modern banking infrastructure?</h2>
                <p class="mt-4 text-lg text-[var(--text-muted)]">Join teams that trust Bastion Peak for secure financial operations.</p>
                <div class="mt-10">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-semibold text-white bg-[var(--primary)] hover:bg-[var(--primary-dark)] rounded-xl transition-colors shadow-lg">
                        Get started
                        <x-icon name="arrow-right" class="ml-2 w-4 h-4" />
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-white border-t border-[var(--line)] py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-2.5">
                    <x-bp-logo :width="24" :height="24" />
                    <span class="text-sm font-semibold text-[var(--text)]">Bastion Peak</span>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-6 text-sm text-[var(--text-muted)]">
                    <a href="{{ route('support') }}" class="hover:text-[var(--primary)]">Support</a>
                    <a href="{{ route('learn') }}" class="hover:text-[var(--primary)]">Learn</a>
                    <a href="{{ route('recommendations') }}" class="hover:text-[var(--primary)]">Recommendations</a>
                    <a href="{{ route('policies') }}" class="hover:text-[var(--primary)]">Policies</a>
                    <a href="{{ route('terms') }}" class="hover:text-[var(--primary)]">Terms</a>
                    <a href="{{ route('privacy') }}" class="hover:text-[var(--primary)]">Privacy</a>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-[var(--line)] text-center">
                <p class="text-xs text-[var(--text-muted)]">&copy; 1998–2026 Bastion Peak. All rights reserved.</p>
            </div>
        </div>
    </footer>

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
                text: '"The real-time audit trail gives us confidence in every transaction. We can see exactly who did what and when — no more chasing down emails.'"'
            },
            {
                avatar: 'SL',
                name: 'Sarah Lin',
                title: 'CTO, Vertex Bank',
                rating: 5,
                text: '"SOC 2 compliance was non-negotiable for us. Bastion Peak handled it out of the box — we were live in two weeks, not two quarters.'"'
            },
            {
                avatar: 'DK',
                name: 'David Kim',
                title: 'Operations Director, Summit Group',
                rating: 5,
                text: '"The double-entry ledger is rock-solid. After three years on Bastion Peak, we have zero balance mismatches. That's worth its weight in gold.'"'
            },
            {
                avatar: 'AE',
                name: 'Amira El-Sayed',
                title: 'Head of Treasury, Meridian Capital',
                rating: 5,
                text: '"Virtual cards with spending limits have eliminated fraud losses. The API-first approach made integration seamless with our existing stack.'"'
            }
        ];

        var currentReview = 0;
        var track = document.getElementById('review-track');
        var counter = document.querySelector('text-xs.text-\\[var\\(--text-muted\\)\\]');

        function renderReview(index) {
            var r = reviews[index];
            var stars = '';
            for (var i = 0; i < r.rating; i++) {
                stars += '<x-icon name="star" class="w-4 h-4 fill-current" />';
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
            var span = document.querySelector('#review-slider .flex .text-xs');
            if (span) span.textContent = (currentReview + 1) + ' / 5';
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
</body>
</html>
