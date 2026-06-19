<?php

namespace App\Console\Commands;

use App\Models\SalaryAllocation;
use App\Models\SalaryMonth;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;

class DbExport extends Command
{
    protected $signature = 'db:export {path? : Output file path}';
    protected $description = 'Export all data to a portable JSON file';

    public function handle()
    {
        $path = $this->argument('path') ?? storage_path('app/backups/backup-' . now()->format('Y-m-d-His') . '.json');

        $data = [
            'exported_at' => now()->toIso8601String(),
            'users' => User::all()->map(fn ($u) => array_merge($u->toArray(), ['password' => $u->getAuthPassword()]))->values()->toArray(),
            'transactions' => Transaction::all()->toArray(),
            'salary_months' => SalaryMonth::all()->toArray(),
            'salary_allocations' => SalaryAllocation::all()->toArray(),
        ];

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Exported to {$path}");
    }
}
