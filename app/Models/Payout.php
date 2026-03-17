<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payout extends Model
{
    protected $fillable = [
        'paid_at',
        'amount',
        'note',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function allocations(): HasMany
    {
        return $this->hasMany(PayoutAllocation::class)->with('salaryMonth');
    }

    public function getAllocatedTotalAttribute(): float
    {
        return (float) $this->allocations->sum('amount');
    }

    public function getUnallocatedAttribute(): float
    {
        return max(0, (float) $this->amount - $this->allocated_total);
    }

    public function hasAttachment(): bool
    {
        return ! empty($this->attachment_path);
    }
}
