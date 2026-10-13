<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bastion Peak — Secure Banking Infrastructure</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="description" content="Bastion Peak is a modern banking platform for secure account management, transfers, and financial operations with role-based access and audit trails.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    </style>
</head>
<body class="bg-white text-[var(--text)]">

    {{-- Navigation --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-[var(--line)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                <x-bp-logo :width="28" :height="28" />
                <span class="font-bold text-[var(--primary-dark)] tracking-tight text-xl">Bastion Peak</span>
            </a>
            <div class="hidden lg:flex items-center gap-8">
                <a href="#fp-features" class="text-sm font-medium text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Features</a>
                <a href="#fp-security" class="text-sm font-medium text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Security</a>
                <a href="#fp-cta" class="text-sm font-medium text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">See it in action</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('signin') }}" class="px-4 py-2 text-sm font-medium text-[var(--text)] hover:text-[var(--primary-dark)] transition-colors">Log in</a>
                <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-semibold text-white bg-[var(--primary-dark)] hover:bg-[var(--primary)] rounded-md transition-colors shadow-sm">
                    Get started
                </a>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="relative h-screen flex items-center pt-16">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?fm=jpg&q=80&w=2000&auto=format&fit=crop" alt="Banking infrastructure" class="w-full h-full object-cover" loading="eager">
            <div class="absolute inset-0 bg-gradient-to-b from-black/75 via-black/50 to-black/40"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-6 w-full">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2.5 px-4 py-2 bg-white/5 border border-white/20 rounded-full text-xs font-medium text-blue-100 mb-8">
                    <span class="w-2 h-2 bg-blue-300 rounded-full"></span>
                    Banking infrastructure for modern teams
                </div>
                <h1 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white tracking-tight leading-[1.05] mb-6">
                    Move money.<br>Manage risk.<br>Scale globally.
                </h1>
                <p class="text-lg text-blue-100 leading-relaxed mb-8 max-w-xl">
                    A banking platform built for scale — with approval flows, double-entry ledger integrity, and real-time visibility across accounts, cards, and merchants.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3.5 text-sm font-semibold text-[var(--primary-dark)] bg-white rounded-md shadow-lg hover:shadow-xl transition-shadow">
                        Get started
                        <x-icon name="arrow-right" class="ml-2 w-4 h-4" />
                    </a>
                    <a href="#fp-features" class="inline-flex items-center justify-center px-6 py-3.5 text-sm font-medium text-white bg-white/10 hover:bg-white/15 border border-white/20 rounded-md transition-colors">
                        Learn more
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Trust bar --}}
    <section class="py-14 bg-white border-t border-[var(--line)]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <p class="text-3xl font-bold text-[var(--primary-dark)] mb-1">99.9%</p>
                    <p class="text-sm text-[var(--text-muted)]">Uptime SLA</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-[var(--primary-dark)] mb-1">256-bit</p>
                    <p class="text-sm text-[var(--text-muted)]">Encryption</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-[var(--primary-dark)] mb-1">&lt;2s</p>
                    <p class="text-sm text-[var(--text-muted)]">Transfer speed</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-[var(--primary-dark)] mb-1">SOC 2</p>
                    <p class="text-sm text-[var(--text-muted)]">Certified</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Features - text-first layout, no cards --}}
    <section id="fp-features" class="py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-6">
            <div class="max-w-3xl mb-20">
                <h2 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Features built for compliance</h2>
                <p class="mt-4 text-lg text-[var(--text-muted)]">Everything your finance team needs to move money safely, with proper controls and full visibility.</p>
            </div>

            <div class="space-y-16">
                {{-- Feature 1: Ledger --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="order-2 lg:order-1">
                        <h3 class="text-2xl font-bold text-[var(--text)] mb-4">Double-entry ledger</h3>
                        <p class="text-lg text-[var(--text-muted)] leading-relaxed mb-6">Every transaction creates two offsetting entries — debits and credits — ensuring mathematical accuracy. Balances are recalculated automatically, and every write is immutable in the audit trail.</p>
                        <div class="flex items-center gap-6 text-sm">
                            <span class="flex items-center gap-2 text-[var(--text-muted)]">
                                <x-icon name="check" class="w-4 h-4 text-[var(--accent)]" /> Real-time balance
                            </span>
                            <span class="flex items-center gap-2 text-[var(--text-muted)]">
                                <x-icon name="check" class="w-4 h-4 text-[var(--accent)]" /> Immutable entries
                            </span>
                        </div>
                    </div>
                    <div class="order-1 lg:order-2">
                        <div class="bg-[var(--accent-subtle)] rounded-xl p-2">
                            <img src="https://images.unsplash.com/photo-1551285788-a3146e8efb9f?fm=jpg&q=80&w=800&auto=format&fit=crop" alt="" class="w-full h-auto rounded-lg" loading="lazy">
                        </div>
                    </div>
                </div>

                {{-- Feature 2: Approval flows --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-[var(--text)] mb-4">Approval workflows</h3>
                        <p class="text-lg text-[var(--text-muted)] leading-relaxed mb-6">Configure multi-step approval chains with per-user limits, escalation rules, and real-time notifications. Every action is logged with timestamps and actor attribution.</p>
                        <div class="flex items-center gap-6 text-sm">
                            <span class="flex items-center gap-2 text-[var(--text-muted)]">
                                <x-icon name="check" class="w-4 h-4 text-[var(--accent)]" /> Custom policies
                            </span>
                            <span class="flex items-center gap-2 text-[var(--text-muted)]">
                                <x-icon name="check" class="w-4 h-4 text-[var(--accent)]" /> Full audit trail
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="bg-[var(--accent-subtle)] rounded-xl p-2">
                            <img src="https://images.unsplash.com/photo-1552667468-071e6a5c7c5a?fm=jpg&q=80&w=800&auto=format&fit=crop" alt="" class="w-full h-auto rounded-lg" loading="lazy">
                        </div>
                    </div>
                </div>

                {{-- Feature 3: Security --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="order-2 lg:order-1">
                        <h3 class="text-2xl font-bold text-[var(--text)] mb-4">Enterprise-grade security</h3>
                        <p class="text-lg text-[var(--text-muted)] leading-relaxed mb-6">SOC 2 Type II certified with 256-bit encryption at rest and in transit. Role-based access controls, session management, and real-time anomaly detection protect your infrastructure.</p>
                        <div class="flex items-center gap-6 text-sm">
                            <span class="flex items-center gap-2 text-[var(--text-muted)]">
                                <x-icon name="check" class="w-4 h-4 text-[var(--accent)]" /> SOC 2 Type II
                            </span>
                            <span class="flex items-center gap-2 text-[var(--text-muted)]">
                                <x-icon name="check" class="w-4 h-4 text-[var(--accent)]" /> 256-bit encryption
                            </span>
                        </div>
                    </div>
                    <div class="order-1 lg:order-2">
                        <div class="bg-[var(--accent-subtle)] rounded-xl p-2">
                            <img src="https://images.unsplash.com/photo-1451187580459-43a56822d3ee?fm=jpg&q=80&w=800&auto=format&fit=crop" alt="" class="w-full h-auto rounded-lg" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Security section --}}
    <section id="fp-security" class="py-20 bg-[var(--primary-dark)] text-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold tracking-tight mb-4">Security and compliance built in</h2>
                    <p class="text-lg text-blue-100 mb-8">Bastion Peak is SOC 2 Type II certified and built with security at every layer — from 256-bit encryption to role-based access controls.</p>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="flex items-start gap-3">
                            <x-icon name="lock" class="w-5 h-5 text-blue-300 mt-0.5 flex-shrink-0" />
                            <div>
                                <p class="font-semibold mb-1">256-bit encryption</p>
                                <p class="text-sm text-blue-200">All data encrypted at rest and in transit</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-icon name="shield" class="w-5 h-5 text-blue-300 mt-0.5 flex-shrink-0" />
                            <div>
                                <p class="font-semibold mb-1">SOC 2 Type II</p>
                                <p class="text-sm text-blue-200">Third-party verified compliance</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-icon name="users" class="w-5 h-5 text-blue-300 mt-0.5 flex-shrink-0" />
                            <div>
                                <p class="font-semibold mb-1">Role-based access</p>
                                <p class="text-sm text-blue-200">Granular permissions per team</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-icon name="clipboard-check" class="w-5 h-5 text-blue-300 mt-0.5 flex-shrink-0" />
                            <div>
                                <p class="font-semibold mb-1">Audit trail</p>
                                <p class="text-sm text-blue-200">Every action logged and traceable</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:justify-end">
                    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1454165804600-be344094a608?fm=jpg&q=80&w=800&auto=format&fit=crop" alt="Secure banking dashboard" class="w-full h-auto">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section id="fp-testimonials" class="py-20 border-t border-[var(--line)]">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-2xl lg:text-3xl font-bold text-[var(--text)] tracking-tight">What our customers say</h2>
                <p class="mt-3 text-sm text-[var(--text-muted)]">Hear from finance teams that have transformed their banking operations.</p>
            </div>

            <div class="relative">
                <div id="review-track" class="mb-8">
                    <div class="bg-[var(--line-soft)] rounded-2xl p-10">
                        <div class="flex items-center gap-1 mb-4 text-[var(--warning)]">
                            <x-icon name="star" class="w-4 h-4 fill-current" />
                            <x-icon name="star" class="w-4 h-4 fill-current" />
                            <x-icon name="star" class="w-4 h-4 fill-current" />
                            <x-icon name="star" class="w-4 h-4 fill-current" />
                            <x-icon name="star" class="w-4 h-4 fill-current" />
                        </div>
                        <p class="text-xl text-[var(--text)] leading-relaxed mb-6">
                            "Bastion Peak replaced five different tools with a single platform. The approval flows alone saved us 12 hours a week in manual reconciliation."
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] font-bold">MJ</div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-[var(--text)]">Maria Johnson</p>
                                <p class="text-xs text-[var(--text-muted)]">Finance Lead, Apex Labs</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center gap-2" id="review-nav">
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

    {{-- CTA with image background --}}
    <section id="fp-cta" class="relative py-24 lg:py-32">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1496170804224-8f49c8ef5e8c?fm=jpg&q=80&w=1800&auto=format&fit=crop" alt="Finance team collaboration" class="w-full h-full object-cover" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-black/30"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-white tracking-tight mb-4">Ready to modernize your banking infrastructure?</h2>
            <p class="mt-4 text-lg text-blue-100">Join finance teams that trust Bastion Peak for secure, compliant financial operations.</p>
            <div class="mt-10">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-semibold text-[var(--primary-dark)] bg-white rounded-md shadow-lg hover:shadow-xl transition-all">
                    Get started
                    <x-icon name="arrow-right" class="ml-2 w-4 h-4" />
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-white border-t border-[var(--line)] pt-16">
        <div class="max-w-7xl mx-auto px-6 pb-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-12 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2.5 mb-4">
                        <x-bp-logo :width="24" :height="24" />
                        <span class="text-lg font-bold text-[var(--primary-dark)]">Bastion Peak</span>
                    </div>
                    <p class="text-sm text-[var(--text-muted)] max-w-xs">Secure banking infrastructure for modern teams.</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-4">Product</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#fp-features" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Features</a></li>
                        <li><a href="#fp-security" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Security</a></li>
                        <li><a href="#fp-testimonials" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Customers</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-4">Company</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('support') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Support</a></li>
                        <li><a href="{{ route('learn') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Learn</a></li>
                        <li><a href="{{ route('policies') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Policies</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-4">Legal</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('terms') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Terms of Service</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-[var(--line)] pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-[var(--text-muted)]">&copy; 1998–2026 Bastion Peak. All rights reserved.</p>
                <p class="text-xs text-[var(--text-muted)]">Banking infrastructure for modern teams.</p>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var reviews = [
            { avatar: 'MJ', name: 'Maria Johnson', title: 'Finance Lead, Apex Labs', rating: 5,
              text: '"Bastion Peak replaced five different tools with a single platform. The approval flows alone saved us 12 hours a week in manual reconciliation."' },
            { avatar: 'TR', name: 'Thomas Rivera', title: 'Treasury Manager, Nexus Corp', rating: 5,
              text: '"The real-time audit trail gives us confidence in every transaction. We can see exactly who did what and when — no more chasing down emails."' },
            { avatar: 'SL', name: 'Sarah Lin', title: 'CTO, Vertex Bank', rating: 5,
              text: '"SOC 2 compliance was non-negotiable for us. Bastion Peak handled it out of the box — we were live in two weeks, not two quarters."' },
            { avatar: 'DK', name: 'David Kim', title: 'Operations Director, Summit Group', rating: 5,
              text: '"The double-entry ledger is rock-solid. After three years on Bastion Peak, we have zero balance mismatches. That\'s worth its weight in gold."' },
            { avatar: 'AE', name: 'Amira El-Sayed', title: 'Head of Treasury, Meridian Capital', rating: 5,
              text: '"Virtual cards with spending limits have eliminated fraud losses. The API-first approach made integration seamless with our existing stack."' }
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
                '<div class="bg-[var(--line-soft)] rounded-2xl p-10">' +
                '<div class="flex items-center gap-1 mb-4 text-[var(--warning)]">' + stars + '</div>' +
                '<p class="text-xl text-[var(--text)] leading-relaxed mb-6">' + r.text + '</p>' +
                '<div class="flex items-center gap-3">' +
                '<div class="w-12 h-12 rounded-full bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] font-bold">' + r.avatar + '</div>' +
                '<div class="text-left"><p class="text-sm font-semibold text-[var(--text)]">' + r.name + '</p>' +
                '<p class="text-xs text-[var(--text-muted)]">' + r.title + '</p></div></div></div>';
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
</body>
</html>
