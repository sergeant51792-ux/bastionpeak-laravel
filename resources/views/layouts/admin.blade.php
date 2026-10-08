<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — Bastion Peak</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link.active { background: var(--primary); color: white; }
        .sidebar-link:hover:not(.active) { background: var(--line-soft); color: var(--text); }
        .sidebar-link.active svg { stroke: white; }

        @media (min-width: 1024px) {
            .sidebar-collapsed {
                width: 72px !important;
            }
            .sidebar-collapsed .sidebar-link-label,
            .sidebar-collapsed .logo-text {
                display: none;
            }
        }
        @media (max-width: 1023px) {
            .sidebar-closed {
                transform: translateX(-100%);
            }
            .sidebar-open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-[var(--bg)] text-[var(--text)]">
    <div class="flex min-h-screen">
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden"></div>

        <aside id="admin-sidebar" class="bg-white dark:bg-[var(--surface)] border-r border-[var(--line)] fixed inset-y-0 left-0 z-50 transition-all duration-300 ease-in-out w-60 sidebar-closed lg:translate-x-0 lg:static lg:inset-auto lg:z-auto lg:sidebar-open lg:w-60">
            <div class="h-16 flex items-center justify-between px-3 border-b border-[var(--line)]">
                <div class="flex items-center gap-2.5">
                    <x-icon name="landmark" class="w-7 h-7 text-[var(--primary)] flex-shrink-0" />
                    <span class="logo-text font-semibold text-[var(--primary)] tracking-tight">Bastion Peak</span>
                </div>
                <button id="sidebar-toggle-desktop" class="hidden lg:inline-flex p-2 rounded-xl text-[var(--text-muted)] hover:bg-[var(--surface-raised)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                    <x-icon name="panel-left-open" class="w-5 h-5" />
                </button>
                <button id="mobile-menu-close" class="lg:hidden p-2 rounded-xl text-[var(--text-muted)] hover:bg-[var(--surface-raised)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                @php
                    $sidebarItems = [
                        ['route' => 'admin.approvals', 'icon' => 'check-circle', 'label' => 'Approvals'],
                        ['route' => 'admin.users.index', 'icon' => 'users', 'label' => 'Users'],
                        ['route' => 'admin.cards.index', 'icon' => 'credit-card', 'label' => 'Cards'],
                        ['route' => 'admin.messages.index', 'icon' => 'message-square', 'label' => 'Messages'],
                        ['route' => 'admin.deposit-methods.index', 'icon' => 'wallet', 'label' => 'Deposit Methods'],
                    ];
                @endphp
                @foreach($sidebarItems as $item)
                    @php
                        $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                        if ($item['route'] === 'admin.users.index' && request()->routeIs('admin.overview')) {
                            $isActive = true;
                        }
                    @endphp
                    <a href="{{ route($item['route']) }}" class="sidebar-link @if($isActive) active @endif flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors">
                        <x-icon name="{{ $item['icon'] }}" class="w-5 h-5 flex-shrink-0" />
                        <span class="sidebar-link-label">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-h-screen" id="admin-main">
            <header class="h-16 flex items-center gap-3 justify-between px-3 sm:px-4 border-b border-[var(--line)] bg-white dark:bg-[var(--surface)] flex-shrink-0">
                <div class="flex items-center gap-3">
                    <button id="mobile-menu-btn" class="lg:hidden p-2.5 rounded-xl text-[var(--text-muted)] hover:bg-[var(--surface-raised)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                        <x-icon name="menu" class="w-5 h-5" />
                    </button>
                    <h1 class="text-base font-semibold text-[var(--text)] tracking-tight">@yield('title', 'Admin')</h1>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[var(--primary)] flex items-center justify-center text-white text-xs font-semibold">{{ substr(auth()->user()->name ?? 'Admin', 0, 2) }}</div>
                </div>
            </header>

            <main class="p-3 sm:p-4 lg:p-6 w-full min-w-0 overflow-x-hidden">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('mobile-menu-close');
        const desktopToggleBtn = document.getElementById('desktop-sidebar-toggle');

        function closeMobileSidebar() {
            sidebar.classList.add('sidebar-closed');
            sidebar.classList.remove('sidebar-open');
            overlay.classList.add('hidden');
        }

        function openMobileSidebar() {
            sidebar.classList.remove('sidebar-closed');
            sidebar.classList.add('sidebar-open');
            overlay.classList.remove('hidden');
        }

        function toggleDesktopSidebar() {
            sidebar.classList.toggle('sidebar-collapsed');
        }

        if (mobileBtn) {
            mobileBtn.addEventListener('click', openMobileSidebar);
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', closeMobileSidebar);
        }
        if (overlay) {
            overlay.addEventListener('click', closeMobileSidebar);
        }
        if (desktopToggleBtn) {
            desktopToggleBtn.addEventListener('click', toggleDesktopSidebar);
        }

        document.querySelectorAll('#admin-sidebar a[href]').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    closeMobileSidebar();
                }
            });
        });

        // Initialize: sidebar should be open on desktop, closed on mobile
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('sidebar-closed');
            sidebar.classList.remove('sidebar-collapsed');
        }
    </script>

    @stack('scripts')
</body>
</html>
