<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bastion Peak</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="description" content="Bastion Peak is banking infrastructure for finance and operations teams. Approval workflows, double-entry ledgers, and audit trails built for people who need control.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        body { background: var(--bg); }
    </style>
</head>
<body class="text-[var(--text)]">

    <header class="border-b border-[var(--line)]">
        <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                <x-bp-logo :width="28" :height="28" />
                <span class="font-bold text-[var(--primary-dark)]">Bastion Peak</span>
            </a>
            <div class="flex items-center gap-8 text-sm">
                <a href="#lp-security" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Security</a>
                <a href="#lp-cta" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">How it works</a>
                <a href="{{ route('signin') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)] transition-colors">Log in</a>
                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-[var(--primary-dark)] hover:bg-[var(--primary)] rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--primary-dark)]/30">Get started</a>
            </div>
        </div>
    </header>

    <main>
        <section class="pt-20 pb-16 md:pt-28 md:pb-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                    <div>
                        <h1 class="text-4xl md:text-5xl font-bold text-[var(--text)] leading-tight mb-6">
                            Banking infrastructure that moves money with full audit
                        </h1>
                        <p class="text-lg text-[var(--text-muted)] leading-relaxed mb-8 max-w-lg">
                            Approval workflows, double-entry ledgers, and audit trails — for finance and operations teams who need every transaction traceable and every action accountable.
                        </p>
                        <div class="flex gap-4">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-[var(--primary-dark)] hover:bg-[var(--primary)] rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--primary-dark)]/30">Get started</a>
                            <a href="#lp-how-it-works" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-[var(--text-muted)] hover:text-[var(--primary-dark)] border border-[var(--line)] rounded-md transition-colors">See how it works</a>
                        </div>
                    </div>
                    <div class="bg-white border border-[var(--line)] rounded-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-[var(--line-soft)]">
                                    <tr>
                                        <th class="px-4 py-2 text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">Time</th>
                                        <th class="px-4 py-2 text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">Description</th>
                                        <th class="px-4 py-2 text-xs font-medium text-[var(--text-muted)] text-right uppercase tracking-wider">Debit</th>
                                        <th class="px-4 py-2 text-xs font-medium text-[var(--text-muted)] text-right uppercase tracking-wider">Credit</th>
                                        <th class="px-4 py-2 text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr class="border-t border-[var(--line)]">
                                        <td class="px-4 py-3 text-[var(--text-muted)] font-mono">09:14</td>
                                        <td class="px-4 py-3 text-[var(--text)]">Payment to Acmecorp Ltd</td>
                                        <td class="px-4 py-3 text-right font-mono">—</td>
                                        <td class="px-4 py-3 text-right font-mono">7,500.00</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                        </td>
                                    </tr>
                                    <tr class="border-t border-[var(--line)]">
                                        <td class="px-4 py-3 text-[var(--text-muted)] font-mono">09:15</td>
                                        <td class="px-4 py-3 text-[var(--text)]">Approval — Sarah Chen</td>
                                        <td class="px-4 py-3 text-right font-mono">—</td>
                                        <td class="px-4 py-3 text-right font-mono">—</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Approved</span>
                                        </td>
                                    </tr>
                                    <tr class="border-t border-[var(--line)]">
                                        <td class="px-4 py-3 text-[var(--text-muted)] font-mono">09:15</td>
                                        <td class="px-4 py-3 text-[var(--text)]">Payment to Acmecorp Ltd</td>
                                        <td class="px-4 py-3 text-right font-mono">7,500.00</td>
                                        <td class="px-4 py-3 text-right font-mono">7,500.00</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Posted</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-12 border-t border-[var(--line)]">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                    <div>
                        <p class="text-sm text-[var(--text-muted)]">Uptime SLA</p>
                        <p class="text-2xl font-bold text-[var(--primary-dark)]">99.9%</p>
                    </div>
                    <div>
                        <p class="text-sm text-[var(--text-muted)]">Encryption</p>
                        <p class="text-2xl font-bold text-[var(--primary-dark)]">256-bit</p>
                    </div>
                    <div>
                        <p class="text-sm text-[var(--text-muted)]">Transfer speed</p>
                        <p class="text-2xl font-bold text-[var(--primary-dark)]">&lt;2s</p>
                    </div>
                    <div>
                        <p class="text-sm text-[var(--text-muted)]">Compliance</p>
                        <p class="text-2xl font-bold text-[var(--primary-dark)]">SOC 2 Type II</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="lp-how-it-works" class="py-20 lg:py-28">
            <div class="max-w-7xl mx-auto px-6">
                <div class="space-y-20">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                        <div>
                            <h2 class="text-3xl font-bold text-[var(--text)] leading-tight mb-4">Approve transfers before they post</h2>
                            <p class="text-lg text-[var(--text-muted)] leading-relaxed mb-4">
                                Define who can approve what, at what limit, and when. Every approval is logged with a timestamp and actor attribution. Transfers stay pending until every rule in the chain is satisfied.
                            </p>
                            <p class="text-sm text-[var(--text-muted)]">
                                Rules, not reminders.
                            </p>
                        </div>
                        <div class="bg-white border border-[var(--line)] rounded-md overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-[var(--line-soft)]">
                                        <tr>
                                            <th class="px-4 py-2 text-xs font-medium text-[var(--text-muted)] uppercase">Reference</th>
                                            <th class="px-4 py-2 text-xs font-medium text-[var(--text-muted)] uppercase">Amount</th>
                                            <th class="px-4 py-2 text-xs font-medium text-[var(--text-muted)] uppercase">Approver</th>
                                            <th class="px-4 py-2 text-xs font-medium text-[var(--text-muted)] uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm">
                                        <tr class="border-t border-[var(--line)]">
                                            <td class="px-4 py-3 font-mono text-[var(--text-muted)]">TXN-00231</td>
                                            <td class="px-4 py-3 font-mono">$12,500.00</td>
                                            <td class="px-4 py-3 text-[var(--text-muted)">Sarah Chen</td>
                                            <td class="px-4 py-3"><span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span></td>
                                        </tr>
                                        <tr class="border-t border-[var(--line)]">
                                            <td class="px-4 py-3 font-mono text-[var(--text-muted)]">TXN-00230</td>
                                            <td class="px-4 py-3 font-mono">$8,000.00</td>
                                            <td class="px-4 py-3 text-[var(--text-muted)]">Sarah Chen, Mark Liu</td>
                                            <td class="px-4 py-3"><span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Posted</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                        <div>
                            <h2 class="text-3xl font-bold text-[var(--text)] leading-tight mb-4">Issue and control virtual cards</h2>
                            <p class="text-lg text-[var(--text-muted)] leading-relaxed mb-4">
                                Generate virtual cards instantly, set spending limits per card or per category, and freeze or replace cards in one click. Every card carries its own audit trail.
                            </p>
                            <p class="text-sm text-[var(--text-muted)]">
                                Control, not just cards.
                            </p>
                        </div>
                        <div class="bg-white border border-[var(--line)] rounded-md p-6">
                            <div class="space-y-4 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-[var(--text-muted)]">VC-4729</span>
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[var(--text-muted)]">Limit</span>
                                    <span class="font-mono text-[var(--text)]">$5,000.00 / 30 days</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[var(--text-muted)]">Last charge</span>
                                    <span class="font-mono text-[var(--text-muted)]">Today, 14:22</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="lp-testimonial" class="py-20 bg-[var(--line-soft)]">
            <div class="max-w-3xl mx-auto px-6 text-center">
                <blockquote class="text-2xl md:text-3xl font-light text-[var(--text)] leading-tight mb-8">
                    Bastion Peak replaced five different tools with a single platform. The approval flows alone saved us 12 hours a week in manual reconciliation.
                </blockquote>
                <p class="text-sm text-[var(--text-muted)]">
                    Maria Johnson — Finance Lead, Apex Labs
                </p>
            </div>
        </section>

        <section id="lp-cta" class="py-20 bg-[var(--primary-dark)] text-white text-center">
            <div class="max-w-3xl mx-auto px-6">
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-4">
                    Banking infrastructure for modern teams
                </h2>
                <p class="mt-4 text-lg text-blue-100">
                    Sign up to open an account in under 2 minutes.
                </p>
                <div class="mt-8">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-[var(--primary-dark)] bg-white rounded-md hover:bg-blue-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[var(--primary-dark)] focus:ring-white">
                        Get started
                    </a>
                </div>
            </div>
        </section>

    </main>

    <footer class="border-t border-[var(--line)] pt-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-10 mb-10">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2.5 mb-3">
                        <x-bp-logo :width="24" :height="24" />
                        <span class="font-bold text-[var(--primary-dark)]">Bastion Peak</span>
                    </div>
                    <p class="text-sm text-[var(--text-muted)] max-w-xs">Secure banking infrastructure for finance and operations teams.</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-3">Product</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#lp-how-it-works" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">How it works</a></li>
                        <li><a href="#lp-testimonial" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Customers</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-3">Company</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('support') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Support</a></li>
                        <li><a href="{{ route('learn') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Learn</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-3">Resources</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('policies') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Policies</a></li>
                        <li><a href="{{ route('recommendations') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Recommendations</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-3">Legal</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('terms') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Terms</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-[var(--text-muted)] hover:text-[var(--primary-dark)]">Privacy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-[var(--line)] pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-[var(--text-muted)]">Bastion Peak is a registered trademark. &copy; 2026 Bastion Peak.</p>
                <p class="text-xs text-[var(--text-muted)]">Banking infrastructure for finance and operations teams.</p>
            </div>
        </div>
    </footer>

</body>
</html>
