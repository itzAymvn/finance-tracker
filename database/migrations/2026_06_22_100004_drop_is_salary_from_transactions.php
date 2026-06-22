<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('transactions', 'is_salary')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropIndex(['is_salary']);
            });
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('is_salary');
            });
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_salary')->default(false)->after('source');
            $table->index('is_salary');
        });
    }
};
