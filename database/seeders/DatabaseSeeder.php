<?php

namespace Database\Seeders;

use App\Models\SalaryMonth;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);

        // Salary months: 2024-11 -> 2025-03 @ 3000, 2025-04 -> 2026-12 @ 4000.
        // Start at 2024-11 because that is the first month covered by the bank export.
        foreach (['2024-11', '2024-12', '2025-01', '2025-02', '2025-03'] as $key) {
            SalaryMonth::firstOrCreate(
                ['month_key' => $key],
                ['expected_salary' => 3000, 'currency' => 'MAD']
            );
        }

        for ($y = 2025; $y <= 2026; $y++) {
            $start = $y === 2025 ? 4 : 1;
            for ($m = $start; $m <= 12; $m++) {
                $key = sprintf('%04d-%02d', $y, $m);
                SalaryMonth::firstOrCreate(
                    ['month_key' => $key],
                    ['expected_salary' => 4000, 'currency' => 'MAD']
                );
            }
        }
    }
}
