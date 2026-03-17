<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutAllocation extends Model
{
    protected $fillable = [
        'payout_id',
        'salary_month_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }

    public function salaryMonth(): BelongsTo
    {
        return $this->belongsTo(SalaryMonth::class);
    }
}
