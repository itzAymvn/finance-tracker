<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'paid_at',
        'value_date',
        'label',
        'amount',
        'source',
        'category_id',
        'salary_month_id',
        'subscription_id',
        'raw',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'value_date' => 'datetime',
        'amount' => 'decimal:2',
        'raw' => 'array',
    ];

    public function salaryMonth(): BelongsTo
    {
        return $this->belongsTo(SalaryMonth::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(SalaryAllocation::class)->with('salaryMonth');
    }

    public function scopeCredits(Builder $q): Builder
    {
        return $q->where('amount', '>', 0);
    }

    public function scopeDebits(Builder $q): Builder
    {
        return $q->where('amount', '<', 0);
    }

    public function scopeSalary(Builder $q): Builder
    {
        return $q->whereHas('category', fn ($cq) => $cq->where('is_salary', true));
    }

    public function isCredit(): bool
    {
        return (float) $this->amount > 0;
    }

    public function isDebit(): bool
    {
        return (float) $this->amount < 0;
    }

    public function isSalary(): bool
    {
        return $this->category?->is_salary === true;
    }

    /**
     * Total amount allocated across all months. For salary credits, equals
     * min(amount, sum of available capacity in eligible months).
     */
    public function getAllocatedTotalAttribute(): float
    {
        return (float) $this->allocations->sum('amount');
    }

    /**
     * For salary credits, the portion of the amount not allocated to any month.
     */
    public function getUnallocatedAttribute(): float
    {
        return max(0, (float) $this->amount - $this->allocated_total);
    }
}
