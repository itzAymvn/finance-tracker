<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('salary_month_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->unique(['payout_id', 'salary_month_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_allocations');
    }
};
