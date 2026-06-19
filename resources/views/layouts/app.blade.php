<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Payroll') — Payroll</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('dark') === 'true' || (!('dark' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-surface dark:bg-surface-dark text-ink dark:text-white font-sans">

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="hidden lg:flex lg:flex-col lg:w-64 shrink-0 bg-surface-sidebar text-white">
            <div class="flex items-center gap-3 px-6 h-16 border-b border-white/10">
                <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14.93V18h-2v-1.07A4.01 4.01 0 0 1 8 13h2c0 1.1.9 2 2 2s2-.9 2-2c0-1.1-.9-2-2-2a4 4 0 0 1 0-8V6h2v-.07A4.01 4.01 0 0 1 16 9h-2c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2a4 4 0 0 1 0 8z"/></svg>
                </div>
                <span class="font-semibold text-lg tracking-tight">Payroll</span>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Overview
                </a>
                <a href="{{ route('transactions.index') }}"
                   class="sidebar-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Transactions
                </a>
            </nav>

            <div class="px-3 py-4 border-t border-white/10">
                <a href="{{ route('profile.edit') }}"
                   class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile
                </a>
                <a href="{{ route('backup.index') }}"
                   class="sidebar-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Backup
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-left">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Log out
                    </button>
                </form>
            </div>
        </aside>

        {{-- Mobile sidebar --}}
        <div id="mobile-sidebar" class="lg:hidden fixed inset-0 z-40 hidden">
            <div id="mobile-overlay" class="fixed inset-0 bg-black/50 transition-opacity duration-300"></div>
            <div id="mobile-panel" class="fixed inset-y-0 left-0 w-64 bg-surface-sidebar text-white shadow-xl transition-transform duration-300 -translate-x-full">
                <div class="flex items-center justify-between px-6 h-16 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center">
                            <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14.93V18h-2v-1.07A4.01 4.01 0 0 1 8 13h2c0 1.1.9 2 2 2s2-.9 2-2c0-1.1-.9-2-2-2a4 4 0 0 1 0-8V6h2v-.07A4.01 4.01 0 0 1 16 9h-2c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2a4 4 0 0 1 0 8z"/></svg>
                        </div>
                        <span class="font-semibold text-lg">Payroll</span>
                    </div>
                    <button id="mobile-close" class="p-1 rounded-md text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <nav class="px-3 py-4 space-y-1">
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Overview
                    </a>
                    <a href="{{ route('transactions.index') }}" class="sidebar-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Transactions
                    </a>
                    <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profile
                    </a>
                    <a href="{{ route('backup.index') }}" class="sidebar-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Backup
                    </a>
                </nav>
                <div class="px-3 py-4 border-t border-white/10">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="sidebar-link w-full text-left">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main content area --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top bar --}}
            <header class="bg-surface-card dark:bg-surface-dark-card border-b border-border dark:border-border-dark h-16 shrink-0 flex items-center px-4 lg:px-6 gap-4">
                <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-lg text-ink-soft dark:text-slate-400 hover:text-ink dark:hover:text-white hover:bg-surface-hover dark:hover:bg-surface-dark-hover transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="flex-1"></div>

                <button id="dark-toggle" class="p-2 rounded-lg text-ink-soft dark:text-slate-400 hover:text-ink dark:hover:text-white hover:bg-surface-hover dark:hover:bg-surface-dark-hover transition-colors" title="Toggle dark mode">
                    <svg id="sun-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg id="moon-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>

                <a href="{{ route('salary-months.create') }}" class="btn-secondary text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Month
                </a>
                <a href="{{ route('transactions.create') }}" class="btn-primary text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New
                </a>

                <div class="flex items-center gap-2 pl-4 border-l border-border dark:border-border-dark">
                    <div class="w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-ink-dim dark:text-slate-300">{{ Auth::user()->name }}</span>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto">
                <div class="max-w-6xl mx-auto px-4 lg:px-6 py-8">

                    @if(session('success'))
                    <div class="flash-success mb-5">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="flex-1">{{ session('success') }}</span>
                        <button class="shrink-0 opacity-50 hover:opacity-100" onclick="this.closest('div').remove()">&times;</button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="flash-error mb-5">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="flex-1">{{ session('error') }}</span>
                        <button class="shrink-0 opacity-50 hover:opacity-100" onclick="this.closest('div').remove()">&times;</button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="flash-error mb-5">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="flex-1">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mt-1 ml-4 list-disc space-y-0.5">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button class="shrink-0 opacity-50 hover:opacity-100" onclick="this.closest('div').remove()">&times;</button>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
            const sidebar = document.getElementById('mobile-sidebar');
            const panel = document.getElementById('mobile-panel');
            if (sidebar && panel) {
                sidebar.classList.remove('hidden');
                requestAnimationFrame(() => panel.classList.remove('-translate-x-full'));
            }
        });
        function closeMobile() {
            const sidebar = document.getElementById('mobile-sidebar');
            const panel = document.getElementById('mobile-panel');
            if (sidebar && panel) {
                panel.classList.add('-translate-x-full');
                setTimeout(() => sidebar.classList.add('hidden'), 300);
            }
        }
        document.getElementById('mobile-overlay')?.addEventListener('click', closeMobile);
        document.getElementById('mobile-close')?.addEventListener('click', closeMobile);

        const toggle = document.getElementById('dark-toggle');
        const sun = document.getElementById('sun-icon');
        const moon = document.getElementById('moon-icon');
        if (toggle) {
            if (document.documentElement.classList.contains('dark')) {
                sun.classList.remove('hidden');
                moon.classList.add('hidden');
            }
            toggle.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('dark', isDark);
                sun.classList.toggle('hidden', !isDark);
                moon.classList.toggle('hidden', isDark);
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
