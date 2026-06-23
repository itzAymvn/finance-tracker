<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class GenerateSubscriptionTransactions extends Command
{
    protected $signature = 'subscriptions:generate';
    protected $description = 'Generate transactions for due recurring subscriptions';

    public function handle(SubscriptionService $service): int
    {
        $count = $service->generateDueTransactions();

        if ($count > 0) {
            $this->info("Generated {$count} subscription transaction(s).");
        } else {
            $this->line('No subscription transactions due.');
        }

        return self::SUCCESS;
    }
}
