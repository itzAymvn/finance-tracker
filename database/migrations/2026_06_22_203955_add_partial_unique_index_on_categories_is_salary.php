<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS categories_is_salary_unique ON categories (is_salary) WHERE is_salary = 1');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS categories_is_salary_unique');
    }
};
