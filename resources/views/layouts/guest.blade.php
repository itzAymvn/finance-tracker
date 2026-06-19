<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Payroll') }}</title>
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
<body class="h-full bg-surface dark:bg-surface-dark font-sans antialiased">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <a href="{{ route('login') }}" class="flex items-center justify-center gap-3 text-ink dark:text-white no-underline mb-8">
                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center">
                    <svg class="w-5 h-5 fill-white" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14.93V18h-2v-1.07A4.01 4.01 0 0 1 8 13h2c0 1.1.9 2 2 2s2-.9 2-2c0-1.1-.9-2-2-2a4 4 0 0 1 0-8V6h2v-.07A4.01 4.01 0 0 1 16 9h-2c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2a4 4 0 0 1 0 8z"/></svg>
                </div>
                <span class="text-xl font-bold tracking-tight">Payroll</span>
            </a>

            <div class="bg-surface-card dark:bg-surface-dark-card px-6 py-8 shadow-sm border border-border dark:border-border-dark rounded-xl sm:px-10">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
