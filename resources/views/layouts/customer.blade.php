<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0F172A">
    <title>@yield('title', 'Home') — Bastion Peak</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        html, body {
            max-width: 100vw;
            overflow-x: hidden;
        }
        @media (max-width: 640px) {
            .safe-top { padding-top: env(safe-area-inset-top, 0px); }
            .safe-bottom { padding-bottom: calc(4rem + env(safe-area-inset-bottom, 0px)); }
        }
    </style>
</head>
<body class="bg-[var(--bg)] text-[var(--text)]">
    <script>
         (function() {
            @auth
            var theme = '{{ auth()->user()->theme_preference ?: "light" }}';
            @else
            var theme = 'light';
            @endauth
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (theme === 'dark' || (theme === 'system' && prefersDark)) {
                document.body.classList.add('dark');
                document.body.setAttribute('data-theme', 'dark');
            } else {
                document.body.classList.remove('dark');
                document.body.setAttribute('data-theme', 'light');
            }

            @if(auth()->check() && auth()->user()->hide_balance)
            document.body.setAttribute('data-hide-balance', 'true');
            @endif
        })();
    </script>
    <div class="flex min-h-screen w-full overflow-x-hidden">
    @auth
        @if(!auth()->user()->hasRole('Super Admin'))
            <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden"></div>
            <aside id="customer-sidebar" class="bg-white dark:bg-[var(--surface)] border-r border-[var(--line)] fixed inset-y-0 left-0 z-50 transition-transform duration-300 ease-in-out w-64 sidebar-closed lg:translate-x-0 lg:static lg:inset-auto lg:z-auto">
                <div class="p-4 border-b border-[var(--line)] flex items-center justify-between">
                    <x-icon name="landmark" class="w-6 h-6 text-[var(--primary)]" />
                    <button id="sidebar-close" class="lg:hidden p-2 rounded-xl text-[var(--text-muted)] hover:bg-[var(--surface-raised)] transition-colors">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                    <a href="{{ route('home') }}" class="sidebar-link @if(request()->routeIs('home')) active @endif flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors">
                        <x-icon name="layout-dashboard" class="w-5 h-5" />
                        Overview
                    </a>
                    <a href="{{ route('transfer') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors @if(request()->routeIs('transfer')) text-[var(--primary)] bg-[var(--accent-subtle)] @else text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--line-soft)] @endif">
                        <x-icon name="send" class="w-5 h-5" />
                        Payments
                    </a>
                    <a href="{{ route('activity') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors @if(request()->routeIs('activity')) text-[var(--primary)] bg-[var(--accent-subtle)] @else text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--line-soft)] @endif">
                        <x-icon name="list-tree" class="w-5 h-5" />
                        Transactions
                    </a>
                    <a href="{{ route('receive') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors @if(request()->routeIs('receive')) text-[var(--primary)] bg-[var(--accent-subtle)] @else text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--line-soft)] @endif">
                        <x-icon name="download" class="w-5 h-5" />
                        Receive
                    </a>
                    <a href="{{ route('deposit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors @if(request()->routeIs('deposit')) text-[var(--primary)] bg-[var(--accent-subtle)] @else text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--line-soft)] @endif">
                        <x-icon name="wallet" class="w-5 h-5" />
                        Deposit
                    </a>
                    <a href="{{ route('cards') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors @if(request()->routeIs('cards')) text-[var(--primary)] bg-[var(--accent-subtle)] @else text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--line-soft)] @endif">
                        <x-icon name="credit-card" class="h-6 w-6" />
                        Cards
                    </a>
                    <a href="{{ route('financial-services') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors @if(request()->routeIs('financial-services')) text-[var(--primary)] bg-[var(--accent-subtle)] @else text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--line-soft)] @endif">
                        <x-icon name="trending-up" class="w-5 h-5" />
                        Financial Services
                    </a>
                    <a href="{{ route('messages') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors @if(request()->routeIs('messages')) text-[var(--primary)] bg-[var(--accent-subtle)] @else text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--line-soft)] @endif">
                        <x-icon name="message-square" class="w-5 h-5" />
                        Messages
                    </a>
                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors @if(request()->routeIs('profile')) text-[var(--primary)] bg-[var(--accent-subtle)] @else text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--line-soft)] @endif">
                        <x-icon name="user" class="h-6 w-6" />
                        Profile
                    </a>
                </nav>
            </aside>
        @endif
    @endauth

        <div class="flex-1 flex flex-col min-h-screen w-full overflow-x-hidden">
            <header class="h-16 flex items-center justify-between gap-3 px-3 sm:px-4 border-b border-[var(--line)] bg-white dark:bg-[var(--surface)] flex-shrink-0 lg:hidden">
                <div class="flex items-center gap-3">
                    @auth
                        @if(!auth()->user()->hasRole('Super Admin'))
                            <button id="mobile-menu-btn" class="p-2.5 rounded-xl text-[var(--text-muted)] hover:bg-[var(--surface-raised)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                                <x-icon name="menu" class="w-5 h-5" />
                            </button>
                        @endif
                    @endauth
                    <x-icon name="landmark" class="w-7 h-7 text-[var(--primary)]" />
                    <span class="font-semibold text-[var(--primary)] tracking-tight">Bastion Peak</span>
                </div>
                @auth
                    @if(!auth()->user()->hasRole('Super Admin'))
                        <div class="flex items-center gap-2">
                            <a href="{{ route('notifications') }}" class="relative p-2.5 rounded-xl text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--surface-raised)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                                <x-icon name="bell" class="w-5 h-5" />
                                @php
                                    $unreadNotifications = auth()->user()->notifications()->whereNull('read_at')->count();
                                @endphp
                                @if($unreadNotifications > 0)
                                    <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-[9px] font-bold text-white bg-red-500 rounded-full" data-unread-notif-badge>{{ $unreadNotifications }}</span>
                                @endif
                            </a>
                        </div>
                    @endif
                @endauth
            </header>

            <main class="flex-1 overflow-y-auto w-full pb-16 lg:pb-0" id="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Mobile Bottom Navigation --}}
    @auth
        @if(!auth()->user()->hasRole('Super Admin'))
            <nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-[var(--surface)] border-t border-[var(--line)] flex items-center justify-around py-2.5 lg:hidden z-50 bottom-nav" style="bottom: 0; left: 0; right: 0;">
                <a href="{{ route('home') }}" class="bottom-nav-item flex flex-col items-center justify-center w-1/4 min-h-[44px] text-xs font-medium @if(request()->routeIs('home')) text-[var(--primary)] @else text-[var(--text-muted)] @endif">
                    <x-icon name="layout-dashboard" class="w-5 h-5 mb-0.5" />
                    Overview
                </a>
                <a href="{{ route('receive') }}" class="bottom-nav-item flex flex-col items-center justify-center w-1/4 min-h-[44px] text-xs font-medium @if(request()->routeIs('receive')) text-[var(--primary)] @else text-[var(--text-muted)] @endif">
                    <x-icon name="download" class="w-5 h-5 mb-0.5" />
                    Receive
                </a>
                <a href="{{ route('deposit') }}" class="bottom-nav-item flex flex-col items-center justify-center w-1/4 min-h-[44px] text-xs font-medium @if(request()->routeIs('deposit')) text-[var(--primary)] @else text-[var(--text-muted)] @endif">
                    <x-icon name="wallet" class="w-5 h-5 mb-0.5" />
                    Deposit
                </a>
                <button id="mobile-menu-btn-bottom" class="bottom-nav-item flex flex-col items-center justify-center w-1/4 min-h-[44px] text-xs font-medium text-[var(--text-muted)]">
                    <x-icon name="menu" class="w-5 h-5 mb-0.5" />
                    Menu
                </button>
            </nav>
        @endif
    @endauth

    @if(auth()->check() && !auth()->user()->hasRole('Super Admin'))
        <script>
            (function() {
                const sidebar = document.getElementById('customer-sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                const closeBtn = document.getElementById('sidebar-close');
                const menuBtn = document.getElementById('mobile-menu-btn');
                const menuBtnBottom = document.getElementById('mobile-menu-btn-bottom');

                function closeSidebar() {
                    sidebar.classList.add('sidebar-closed');
                    overlay.classList.add('hidden');
                }
                function openSidebar() {
                    sidebar.classList.remove('sidebar-closed');
                    overlay.classList.remove('hidden');
                }
                function toggleSidebar() {
                    if (sidebar.classList.contains('sidebar-closed')) {
                        openSidebar();
                    } else {
                        closeSidebar();
                    }
                }

                if (menuBtn) menuBtn.addEventListener('click', toggleSidebar);
                if (menuBtnBottom) menuBtnBottom.addEventListener('click', toggleSidebar);
                if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
                if (overlay) overlay.addEventListener('click', closeSidebar);

                document.querySelectorAll('#customer-sidebar a[href]').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 1024) {
                            closeSidebar();
                        }
                    });
                });
            })();
        </script>
    @endif

    @if(auth()->check() && !auth()->user()->hasRole('Super Admin'))
        <div id="support-widget" class="fixed bottom-24 right-6 z-[90] w-80 hidden lg:bottom-6">
            <div class="bg-white border border-[var(--line)] rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-[var(--primary)] text-white p-4 flex items-center justify-between cursor-pointer" onclick="toggleSupportChat()">
                <div class="flex items-center gap-3">
                    <x-icon name="message-circle" class="w-5 h-5" />
                    <span>Support</span>
                </div>
                <x-icon name="chevron-up" class="w-5 h-5" />
            </div>

        <div id="support-chat-body" class="hidden">
            <div class="p-4 space-y-3 max-h-[300px] overflow-y-auto" id="support-chat-messages">
                @php
                    $currentUserId = auth()->id();
                    $threads = \App\Models\Message::where(function ($q) use ($currentUserId) {
                        $q->where('sender_id', $currentUserId)->orWhere('recipient_id', $currentUserId);
                    })->orderByDesc('created_at')->limit(10)->get();
                @endphp
                @forelse($threads as $msg)
                    <div class="{{ $msg->sender_id === auth()->id() ? 'ml-auto text-right' : '' }}">
                        <div class="{{ $msg->sender_id === auth()->id() ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface-raised)]' }} p-2.5 rounded-xl inline-block max-w-[80%]">
                            <p class="text-xs">{{ Str::limit($msg->body, 50) }}</p>
                            <p class="text-[9px] opacity-60 mt-1">{{ $msg->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-[var(--text-muted)] text-center py-4">No messages yet. Send a message and our support team will respond shortly.</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('messages.send') }}" class="p-3 border-t border-[var(--line)]">
                @csrf
                <div class="flex gap-2">
                    <textarea name="body" required placeholder="Type a message..." rows="2" class="flex-1 px-3 py-2 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm resize-none"></textarea>
                    <button type="submit" class="px-4 py-2 bg-[var(--primary)] text-white rounded-xl text-sm font-semibold hover:bg-[var(--primary-dark)] transition-colors">Send</button>
                </div>
            </form>
        </div>

        <div id="support-chat-collapsed" class="p-3 border-t border-[var(--line)]">
            <a href="{{ route('messages') }}" class="text-xs text-[var(--accent)] hover:text-[var(--accent-dark)] font-medium">Open messages</a>
        </div>
    </div>

    <button onclick="toggleSupportWidget()" class="fixed bottom-24 right-6 z-[90] w-14 h-14 bg-[var(--primary)] hover:bg-[var(--primary-dark)] text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 hover:scale-105 lg:bottom-6">
        <x-icon name="message-circle" class="w-6 h-6" />
        @php
            $unreadCount = auth()->user()->unreadMessagesCount();
        @endphp
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-[9px] font-bold text-white bg-red-500 rounded-full">{{ $unreadCount }}</span>
        @endif
    </button>
    @endif

    <script>
        let supportWidgetOpen = localStorage.getItem('support-widget-open') === 'true';
        if (supportWidgetOpen) {
            document.getElementById('support-widget').classList.remove('hidden');
        }

        function toggleSupportWidget() {
            const widget = document.getElementById('support-widget');
            if (widget.classList.contains('hidden')) {
                widget.classList.remove('hidden');
                localStorage.setItem('support-widget-open', 'true');
                supportWidgetOpen = true;
            } else {
                widget.classList.add('hidden');
                localStorage.setItem('support-widget-open', 'false');
                supportWidgetOpen = false;
            }
        }

        function toggleSupportChat() {
            const body = document.getElementById('support-chat-body');
            const collapsed = document.getElementById('support-chat-collapsed');
            body.classList.toggle('hidden');
            collapsed.classList.toggle('hidden');
        }
    </script>

    @if(auth()->check() && !auth()->user()->hasRole('Super Admin'))
    <script>
    function numberWithSymbol(num, symbol, decimals) {
        var parts = num.toFixed(decimals).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return symbol + ' ' + parts.join('.');
    }

    (function() {
        let pollInterval = null;

        function refreshCounts() {
            fetch('{{ route('dashboard.updates') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    document.querySelectorAll('[data-unread-notif-badge]').forEach(el => {
                        const count = data.unread_notifications;
                        el.textContent = count;
                        el.style.display = count > 0 ? 'flex' : 'none';
                    });
                    document.querySelectorAll('[data-unread-msg-badge]').forEach(el => {
                        const count = data.unread_messages;
                        el.textContent = count;
                        el.style.display = count > 0 ? 'flex' : 'none';
                    });
                     document.querySelectorAll('[data-balance]').forEach((el, i) => {
                        const account = data.accounts[i];
                        if (account) {
                            const formatted = numberWithSymbol(account.balance, account.currency_symbol, account.currency_decimals);
                            el.textContent = formatted;
                        }
                    });
                 })
                .catch(() => {});
        }

        document.addEventListener('DOMContentLoaded', function() {
            pollInterval = setInterval(refreshCounts, 30000);
        });
    })();
    </script>
    @endif

    @stack('scripts')
</body>
</html>
