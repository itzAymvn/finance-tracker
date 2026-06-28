<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NotifySubscriptionsRenewing extends Command
{
    protected $signature = 'subscriptions:notify';
    protected $description = 'Send Discord notifications for subscriptions renewing within the configured window';

    public function handle(): int
    {
        $webhookUrl = config('services.discord.webhook_url');
        $userId = config('services.discord.user_id');
        $notifyHours = (float) config('services.discord.notify_hours', 1);

        if (! $webhookUrl) {
            $this->warn('Discord webhook URL not configured.');

            return self::SUCCESS;
        }

        $cutoff = Carbon::now()->addHours($notifyHours);
        $sent = 0;

        Subscription::active()
            ->whereNotNull('start_at')
            ->orderBy('id')
            ->chunkById(200, function ($subscriptions) use ($webhookUrl, $userId, $cutoff, &$sent) {
                foreach ($subscriptions as $sub) {
                    $nextDueAt = $sub->getNextDueAt();

                    if (! $nextDueAt || $nextDueAt->gt($cutoff) || $nextDueAt->isPast()) {
                        continue;
                    }

                    // Dedupe per due cycle: once notified for this next_due_at,
                    // don't notify again until it advances to a new period.
                    $cacheKey = "sub:notify:{$sub->id}:{$nextDueAt->timestamp}";
                    if (Cache::has($cacheKey)) {
                        continue;
                    }

                    $this->postToDiscord($webhookUrl, $userId, $sub, $nextDueAt);
                    Cache::put($cacheKey, true, now()->addDays(40));
                    $sent++;
                }
            });

        if ($sent > 0) {
            $this->info("Sent {$sent} renewal notification(s).");
        } else {
            $this->line('No renewals in the notification window.');
        }

        return self::SUCCESS;
    }

    private function postToDiscord(string $webhookUrl, ?string $userId, Subscription $sub, Carbon $nextDueAt): void
    {
        $mention = $userId ? "<@{$userId}> " : '';
        $amount = (float) $sub->amount;
        $credit = $amount > 0;
        $amountStr = ($credit ? '+' : '-') . number_format(abs($amount), 2) . ' MAD';
        $ts = "<t:{$nextDueAt->timestamp}:R>";

        $payload = [
            'username' => config('app.name', 'Payroll'),
            'content' => "{$mention}**{$sub->label}** ({$amountStr}, " . ucfirst($sub->frequency) . ") renews {$ts}.",
        ];

        Http::post($webhookUrl, $payload)->throw();
    }
}
