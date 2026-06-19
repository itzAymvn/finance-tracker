<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payout_allocations');
        Schema::dropIfExists('payouts');
    }

    public function down(): void
    {
        // No rollback: payouts feature is intentionally removed.
    }
};
