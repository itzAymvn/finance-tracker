<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@300;400;500&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-cream font-sans text-ink antialiased"
      style="background-image:url(\"data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.018'/%3E%3C/svg%3E\"); background-size:180px 180px">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <a href="{{ route('login') }}" class="flex items-center gap-2.5 text-ink font-serif text-xl tracking-tight no-underline hover:text-amber transition-colors">
            <span class="w-10 h-10 bg-amber rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 fill-white" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14.93V18h-2v-1.07A4.01 4.01 0 0 1 8 13h2c0 1.1.9 2 2 2s2-.9 2-2c0-1.1-.9-2-2-2a4 4 0 0 1 0-8V6h2v-.07A4.01 4.01 0 0 1 16 9h-2c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2a4 4 0 0 1 0 8z"/></svg>
            </span>
            Payroll
        </a>
        <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-cream-2 border border-border rounded-xl shadow-sm overflow-hidden">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
