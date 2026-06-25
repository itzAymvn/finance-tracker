<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'label',
        'amount',
        'frequency',
        'start_at',
        'status',
        'category_id',
        'last_generated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_at' => 'datetime',
        'last_generated_at' => 'datetime',
    ];

    const FREQUENCIES = ['weekly', 'biweekly', 'monthly', 'quarterly', 'yearly'];

    const STATUSES = ['active', 'paused', 'cancelled'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function pause(): void
    {
        $this->update(['status' => 'paused']);
    }

    public function resume(): void
    {
        // Advance last_generated_at to now so resumed periods are counted
        // from the resume point rather than back-filling the paused gap.
        $this->update([
            'status' => 'active',
            'last_generated_at' => $this->last_generated_at && $this->last_generated_at->lt(now())
                ? now()
                : $this->last_generated_at,
        ]);
    }

    /**
     * Next due date strictly after the given reference point.
     * Centralised here so generation (service) and display (controller)
     * share the same frequency arithmetic.
     */
    public function nextDueAfter(Carbon $from): ?Carbon
    {
        return match ($this->frequency) {
            'weekly' => $from->copy()->addWeek(),
            'biweekly' => $from->copy()->addWeeks(2),
            'monthly' => $from->copy()->addMonth(),
            'quarterly' => $from->copy()->addMonths(3),
            'yearly' => $from->copy()->addYear(),
            default => null,
        };
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function getNextDueAt(): ?Carbon
    {
        if (! $this->start_at) {
            return null;
        }

        // No transactions generated yet — first one is due at start_at
        if (! $this->last_generated_at) {
            return $this->start_at;
        }

        return $this->nextDueAfter($this->last_generated_at);
    }

    public function isCredit(): bool
    {
        return (float) $this->amount > 0;
    }
}
