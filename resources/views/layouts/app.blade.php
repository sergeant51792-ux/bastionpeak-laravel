<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="color-scheme" content="dark light">
    <title>@yield('title', 'Bastion Peak') - Bastion Peak</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/css/utilities.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('fonts/schibsted.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/ibm-plex.css') }}">
    <style>
        :root {
            --font-display: "Schibsted Grotesk", system-ui, sans-serif;
            --font-body: "IBM Plex Sans", system-ui, sans-serif;
        }
        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--text);
        }
        h1, h2, h3, h4, h5, .font-display { font-family: var(--font-display); }
    </style>
</head>
<body class="antialiased min-h-screen pb-24">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-[var(--primary)] focus:text-white focus:rounded-lg">Skip to main content</a>

    <main id="main-content" class="min-h-screen">
        @yield('content')
    </main>

    <nav class="fixed bottom-0 inset-x-0 bg-[var(--surface)]/90 backdrop-blur-md border-t border-[var(--line)] z-40 safe-area-inset-bottom">
        <div class="max-w-lg mx-auto flex items-center justify-around py-2">
            <a href="{{ route('home') }}" class="nav-item flex flex-col items-center gap-0.5 px-3 py-1 text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors {{ request()->routeIs('home') ? 'text-[var(--accent)]' : '' }}">
                <x-icon name="columns" class="h-6 w-6" />
                <span class="text-[10px] font-medium">Transactions</span>
            </a>
            <a href="{{ route('transfer') }}" class="nav-item flex flex-col items-center gap-0.5 px-3 py-1 text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors {{ request()->routeIs('transfer*') ? 'text-[var(--accent)]' : '' }}">
                <x-icon name="shuffle" class="h-6 w-6" />
                <span class="text-[10px] font-medium">Payments</span>
            </a>
            <a href="{{ route('home') }}" class="nav-item flex flex-col items-center gap-0.5 px-3 py-1 text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors {{ request()->routeIs('home') ? 'text-[var(--accent)]' : '' }}">
                <x-icon name="home" class="h-6 w-6" />
                <span class="text-[10px] font-medium">Home</span>
            </a>
            <a href="{{ route('cards') }}" class="nav-item flex flex-col items-center gap-0.5 px-3 py-1 text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors {{ request()->routeIs('cards') ? 'text-[var(--accent)]' : '' }}">
                <x-icon name="credit-card" class="h-6 w-6" />
                <span class="text-[10px] font-medium">Cards</span>
            </a>
            <a href="{{ route('profile') }}" class="nav-item flex flex-col items-center gap-0.5 px-3 py-1 text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors {{ request()->routeIs('profile') ? 'text-[var(--accent)]' : '' }}">
                <x-icon name="user"  class="h-6 w-6" />
                <span class="text-[10px] font-medium">Profile</span>
            </a>
        </div>
    </nav>
</body>
</html>
