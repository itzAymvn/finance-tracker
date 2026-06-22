<?php

namespace App\Services;

use App\Models\SalaryAllocation;
use App\Models\SalaryMonth;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AllocationService
{
    /**
     * Allocate a salary transaction FIFO across eligible salary months
     * (month_key <= transaction's paid_at month). Each month is capped at
     * expected_salary; surplus rolls forward. Any leftover (insufficient
     * future-eligible capacity) remains unallocated on the transaction.
     *
     * Sets $transaction->salary_month_id to the first month touched.
     *
     * @return int  Number of allocation rows created.
     */
    public function reallocate(Transaction $transaction): int
    {
        return DB::transaction(function () use ($transaction) {
            if (! $transaction->is_salary || ! $transaction->isCredit()) {
                $transaction->salary_month_id = null;
                $transaction->save();
                $transaction->allocations()->delete();

                return 0;
            }

            $transaction->allocations()->delete();

            $paidMonthKey = $transaction->paid_at->format('Y-m');
            $months = SalaryMonth::where('month_key', '<=', $paidMonthKey)
                ->orderBy('month_key')
                ->lockForUpdate()
                ->get();

            if ($months->isEmpty()) {
                $transaction->salary_month_id = null;
                $transaction->save();

                return 0;
            }

            // Build capacity map: id => already-assigned total (excluding this tx).
            $totals = DB::table('salary_allocations')
                ->selectRaw('salary_month_id, SUM(amount) as total')
                ->where('transaction_id', '!=', $transaction->id)
                ->groupBy('salary_month_id')
                ->pluck('total', 'salary_month_id')
                ->map(fn ($v) => (float) $v)
                ->all();

            $remaining = (float) $transaction->amount;
            $primaryMonthId = null;
            $created = 0;

            foreach ($months as $month) {
                if ($remaining <= 0.005) {
                    break;
                }
                $assigned = $totals[$month->id] ?? 0.0;
                $capacity = (float) $month->expected_salary - $assigned;
                if ($capacity <= 0.005) {
                    continue;
                }
                $chunk = min($capacity, $remaining);

                SalaryAllocation::create([
                    'transaction_id' => $transaction->id,
                    'salary_month_id' => $month->id,
                    'amount' => round($chunk, 2),
                ]);
                $created++;

                $totals[$month->id] = ($totals[$month->id] ?? 0.0) + $chunk;
                $remaining -= $chunk;

                if ($primaryMonthId === null) {
                    $primaryMonthId = $month->id;
                }
            }

            $transaction->salary_month_id = $primaryMonthId;
            $transaction->save();

            return $created;
        });
    }
}
