<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\SalaryAllocation;
use App\Models\SalaryMonth;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class BackupAuto extends Command
{
    protected $signature = 'backup:auto';

    protected $description = 'Create an automatic backup if enabled and the configured interval has elapsed since the last auto-backup.';

    private const ALLOWED_INTERVALS = [6, 12, 24, 168];

    public function handle(): int
    {
        $enabled = (bool) Setting::get('backup_enabled', '0');
        if (! $enabled) {
            $this->info('Auto-backup disabled. Skipping.');

            return self::SUCCESS;
        }

        $intervalHours = (int) Setting::get('backup_interval_hours', '24');
        if (! in_array($intervalHours, self::ALLOWED_INTERVALS, true)) {
            $this->warn("Invalid interval {$intervalHours}h. Falling back to 24h.");
            $intervalHours = 24;
        }

        $last = $this->lastAutoBackupTimestamp();
        if ($last !== null) {
            $elapsedMinutes = (int) now()->diffInMinutes($last);
            $intervalMinutes = $intervalHours * 60;
            if ($elapsedMinutes < $intervalMinutes) {
                $remaining = $intervalMinutes - $elapsedMinutes;
                $this->info(sprintf(
                    'Last auto-backup %s. Interval %dh not reached (%dmin remaining). Skipping.',
                    $last->diffForHumans(),
                    $intervalHours,
                    $remaining
                ));

                return self::SUCCESS;
            }
        }

        $filename = 'backups/auto-'.now()->format('Y-m-d-His').'.json';
        $data = [
            'exported_at' => now()->toIso8601String(),
            'kind' => 'auto',
            'users' => User::all()->map(fn ($u) => array_merge($u->toArray(), ['password' => $u->getAuthPassword()]))->values()->toArray(),
            'categories' => Category::all()->toArray(),
            'transactions' => Transaction::all()->toArray(),
            'salary_months' => SalaryMonth::all()->toArray(),
            'salary_allocations' => SalaryAllocation::all()->toArray(),
            'subscriptions' => Subscription::all()->toArray(),
        ];

        Storage::put($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info('Auto-backup created: '.basename($filename));

        return self::SUCCESS;
    }

    private function lastAutoBackupTimestamp(): ?Carbon
    {
        $latestTs = 0;
        foreach (Storage::files('backups') as $f) {
            if (! str_starts_with(basename($f), 'auto-')) {
                continue;
            }
            $ts = Storage::lastModified($f);
            if ($ts > $latestTs) {
                $latestTs = $ts;
            }
        }

        return $latestTs > 0 ? Carbon::createFromTimestamp($latestTs) : null;
    }
}
