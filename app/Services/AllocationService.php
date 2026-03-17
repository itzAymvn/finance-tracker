<?php

namespace App\Services;

use App\Models\Payout;
use App\Models\PayoutAllocation;
use App\Models\SalaryMonth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AllocationService
{
    /**
     * Allocate a payout amount automatically to the oldest unpaid/partial months first.
     * Deletes any existing allocations for the payout before creating new ones.
     */
    public function autoAllocate(Payout $payout): void
    {
        DB::transaction(function () use ($payout) {
            $payout->allocations()->delete();

            $months = SalaryMonth::orderBy('month_key')->get();

            $remaining = (float) $payout->amount;

            foreach ($months as $month) {
                if ($remaining <= 0) {
                    break;
                }

                $monthRemaining = (float) $month->expected_salary
                    - (float) PayoutAllocation::where('salary_month_id', $month->id)->sum('amount');

                if ($monthRemaining <= 0) {
                    continue;
                }

                $allocate = min($monthRemaining, $remaining);

                PayoutAllocation::create([
                    'payout_id' => $payout->id,
                    'salary_month_id' => $month->id,
                    'amount' => $allocate,
                ]);

                $remaining -= $allocate;
            }
        });
    }

    /**
     * Manually allocate a payout to specific months with explicit amounts.
     *
     * @param  array<int, array{salary_month_id: int, amount: float}>  $lines
     */
    public function manualAllocate(Payout $payout, array $lines): void
    {
        $total = array_sum(array_column($lines, 'amount'));

        if (round($total, 2) > round((float) $payout->amount, 2)) {
            throw new InvalidArgumentException(
                "Total allocated ({$total}) exceeds payout amount ({$payout->amount})."
            );
        }

        $monthIds = array_column($lines, 'salary_month_id');
        if (count($monthIds) !== count(array_unique($monthIds))) {
            throw new InvalidArgumentException('Duplicate salary months in allocation.');
        }

        foreach ($lines as $line) {
            if ((float) $line['amount'] <= 0) {
                throw new InvalidArgumentException('Each allocation amount must be greater than zero.');
            }
        }

        DB::transaction(function () use ($payout, $lines) {
            $payout->allocations()->delete();

            foreach ($lines as $line) {
                PayoutAllocation::create([
                    'payout_id' => $payout->id,
                    'salary_month_id' => $line['salary_month_id'],
                    'amount' => $line['amount'],
                ]);
            }
        });
    }
}
