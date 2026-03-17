<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Payroll') — Payroll</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@300;400;500&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<body class="min-h-full bg-cream font-sans text-ink antialiased"
      style="background-image:url(\"data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.018'/%3E%3C/svg%3E\"); background-size:180px 180px">

    {{-- ── Navbar ── --}}
    <nav class="sticky top-0 z-50 bg-ink border-b border-white/5 backdrop-blur-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-stretch h-14 gap-0">

                {{-- Brand --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2.5 pr-6 border-r border-white/8 text-cream font-serif text-lg tracking-tight shrink-0 no-underline hover:text-amber-lt transition-colors duration-150">
                    <span class="w-7 h-7 bg-amber rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14.93V18h-2v-1.07A4.01 4.01 0 0 1 8 13h2c0 1.1.9 2 2 2s2-.9 2-2c0-1.1-.9-2-2-2a4 4 0 0 1 0-8V6h2v-.07A4.01 4.01 0 0 1 16 9h-2c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2a4 4 0 0 1 0 8z"/></svg>
                    </span>
                    Payroll
                </a>

                {{-- Desktop links --}}
                @auth
                <ul class="hidden lg:flex items-stretch list-none m-0 p-0 pl-1 gap-0 flex-1">
                    <li class="flex items-stretch">
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center px-4 text-cream/60 text-xs font-semibold tracking-widest uppercase no-underline border-b-2 transition-colors duration-150
                                  {{ request()->routeIs('dashboard') ? 'text-amber-lt border-amber-lt' : 'border-transparent hover:text-cream' }}">
                            Overview
                        </a>
                    </li>
                    <li class="flex items-stretch">
                        <a href="{{ route('payouts.index') }}"
                           class="flex items-center px-4 text-cream/60 text-xs font-semibold tracking-widest uppercase no-underline border-b-2 transition-colors duration-150
                                  {{ request()->routeIs('payouts.*') ? 'text-amber-lt border-amber-lt' : 'border-transparent hover:text-cream' }}">
                            Payouts
                        </a>
                    </li>
                </ul>
                @endauth

                {{-- Desktop action buttons --}}
                <div class="hidden lg:flex items-center gap-2 pl-4 border-l border-white/8 ml-auto">
                    @auth
                    <a href="{{ route('salary-months.create') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold tracking-widest uppercase text-cream/70 border border-white/15 hover:text-cream hover:bg-white/8 no-underline transition-all duration-150">
                        + Month
                    </a>
                    <a href="{{ route('payouts.create') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold tracking-widest uppercase text-ink bg-amber hover:bg-amber-lt no-underline transition-all duration-150">
                        + Payout
                    </a>
                    <span class="text-cream/50 text-xs ml-2">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold tracking-widest uppercase text-cream/70 border border-white/15 hover:text-cream hover:bg-white/8 no-underline transition-all duration-150 cursor-pointer">
                            Log out
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold tracking-widest uppercase text-ink bg-amber hover:bg-amber-lt no-underline transition-all duration-150">
                        Log in
                    </a>
                    @endauth
                </div>

                {{-- Mobile toggle --}}
                <button id="nav-toggle"
                        class="lg:hidden ml-auto flex items-center justify-center w-9 h-9 my-auto rounded-md border border-white/20 text-cream/70 hover:text-cream hover:bg-white/8 transition-colors">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5h14v1.5H3V5zm0 4.5h14V11H3V9.5zm0 4.5h14v1.5H3V14z"/></svg>
                </button>
            </div>

            {{-- Mobile menu --}}
            <div id="nav-mobile" class="hidden lg:hidden border-t border-white/8 py-3">
                @auth
                <div class="flex flex-col gap-1 mb-3">
                    <a href="{{ route('dashboard') }}"
                       class="px-2 py-2 text-xs font-semibold tracking-widest uppercase no-underline border-l-2 transition-colors
                              {{ request()->routeIs('dashboard') ? 'text-amber-lt border-amber-lt' : 'text-cream/60 border-transparent hover:text-cream' }}">
                        Overview
                    </a>
                    <a href="{{ route('payouts.index') }}"
                       class="px-2 py-2 text-xs font-semibold tracking-widest uppercase no-underline border-l-2 transition-colors
                              {{ request()->routeIs('payouts.*') ? 'text-amber-lt border-amber-lt' : 'text-cream/60 border-transparent hover:text-cream' }}">
                        Payouts
                    </a>
                </div>
                <div class="flex flex-wrap gap-2 pt-3 border-t border-white/8">
                    <a href="{{ route('salary-months.create') }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold tracking-widest uppercase text-cream/70 border border-white/15 hover:text-cream no-underline transition-colors">
                        + Month
                    </a>
                    <a href="{{ route('payouts.create') }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold tracking-widest uppercase text-ink bg-amber hover:bg-amber-lt no-underline transition-colors">
                        + Payout
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-md text-xs font-semibold tracking-widest uppercase text-cream/70 border border-white/15 hover:text-cream transition-colors cursor-pointer">
                            Log out
                        </button>
                    </form>
                </div>
                @else
                <a href="{{ route('login') }}"
                   class="inline-flex px-3 py-1.5 rounded-md text-xs font-semibold tracking-widest uppercase text-ink bg-amber hover:bg-amber-lt no-underline transition-colors">
                    Log in
                </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ── Page ── --}}
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-9 pb-20">

        {{-- Flash success --}}
        @if(session('success'))
        <div class="flex items-start gap-3 mb-5 p-4 rounded-xl bg-emerald-lt border-l-4 border-emerald text-emerald text-sm animate-[fadeUp_.3s_ease_both]">
            <span class="shrink-0 mt-0.5 font-bold">✓</span>
            <span>{{ session('success') }}</span>
            <button class="ml-auto shrink-0 opacity-50 hover:opacity-100 transition-opacity text-base leading-none" onclick="this.closest('div').remove()">×</button>
        </div>
        @endif

        {{-- Flash error --}}
        @if(session('error'))
        <div class="flex items-start gap-3 mb-5 p-4 rounded-xl bg-ruby-lt border-l-4 border-ruby text-ruby text-sm">
            <span class="shrink-0 mt-0.5 font-bold">!</span>
            <span>{{ session('error') }}</span>
            <button class="ml-auto shrink-0 opacity-50 hover:opacity-100 transition-opacity text-base leading-none" onclick="this.closest('div').remove()">×</button>
                    </div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="flex items-start gap-3 mb-5 p-4 rounded-xl bg-ruby-lt border-l-4 border-ruby text-ruby text-sm">
            <span class="shrink-0 mt-0.5 font-bold">!</span>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul class="mt-1 ml-4 list-disc space-y-0.5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button class="ml-auto shrink-0 opacity-50 hover:opacity-100 transition-opacity text-base leading-none" onclick="this.closest('div').remove()">×</button>
        </div>
        @endif

        @yield('content')
    </main>

    <script>
        document.getElementById('nav-toggle')?.addEventListener('click', () => {
            document.getElementById('nav-mobile').classList.toggle('hidden');
        });
    </script>
    @yield('scripts')
    </body>
</html>
