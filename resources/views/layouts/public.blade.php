<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Bastion Peak') — Bastion Peak</title>
    <link rel="icon" href="{{ asset('Bastion Peaks.png') }}" type="image/png">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="description" content="@yield('meta_description', 'Bastion Peak is a modern banking platform for secure account management, transfers, and financial operations.')">
    @vite(['resources/css/app.css', 'resources/css/utilities.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('fonts/schibsted.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/ibm-plex.css') }}">
    <style>
        :root {
            --font-display: "Schibsted Grotesk", system-ui, sans-serif;
            --font-body: "IBM Plex Sans", system-ui, sans-serif;
        }
        body { font-family: var(--font-body); }
        .font-display { font-family: var(--font-display); }
    </style>

        </head>
<body class="bg-[var(--bg)] text-[var(--text)]">
<script>
        (function() {
            var dbTheme = localStorage.getItem("theme") || "dark";
            var prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
            if (dbTheme === "dark" || (dbTheme === "system" && prefersDark)) {
                document.body.classList.add("dark");
                document.body.setAttribute("data-theme", "dark");
            } else if (dbTheme === "light") {
                document.body.classList.remove("dark");
                document.body.setAttribute("data-theme", "light");
            } else {
                document.body.classList.add("dark");
                document.body.setAttribute("data-theme", "dark");
            }
        })();
    </script>
    <nav class="sticky top-0 z-50 bg-[var(--surface)]/90 backdrop-blur-md border-b border-[var(--line)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                <x-bp-logo :width="28" :height="28" />
                <span class="font-semibold text-[var(--primary)] tracking-tight">Bastion Peak</span>
            </a>
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('support') }}" class="text-sm font-medium text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">Support</a>
                <a href="{{ route('learn') }}" class="text-sm font-medium text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">Learn</a>
                <a href="{{ route('recommendations') }}" class="text-sm font-medium text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">Recommendations</a>
                <a href="{{ route('policies') }}" class="text-sm font-medium text-[var(--text-muted)] hover:text-[var(--primary)] transition-colors">Policies</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('signin') }}" class="px-4 py-2 text-sm font-medium text-[var(--text)] hover:text-[var(--primary)] transition-colors">Log in</a>
                <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-semibold text-white bg-[var(--primary)] hover:bg-[var(--primary-dark)] rounded-xl transition-colors shadow-sm">Get started</a>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-12 lg:py-16">
        @yield('content')
    </main>

    <footer class="bg-[var(--surface)] border-t border-[var(--line)] py-12 mt-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <x-bp-logo :width="24" :height="24" />
                        <span class="text-sm font-semibold text-[var(--text)]">Bastion Peak</span>
                    </div>
                    <p class="text-xs text-[var(--text-muted)] leading-relaxed">Secure. Compliant. Modern.</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-3">Product</p>
                    <ul class="space-y-2 text-sm text-[var(--text-muted)]">
                        <li><a href="{{ route('recommendations') }}" class="hover:text-[var(--primary)]">Recommendations</a></li>
                        <li><a href="{{ route('learn') }}" class="hover:text-[var(--primary)]">Learn</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-3">Company</p>
                    <ul class="space-y-2 text-sm text-[var(--text-muted)]">
                        <li><a href="{{ route('support') }}" class="hover:text-[var(--primary)]">Support</a></li>
                        <li><a href="{{ route('policies') }}" class="hover:text-[var(--primary)]">Policies</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--text)] uppercase tracking-wider mb-3">Legal</p>
                    <ul class="space-y-2 text-sm text-[var(--text-muted)]">
                        <li><a href="{{ route('terms') }}" class="hover:text-[var(--primary)]">Terms of Service</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-[var(--primary)]">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-10 pt-6 border-t border-[var(--line)] flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-[var(--text-muted)]">&copy; 1998–2026 Bastion Peak. All rights reserved.</p>
                <p class="text-xs text-[var(--text-muted)]">Banking infrastructure for modern teams.</p>
            </div>
        </div>
    </footer>
</body>
</html>
