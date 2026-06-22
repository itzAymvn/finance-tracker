<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'icon', 'is_salary'];

    protected $casts = [
        'is_salary' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTransactionCountAttribute(): int
    {
        return $this->transactions()->count();
    }
}
