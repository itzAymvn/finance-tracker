<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function allocations(): HasMany
    {
        return $this->hasMany(PayoutAllocation::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->allocations->sum('amount');
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
            return $paid > $expected ? 'overpaid' : 'paid';
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
}
