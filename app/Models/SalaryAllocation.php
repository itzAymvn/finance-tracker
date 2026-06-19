<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SalaryAllocation extends Pivot
{
    protected $table = 'salary_allocations';

    protected $fillable = [
        'transaction_id',
        'salary_month_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function salaryMonth()
    {
        return $this->belongsTo(SalaryMonth::class);
    }
}
