<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('paid_at');
            $table->dateTime('value_date')->nullable();
            $table->string('label');
            $table->decimal('amount', 10, 2)->comment('Signed: +credit / -debit');
            $table->string('source', 20)->default('manuel');
            $table->boolean('is_salary')->default(false);
            $table->foreignId('salary_month_id')->nullable()->constrained()->nullOnDelete();
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->unique(['paid_at', 'amount', 'label'], 'transactions_dedup');
            $table->index('paid_at');
            $table->index('is_salary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
