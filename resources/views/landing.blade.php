<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bastion Peak — Secure Banking Infrastructure</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="description" content="Bastion Peak is a modern internal banking platform for secure account management, transfers, and financial operations.">
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
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
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
                        <a href="#features" class="inline-flex items-center justify-center px-6 py-3.5 text-sm font-semibold text-white bg-blue-800/40 hover:bg-blue-800/60 border border-blue-400/30 rounded-xl transition-colors backdrop-blur-sm">
                            Learn more
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="py-20 lg:py-28 bg-[var(--bg)]">
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
                        <h3 class="text-lg font-semibold text-[var(--text)] mb-2">Reporting & insights</h3>
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

        <section class="about-bg py-20 lg:py-28">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl lg:text-4xl font-bold text-white tracking-tight leading-tight">Built for operations, not just optics</h2>
                        <p class="mt-5 text-blue-100 leading-relaxed">Most banking dashboards show pretty charts. Bastion Peak is built to move money safely — with approval flows, ledger integrity, and real-time visibility across accounts, cards, and merchants.</p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-start gap-3 text-sm text-blue-50">
                                <x-icon name="check" class="w-5 h-5 text-blue-300 mt-0.5 flex-shrink-0" />
                                <span>Double-entry ledger with automatic balance recalculation</span>
                            </li>
                            <li class="flex items-start gap-3 text-sm text-blue-50">
                                <x-icon name="check" class="w-5 h-5 text-blue-300 mt-0.5 flex-shrink-0" />
                                <span>Role-based access for customers and operators</span>
                            </li>
                            <li class="flex items-start gap-3 text-sm text-blue-50">
                                <x-icon name="check" class="w-5 h-5 text-blue-300 mt-0.5 flex-shrink-0" />
                                <span>Complete audit trail for every transaction and change</span>
                            </li>
                        </ul>
                    </div>
                    <div class="relative hidden lg:block">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1554469384-e58fac16e23a?fm=jpg&q=80&w=1200&auto=format&fit=crop" alt="Modern financial district" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-white border-t border-[var(--line)]">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
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

        <section class="py-20 bg-[var(--bg)]">
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
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <x-bp-logo :width="24" :height="24" />
                <span class="text-sm font-semibold text-[var(--text)]">Bastion Peak</span>
            </div>
            <p class="text-xs text-[var(--text-muted)]">Internal banking platform. Secure. Compliant. Modern.</p>
        </div>
    </footer>
</body>
</html>
