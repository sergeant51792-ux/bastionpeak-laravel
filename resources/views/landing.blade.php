<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bastion Peak — Secure Banking Infrastructure</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="description" content="Bastion Peak is a banking platform for finance teams that need approval workflows, double-entry ledgers, and SOC 2 compliance.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        .hero-img {
            background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?fm=jpg&q=80&w=2000&auto=format&fit=crop');
        }
        .cta-img {
            background-image: url('https://images.unsplash.com/photo-1496170804224-8f49c558efc0?fm=jpg&q=80&w=2000&auto=format&fit=crop');
        }
    </style>
</head>
<body class="bg-white text-[var(--text)]">

    <nav class="border-b border-[var(--line)]">
        <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                <x-bp-logo :width="28" :height="28" />
                <span class="font-bold text-[var(--primary)]">Bastion Peak</span>
            </a>
            <div class="flex items-center gap-8 text-sm">
                <a href="#fp-security" class="text-[var(--text-muted)] hover:text-[var(--text)] transition-colors">Security</a>
                <a href="#fp-testimonials" class="text-[var(--text-muted)] hover:text-[var(--text)] transition-colors">Customers</a>
                <a href="{{ route('signin') }}" class="text-[var(--text-muted)] hover:text-[var(--text)] transition-colors">Log in</a>
                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-[var(--primary)] hover:bg-[var(--primary-dark)] rounded-md transition-colors">Sign up</a>
            </div>
        </div>
    </nav>

    <main>

        <section class="hero-img bg-cover bg-center bg-no-repeat min-h-screen flex items-center">
            <div class="max-w-4xl px-6 py-24 md:py-32 bg-black/55">
                <h1 class="text-4xl md:text-5xl font-bold text-white leading-[1.08] mb-6">
                    Banking infrastructure for modern finance teams
                </h1>
                <p class="text-lg text-blue-100 leading-relaxed max-w-xl mb-8">
                    Approval workflows, double-entry ledgers, and SOC 2 compliance — all in one platform built for finance teams that can't afford mistakes.
                </p>
                <div class="flex gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-[var(--primary-dark)] bg-white rounded-md">
                        Sign up
                        <x-icon name="arrow-right" class="ml-2 w-4 h-4" />
                    </a>
                </div>
                <div class="mt-10 flex gap-8 text-sm text-blue-200">
                    <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-blue-300 rounded-full"></span> SOC 2 Type II certified</span>
                    <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-blue-300 rounded-full"></span> 256-bit encryption</span>
                </div>
            </div>
        </section>

        <section class="py-16 border-t border-[var(--line)]">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                    <div>
                        <p class="text-3xl font-bold text-[var(--primary-dark)]">99.9%</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Uptime SLA</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-[var(--primary-dark)]">256-bit</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Encryption</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-[var(--primary-dark)]">&lt;2s</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Transfer speed</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-[var(--primary-dark)]">SOC 2</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Certified</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 lg:py-32">
            <div class="max-w-7xl mx-auto px-6 space-y-24">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-[var(--text)] leading-tight mb-4">Double-entry ledger</h2>
                        <p class="text-lg text-[var(--text-muted)] leading-relaxed mb-4">
                            Every debit creates a matching credit. Balances are recalculated after every transaction, and the audit trail is immutable.
                        </p>
                        <p class="text-sm text-[var(--text-muted)]">
                            No reconciliation. No guesswork.
                        </p>
                    </div>
                    <div class="lg:justify-self-end">
                        <div class="bg-[var(--line-soft)] rounded-md overflow-hidden w-full max-w-sm">
                            <img src="https://images.unsplash.com/photo-1551285788-a3146e8efc9f?fm=jpg&q=80&w=600&auto=format&fit=crop" alt="" class="w-full h-auto">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="lg:order-2">
                        <h2 class="text-3xl font-bold text-[var(--text)] leading-tight mb-4">Approval workflows</h2>
                        <p class="text-lg text-[var(--text-muted)] leading-relaxed mb-4">
                            Define who can approve what, at what limit, and when. Every decision is logged with a timestamp and actor attribution.
                        </p>
                        <p class="text-sm text-[var(--text-muted)]">
                            Rules, not reminders.
                        </p>
                    </div>
                    <div class="lg:order-1">
                        <div class="bg-[var(--line-soft)] rounded-md overflow-hidden w-full max-w-sm lg:justify-self-start">
                            <img src="https://images.unsplash.com/photo-1552667468-071e6a5c7c5a?fm=jpg&q=80&w=600&auto=format&fit=crop" alt="" class="w-full h-auto">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-[var(--text)] leading-tight mb-4">SOC 2 Type II compliance</h2>
                        <p class="text-lg text-[var(--text-muted)] leading-relaxed mb-4">
                            Third-party audited for security, availability, and confidentiality. 256-bit encryption at rest and in transit.
                        </p>
                        <p class="text-sm text-[var(--text-muted)]">
                            Built in, not bolted on.
                        </p>
                    </div>
                    <div class="lg:justify-self-end">
                        <div class="bg-[var(--line-soft)] rounded-md overflow-hidden w-full max-w-sm">
                            <img src="https://images.unsplash.com/photo-1451187580459-43a56822d3ee?fm=jpg&q=80&w=600&auto=format&fit=crop" alt="" class="w-full h-auto">
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <section id="fp-testimonials" class="py-24 bg-[var(--line-soft)]">
            <div class="max-w-4xl mx-auto px-6">
                <div id="review-track" class="text-center">
                    <blockquote class="text-2xl md:text-3xl font-light text-[var(--text)] leading-tight mb-6 max-w-2xl mx-auto">
                        "Bastion Peak replaced five different tools with a single platform. The approval flows alone saved us 12 hours a week in manual reconciliation."
                    </blockquote>
                    <div class="flex items-center justify-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] font-bold">MJ</div>
                        <div class="text-left">
                            <p class="text-sm font-semibold text-[var(--text)]">Maria Johnson</p>
                            <p class="text-xs text-[var(--text-muted)]">Finance Lead, Apex Labs</p>
                        </div>
                    </div>
                </div>
                <p class="text-center text-xs text-[var(--text-muted)] mt-6" id="review-counter">1 / 5</p>
            </div>
        </section>

        <section id="fp-cta" class="cta-img bg-cover bg-center bg-no-repeat min-h-[500px] flex items-center">
            <div class="max-w-3xl px-6 py-16 bg-black/50">
                <h2 class="text-3xl md:text-4xl font-bold text-white tracking-tight mb-4">
                    Ready to modernize your banking infrastructure?
                </h2>
                <p class="mt-4 text-lg text-blue-100">
                    Join finance teams that trust Bastion Peak for secure, compliant financial operations. Sign up in under 2 minutes.
                </p>
                <div class="mt-8">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-[var(--primary-dark)] bg-white rounded-md">
                        Sign up
                        <x-icon name="arrow-right" class="ml-2 w-4 h-4" />
                    </a>
                </div>
            </div>
        </section>

    </main>

    <footer class="border-t border-[var(--line)] py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-10">
                <div>
                    <div class="flex items-center gap-2.5 mb-3">
                        <x-bp-logo :width="24" :height="24" />
                        <span class="text-sm font-bold text-[var(--primary-dark)]">Bastion Peak</span>
                    </div>
                    <p class="text-xs text-[var(--text-muted)]">Secure banking infrastructure for modern teams.</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-4">Product</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#fp-security" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Security</a></li>
                        <li><a href="#fp-testimonials" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Customers</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-4">Company</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('support') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Support</a></li>
                        <li><a href="{{ route('learn') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Learn</a></li>
                        <li><a href="{{ route('policies') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Policies</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-4">Legal</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('terms') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Terms</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Privacy</a></li>
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
            { avatar: 'MJ', name: 'Maria Johnson', title: 'Finance Lead, Apex Labs',
              text: '"Bastion Peak replaced five different tools with a single platform. The approval flows alone saved us 12 hours a week in manual reconciliation."' },
            { avatar: 'TR', name: 'Thomas Rivera', title: 'Treasury Manager, Nexus Corp',
              text: '"The real-time audit trail gives us confidence in every transaction. We can see exactly who did what and when — no more chasing down emails."' },
            { avatar: 'SL', name: 'Sarah Lin', title: 'CTO, Vertex Bank',
              text: '"SOC 2 compliance was non-negotiable for us. Bastion Peak handled it out of the box — we were live in two weeks, not two quarters."' },
            { avatar: 'DK', name: 'David Kim', title: 'Operations Director, Summit Group',
              text: '"The double-entry ledger is rock-solid. After three years on Bastion Peak, we have zero balance mismatches."' },
            { avatar: 'AE', name: 'Amira El-Sayed', title: 'Head of Treasury, Meridian Capital',
              text: '"Virtual cards with spending limits have eliminated fraud losses. The API-first approach made integration seamless."' }
        ];

        var currentReview = 0;
        var track = document.getElementById('review-track');
        var counter = document.getElementById('review-counter');

        function renderReview(index) {
            var r = reviews[index];
            track.innerHTML =
                '<blockquote class="text-2xl md:text-3xl font-light text-[var(--text)] leading-tight mb-6 max-w-2xl mx-auto">' + r.text + '</blockquote>' +
                '<div class="flex items-center justify-center gap-3">' +
                '<div class="w-10 h-10 rounded-full bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] font-bold">' + r.avatar + '</div>' +
                '<div class="text-left"><p class="text-sm font-semibold text-[var(--text)]">' + r.name + '</p>' +
                '<p class="text-xs text-[var(--text-muted)]">' + r.title + '</p></div></div>';
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

        var nav = document.createElement('div');
        nav.className = 'flex justify-center gap-2 mt-6';
        nav.innerHTML =
            '<button onclick="prevReview()" class="w-9 h-9 rounded bg-white border border-[var(--line)] flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text)] min-h-[44px] min-w-[44px]">' +
            '<x-icon name="chevron-left" class="w-4 h-4" /></button>' +
            '<button onclick="nextReview()" class="w-9 h-9 rounded bg-white border border-[var(--line)] flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text)] min-h-[44px] min-w-[44px]">' +
            '<x-icon name="chevron-right" class="w-4 h-4" /></button>';
        counter.parentNode.insertBefore(nav, counter);

        renderReview(currentReview);
        updateCounter();
    });
    </script>
</body>
</html>
