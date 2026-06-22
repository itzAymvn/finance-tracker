<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class SalaryMonth extends Model
{
    protected $fillable = [
        'month_key',
        'expected_salary',
        'currency',
        'notes',
    ];

    protected $casts = [
        'expected_salary' => 'decimal:2',
    ];

    /**
     * Allocation rows pointing at this month (each carries an amount and a
     * transaction_id).
     */
    public function salaryAllocations(): HasMany
    {
        return $this->hasMany(SalaryAllocation::class);
    }

    /**
     * Convenience: salary transactions allocated to this month, via the
     * allocations pivot. Each transaction may appear more than once if it was
     * split across multiple months (the salary_allocations.amount tells you
     * how much landed here).
     */
    public function salaryTransactions()
    {
        return $this->belongsToMany(Transaction::class, 'salary_allocations')
            ->withPivot('amount')
            ->using(SalaryAllocation::class)
            ->whereHas('category', fn ($q) => $q->where('is_salary', true));
    }

    /**
     * In-month totals: sum of salary_allocations.amount for this month.
     * Each allocation is capped so the month never exceeds expected_salary
     * (enforced at write time by the importer / controller).
     */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->salaryAllocations()->sum('amount');
    }

    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->expected_salary - $this->total_paid);
    }

    public function getStatusAttribute(): string
    {
        $paid = $this->total_paid;
        $expected = (float) $this->expected_salary;

        if ($paid <= 0) {
            return 'unpaid';
        }

        if ($paid >= $expected) {
            // Equality is the normal "paid" state; slight float rounding can
            // tip it just above. Treat anything within 0.005 as exact.
            return ($paid - $expected) > 0.005 ? 'overpaid' : 'paid';
        }

        return 'partial';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'bg-success',
            'partial' => 'bg-warning text-dark',
            'overpaid' => 'bg-info',
            default => 'bg-secondary',
        };
    }

    public function getProgressPercentAttribute(): int
    {
        $expected = (float) $this->expected_salary;
        if ($expected <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->total_paid / $expected) * 100));
    }

    public function getLabelAttribute(): string
    {
        [$year, $month] = explode('-', $this->month_key);

        return date('F Y', mktime(0, 0, 0, (int) $month, 1, (int) $year));
    }

    /**
     * Cumulative-rollover view: this month + all earlier months, FIFO.
     */
    public function getCumulativeDueAttribute(): float
    {
        return (float) static::where('month_key', '<=', $this->month_key)
            ->sum('expected_salary');
    }

    public function getCumulativePaidAttribute(): float
    {
        $endOfMonth = substr($this->month_key, 0, 4).'-'.substr($this->month_key, 5, 2).'-31 23:59:59';

        return (float) DB::table('salary_allocations')
            ->join('transactions', 'salary_allocations.transaction_id', '=', 'transactions.id')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('categories.is_salary', true)
            ->where('transactions.paid_at', '<=', $endOfMonth)
            ->sum('salary_allocations.amount');
    }

    public function getCumulativeRemainingAttribute(): float
    {
        return max(0, $this->cumulative_due - $this->cumulative_paid);
    }

    public function getCumulativeStatusAttribute(): string
    {
        $due = $this->cumulative_due;
        $paid = $this->cumulative_paid;

        if ($paid <= 0) {
            return 'unpaid';
        }

        if ($paid >= $due) {
            return ($paid - $due) > 0.005 ? 'overpaid' : 'paid';
        }

        return 'partial';
    }

    public function getCumulativeProgressPercentAttribute(): int
    {
        if ($this->cumulative_due <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->cumulative_paid / $this->cumulative_due) * 100));
    }
}
