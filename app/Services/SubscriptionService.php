<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function __construct(private readonly AllocationService $allocationService) {}

    public function generateDueTransactions(): int
    {
        $count = 0;
        $now = Carbon::now();

        Subscription::active()
            ->whereNotNull('start_at')
            ->orderBy('id')
            ->chunkById(200, function ($subscriptions) use ($now, &$count) {
                foreach ($subscriptions as $sub) {
                    $count += $this->generateForSubscription($sub, $now);
                }
            });

        return $count;
    }

    /**
     * Generate all due transactions for a single subscription.
     * Wrapped in a transaction with a row lock so two concurrent
     * scheduler ticks cannot duplicate the same due period.
     */
    private function generateForSubscription(Subscription $sub, Carbon $now): int
    {
        return DB::transaction(function () use ($sub, $now) {
            // Re-load under the lock so we observe committed writes from
            // any concurrently completed tick.
            /** @var Subscription $locked */
            $locked = Subscription::whereKey($sub->id)->lockForUpdate()->first();
            if (! $locked || ! $locked->isActive() || ! $locked->start_at) {
                return 0;
            }

            $count = 0;
            $lastGenerated = $locked->last_generated_at;

            // First generation: use start_at directly if no last_generated_at.
            if (! $lastGenerated && $locked->start_at->lte($now)) {
                $this->createTransaction($locked, $locked->start_at);
                $lastGenerated = $locked->start_at;
                $count++;
            }

            // Generate any subsequent due transactions. Cap at a sane
            // upper bound to prevent an unbounded catch-up storm.
            for ($i = 0; $i < 240; $i++) {
                $nextDue = $lastGenerated
                    ? $locked->nextDueAfter($lastGenerated)
                    : null;
                if (! $nextDue || $nextDue->gt($now)) {
                    break;
                }

                $this->createTransaction($locked, $nextDue);
                $lastGenerated = $nextDue;
                $count++;
            }

            // Single write of last_generated_at per run.
            if ($lastGenerated && (
                ! $locked->last_generated_at ||
                $locked->last_generated_at->ne($lastGenerated)
            )) {
                $locked->update(['last_generated_at' => $lastGenerated]);
            }

            return $count;
        });
    }

    private function createTransaction(Subscription $sub, Carbon $paidAt): Transaction
    {
        $transaction = Transaction::create([
            'paid_at' => $paidAt,
            'label' => $sub->label,
            'amount' => $sub->amount,
            'source' => 'subscription',
            'category_id' => $sub->category_id,
            'subscription_id' => $sub->id,
        ]);

        $transaction->load('category');
        if ($transaction->isSalary()) {
            $this->allocationService->reallocate($transaction);
        }

        return $transaction;
    }
}
