<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_months', function (Blueprint $table) {
            $table->id();
            $table->string('month_key', 7)->unique()->comment('Format: YYYY-MM');
            $table->decimal('expected_salary', 10, 2);
            $table->string('currency', 10)->default('MAD');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_months');
    }
};
