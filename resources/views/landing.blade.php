<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bastion Peak — Secure Banking Infrastructure</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="description" content="Bastion Peak is a modern banking platform for secure account management, transfers, and financial operations.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('fonts/schibsted.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/ibm-plex.css') }}">
    <style>
        :root {
            --font-display: "Schibsted Grotesk", system-ui, sans-serif;
            --font-body: "IBM Plex Sans", system-ui, sans-serif;
            --text-scale-h1: 3.5rem;
            --text-scale-h2: 2.5rem;
            --text-scale-h3: 1.5rem;
            --text-scale-body: 1.125rem;
            --text-scale-small: 0.875rem;
        }
        @media (min-width: 768px) {
            :root {
                --text-scale-h1: 4.5rem;
                --text-scale-h2: 3rem;
                --text-scale-h3: 1.75rem;
                --text-scale-body: 1.25rem;
            }
        }

        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--text);
        }

        .font-display {
            font-family: var(--font-display);
        }

        .bank-card {
            border-radius: 1.25rem;
            padding: 2rem;
            color: var(--text-inverse);
            position: relative;
            overflow: hidden;
        }

        .bank-card::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -20%;
            width: 20rem;
            height: 20rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .bank-card::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 14rem;
            height: 14rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .hero-image {
            background: url('https://images.unsplash.com/photo-1551288326-c0a93f84f3c1?fm=jpg&q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }

        .cta-image {
            background: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?fm=jpg&q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }

        .focus-ring {
            outline: 2px solid var(--focus);
            outline-offset: 2px;
        }
        .focus-ring:focus-visible {
            outline: 2px solid var(--focus);
            outline-offset: 2px;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
    <body class="bg-[var(--bg)] text-[var(--text)]">
        <nav class="fixed top-0 left-0 right-0 z-50 bg-[var(--surface)]/90 backdrop-blur-md border-b border-[var(--line)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5 focus-ring">
                <x-bp-logo :width="28" :height="28" />
                <span class="font-semibold text-[var(--primary)] tracking-tight">Bastion Peak</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('signin') }}" class="px-4 py-2 text-sm font-medium text-[var(--text)] hover:text-[var(--primary)] transition-colors focus-ring">Log in</a>
                <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-semibold text-white bg-[var(--primary)] hover:bg-[var(--primary-dark)] rounded-xl transition-colors shadow-sm focus-ring">Get started</a>
            </div>
        </div>
    </nav>

    <main>
        <section class="hero-image pt-20 pb-20 lg:pt-32 lg:pb-40">
            <div class="max-w-7xl mx-auto px-6">
                <div class="lg:grid lg:grid-cols-2 lg:gap-20 items-center">
                    <div class="mb-16 lg:mb-0">
                        <h1 class="font-display font-bold text-[var(--text)] tracking-tight mb-6">
                            <span class="block text-4xl md:text-5xl lg:text-6xl leading-[1.1]">
                                Money moves safely
                            </span>
                            <span class="block text-4xl md:text-5xl lg:text-6xl text-[var(--primary)] leading-[1.1]">
                                at Bastion Peak
                            </span>
                        </h1>
                        <p class="text-lg md:text-xl text-[var(--text-muted)] leading-relaxed mb-10 max-w-xl">
                            A banking platform for teams that can't afford downtime, disputes, or drift. Built-in compliance, real-time visibility, and approval flows that keep every transaction verifiable.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3.5 text-base font-semibold text-white bg-[var(--primary)] hover:bg-[var(--primary-dark)] rounded-xl transition-colors shadow-lg focus-ring">
                                Get started
                                <x-icon name="arrow-right" class="ml-2 w-5 h-5" />
                            </a>
                            <a href="#capabilities" class="inline-flex items-center justify-center px-6 py-3.5 text-base font-semibold text-white bg-[var(--primary)]/15 hover:bg-[var(--primary)]/25 border border-[var(--primary)]/30 rounded-xl transition-colors focus-ring">
                                How it works
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="bank-card max-w-md mx-auto lg:ml-auto shadow-2xl">
                            <div class="flex items-center justify-between mb-8">
                                <x-icon name="landmark" class="w-8 h-8 text-white" />
                                <span class="text-xs font-medium text-white/60">Business</span>
                            </div>

                            <div class="mb-8">
                                <p class="text-xs font-medium text-white/60 mb-1">Total balance</p>
                                <p class="text-4xl font-bold text-white tabular-nums">
                                    $842,291.47
                                </p>
                            </div>

                            <div class="flex items-end justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-white/60 mb-1">Primary checking</p>
                                    <p class="text-lg font-medium text-white tabular-nums">
                                        842,291.47
                                    </p>
                                    <p class="text-xs text-white/50 font-mono">
                                        CHASING 007 8412 6000
                                    </p>
                                </div>
                                <div class="flex gap-2 ml-4">
                                    <div class="w-8 h-6 bg-white/15 rounded"></div>
                                    <div class="w-8 h-6 bg-white/15 rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 border-t border-[var(--line)]">
            <div class="max-w-7xl mx-auto px-6">
                <p class="text-center text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-12">
                    Trusted by finance teams at scale
                </p>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center mb-12">
                    <div>
                        <p class="text-3xl lg:text-4xl font-bold text-[var(--primary)] tabular-nums">99.9%</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Uptime SLA</p>
                    </div>
                    <div>
                        <p class="text-3xl lg:text-4xl font-bold text-[var(--primary)]">256-bit</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Encryption</p>
                    </div>
                    <div>
                        <p class="text-3xl lg:text-4xl font-bold text-[var(--primary)]">&lt;2s</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Transfer speed</p>
                    </div>
                    <div>
                        <p class="text-3xl lg:text-4xl font-bold text-[var(--primary)]">SOC 2</p>
                        <p class="text-sm text-[var(--text-muted)] mt-1">Certified</p>
                    </div>
                </div>

                <p class="text-center text-xs text-[var(--text-muted)]">
                    Built for teams that process approvals all day — not dashboards that look good in a pitch deck.
                </p>
            </div>
        </section>

        <section id="capabilities" class="py-20 lg:py-28">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center mb-20">
                    <h2 class="font-display text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight mb-4">
                        Everything needed for secure money movement
                    </h2>
                    <p class="text-lg text-[var(--text-muted)] max-w-2xl mx-auto">
                        From deposits and transfers to cards and compliance reporting — each feature is built around the moment an operator needs to review, approve, or audit a transaction.
                    </p>
                </div>

                <div class="space-y-16">
                    <div class="lg:flex lg:items-start lg:gap-16">
                        <div class="lg:w-1/3 mb-8 lg:mb-0">
                            <h3 class="font-display text-xl font-semibold text-[var(--text)] mb-2">
                                Approval-first transfers
                            </h3>
                            <p class="text-sm text-[var(--text-muted)]">
                                Every payment passes through a real review flow before it posts to the ledger.
                            </p>
                        </div>
                        <div class="lg:w-2/3">
                            <p class="text-[var(--text-muted)] leading-relaxed">
                                Wire instructions, merchant payouts, and inter-account moves are all handled through configurable approval chains. Each request captures proof-of-instruction, method details, and the requester's context. Operators see a unified queue with age-based prioritization, limit-check results, and balance impact — all before they commit.
                            </p>
                        </div>
                    </div>

                    <div class="lg:flex lg:items-start lg:gap-16 lg:flex-row-reverse">
                        <div class="lg:w-1/3 mb-8 lg:mb-0">
                            <h3 class="font-display text-xl font-semibold text-[var(--text)] mb-2">
                                Deposit processing
                            </h3>
                            <p class="text-sm text-[var(--text-muted)]">
                                Funds enter through methods tied to real account credentials.
                            </p>
                        </div>
                        <div class="lg:w-2/3">
                            <p class="text-[var(--text-muted)] leading-relaxed">
                                Customers submit proof-of-payment alongside their deposit request. The system stores the reference image, matches it to the receiving method, and presents the full context in the approval view. Balances update only after verification posts to the double-entry ledger.
                            </p>
                        </div>
                    </div>

                    <div class="lg:flex lg:items-start lg:gap-16">
                        <div class="lg:w-1/3 mb-8 lg:mb-0">
                            <h3 class="font-display text-xl font-semibold text-[var(--text)] mb-2">
                                Virtual cards
                            </h3>
                            <p class="text-sm text-[var(--text-muted)]">
                                Single-use and merchant-restricted cards issued in seconds.
                            </p>
                        </div>
                        <div class="lg:w-2/3">
                            <p class="text-[var(--text-muted)] leading-relaxed">
                                Issue virtual cards with per-card spend limits and automatic expiry. Every transaction on a virtual card generates an immediate audit trail. Freeze, replace, or revoke cards with a single action — changes apply before the next authorization attempt.
                            </p>
                        </div>
                    </div>

                    <div class="lg:flex lg:items-start lg:gap-16 lg:flex-row-reverse">
                        <div class="lg:w-1/3 mb-8 lg:mb-0">
                            <h3 class="font-display text-xl font-semibold text-[var(--text)] mb-2">
                                Ledger &amp; reporting
                            </h3>
                            <p class="text-sm text-[var(--text-muted)]">
                                Every credit has a matching debit. Always.
                            </p>
                        </div>
                        <div class="lg:w-2/3">
                            <p class="text-[var(--text-muted)] leading-relaxed">
                                A strict double-entry ledger recalculates account balances automatically. Any imbalance is caught at commit time — never silently written off. Generate statements, export audit logs, and trace any transaction back to the action that started it.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-[var(--surface-raised)] border-t border-[var(--line)]">
            <div class="max-w-3xl mx-auto px-6 text-center">
                <h2 class="font-display text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight mb-4">
                    A customer recently told us
                </h2>
                <p class="text-lg text-[var(--text-muted)] max-w-2xl mx-auto mb-8">
                    We were using Bastion Peak for a year before we realized how much trust we'd stopped giving to spreadsheets, email threads, and shared password managers. That's the best review we could give.
                </p>
                <div class="flex items-center justify-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] font-bold">
                        AE
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-[var(--text)]">Amira El-Sayed</p>
                        <p class="text-xs text-[var(--text-muted)]">Head of Treasury, Meridian Capital</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-[1fr_400px] gap-16 items-center">
                    <div>
                        <h2 class="font-display text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight mb-6">
                            Banking infrastructure for modern teams
                        </h2>
                        <p class="text-lg text-[var(--text-muted)] leading-relaxed mb-6 max-w-xl">
                            Most banking interfaces prioritize optics over operations. Bastion Peak is built for the moments that matter: a pending approval, a disputed receipt, a balance that needs to tie out before EOD.
                        </p>

                        <ul class="space-y-4 text-sm text-[var(--text-muted)]">
                            <li class="flex items-start gap-3">
                                <x-icon name="check" class="w-5 h-5 text-[var(--accent)] mt-0.5 flex-shrink-0" />
                                <span>Double-entry ledger with automatic balance recalculation</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <x-icon name="check" class="w-5 h-5 text-[var(--accent)] mt-0.5 flex-shrink-0" />
                                <span>Role-based access for customers and operators</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <x-icon name="check" class="w-5 h-5 text-[var(--accent)] mt-0.5 flex-shrink-0" />
                                <span>Complete audit trail for every transaction and change</span>
                            </li>
                        </ul>
                    </div>

                    <div class="hidden lg:block">
                        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden shadow-lg">
                            <div class="relative aspect-[4/3]">
                                <img src="https://images.unsplash.com/photo-1554469384-e58fac16e23a?fm=jpg&q=80&w=800&auto=format&fit=crop" alt="Operations dashboard overview" class="w-full h-full object-cover" loading="lazy">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-image py-20 lg:py-32">
            <div class="max-w-3xl mx-auto px-6 text-center">
                <h2 class="font-display text-3xl lg:text-4xl font-bold text-white tracking-tight mb-4">
                    Ready to move to modern banking infrastructure?
                </h2>
                <p class="text-lg text-white/80 mb-10 max-w-xl mx-auto">
                    Join teams that trust Bastion Peak for secure financial operations.
                </p>
                <div class="flex justify-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-white bg-[var(--primary)] hover:bg-[var(--primary-dark)] rounded-xl transition-colors shadow-lg focus-ring">
                        Get started
                        <x-icon name="arrow-right" class="ml-2 w-5 h-5" />
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-[var(--line)] py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <x-bp-logo :width="24" :height="24" />
                        <span class="font-semibold text-[var(--primary)] tracking-tight">Bastion Peak</span>
                    </div>
                    <p class="text-xs text-[var(--text-muted)] leading-relaxed">
                        Banking infrastructure for modern teams.
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-4">Product</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('recommendations') }}" class="text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">Recommendations</a></li>
                        <li><a href="{{ route('learn') }}" class="text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">Learn</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-4">Company</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('support') }}" class="text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">Support</a></li>
                        <li><a href="{{ route('policies') }}" class="text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">Policies</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-4">Legal</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('terms') }}" class="text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">Terms of Service</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-[var(--line)] flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-[var(--text-muted)]">
                    &copy; 1998–2026 Bastion Peak. All rights reserved.
                </p>
                <a href="{{ route('support') }}" class="text-xs text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">
                    Contact us
                </a>
            </div>
        </div>
    </footer>
</body>
</html>
