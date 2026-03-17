<?php

namespace Database\Seeders;

use App\Models\Payout;
use App\Models\PayoutAllocation;
use App\Models\SalaryMonth;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);

        // 1. Salary months: 07/2024 -> 03/2025 (3000), 04/2025 -> 12/2026 (4000)
        $months = [];

        foreach (['2024-07', '2024-08', '2024-09', '2024-10', '2024-11', '2024-12', '2025-01', '2025-02', '2025-03'] as $key) {
            $months[$key] = SalaryMonth::create([
                'month_key' => $key,
                'expected_salary' => 3000,
                'currency' => 'MAD',
            ]);
        }

        for ($y = 2025; $y <= 2026; $y++) {
            $start = $y === 2025 ? 4 : 1;
            $end = $y === 2026 ? 12 : 12;
            for ($m = $start; $m <= $end; $m++) {
                $key = sprintf('%04d-%02d', $y, $m);
                $months[$key] = SalaryMonth::create([
                    'month_key' => $key,
                    'expected_salary' => 4000,
                    'currency' => 'MAD',
                ]);
            }
        }

        // 2. Full payouts from 07/2024 to 11/2025 (each month paid in full)
        $fullPayoutMonths = [
            '2024-07' => 3000, '2024-08' => 3000, '2024-09' => 3000, '2024-10' => 3000,
            '2024-11' => 3000, '2024-12' => 3000, '2025-01' => 3000, '2025-02' => 3000,
            '2025-03' => 3000, '2025-04' => 4000, '2025-05' => 4000, '2025-06' => 4000,
            '2025-07' => 4000, '2025-08' => 4000, '2025-09' => 4000, '2025-10' => 4000,
            '2025-11' => 4000,
        ];

        foreach ($fullPayoutMonths as $monthKey => $amount) {
            [$year, $month] = explode('-', $monthKey);
            $paidAt = sprintf('%04d-%02d-25 12:00:00', (int) $year, (int) $month);
            $payout = Payout::create(['paid_at' => $paidAt, 'amount' => $amount]);
            PayoutAllocation::create([
                'payout_id' => $payout->id,
                'salary_month_id' => $months[$monthKey]->id,
                'amount' => $amount,
            ]);
        }

        // 3. +3000 for 12/2025 (partial)
        $payout = Payout::create(['paid_at' => '2025-12-25 12:00:00', 'amount' => 3000]);
        PayoutAllocation::create([
            'payout_id' => $payout->id,
            'salary_month_id' => $months['2025-12']->id,
            'amount' => 3000,
        ]);
    }
}
