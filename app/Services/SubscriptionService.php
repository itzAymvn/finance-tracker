<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;

class SubscriptionService
{
    public function __construct(private readonly AllocationService $allocationService) {}

    public function generateDueTransactions(): int
    {
        $count = 0;
        $now = Carbon::now();

        Subscription::active()
            ->whereNotNull('start_at')
            ->get()
            ->each(function (Subscription $sub) use ($now, &$count) {
                $base = $sub->last_generated_at ?? $sub->start_at;

                // First generation: use start_at directly if no last_generated_at
                if (!$sub->last_generated_at && $sub->start_at->lte($now)) {
                    $this->createTransaction($sub, $sub->start_at);
                    $sub->update(['last_generated_at' => $sub->start_at]);
                    $count++;
                    $base = $sub->start_at;
                }

                // Generate any subsequent due transactions
                while (true) {
                    $nextDue = $this->computeNextDue($base, $sub->frequency);
                    if (!$nextDue || $nextDue->gt($now)) {
                        break;
                    }

                    $this->createTransaction($sub, $nextDue);
                    $sub->update(['last_generated_at' => $nextDue]);
                    $base = $nextDue;
                    $count++;
                }
            });

        return $count;
    }

    private function computeNextDue(Carbon $from, string $frequency): ?Carbon
    {
        return match ($frequency) {
            'weekly'    => $from->copy()->addWeek(),
            'biweekly'  => $from->copy()->addWeeks(2),
            'monthly'   => $from->copy()->addMonth(),
            'quarterly' => $from->copy()->addMonths(3),
            'yearly'    => $from->copy()->addYear(),
            default     => null,
        };
    }

    private function createTransaction(Subscription $sub, Carbon $paidAt): Transaction
    {
        $transaction = Transaction::create([
            'paid_at'          => $paidAt,
            'label'            => $sub->label,
            'amount'           => $sub->amount,
            'source'           => 'subscription',
            'category_id'      => $sub->category_id,
            'subscription_id'  => $sub->id,
        ]);

        $transaction->load('category');
        if ($transaction->isSalary()) {
            $this->allocationService->reallocate($transaction);
        }

        return $transaction;
    }
}
