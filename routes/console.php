<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Run the auto-backup check every hour. The command itself decides whether
// to actually create a backup based on the configured interval.
Schedule::command('backup:auto')->hourly()->description('Automatic backup (if enabled and interval elapsed)');
Schedule::command('subscriptions:generate')->everyFiveMinutes()->description('Generate due subscription transactions');
