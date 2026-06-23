<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('amount', 10, 2);
            $table->string('frequency'); // weekly, biweekly, monthly, quarterly, yearly
            $table->datetime('start_at');
            $table->string('status')->default('active'); // active, paused, cancelled
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->datetime('last_generated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
