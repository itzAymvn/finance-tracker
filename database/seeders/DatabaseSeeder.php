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
        // Salary months
        $oct = SalaryMonth::create([
            'month_key' => '2025-10',
            'expected_salary' => 4000,
            'currency' => 'MAD',
        ]);

        $nov = SalaryMonth::create([
            'month_key' => '2025-11',
            'expected_salary' => 4000,
            'currency' => 'MAD',
        ]);

        $dec = SalaryMonth::create([
            'month_key' => '2025-12',
            'expected_salary' => 4000,
            'currency' => 'MAD',
        ]);

        // Payout 1: 2025-12-24 → 500 → all to October
        $p1 = Payout::create(['paid_at' => '2025-12-24 00:00:00', 'amount' => 500]);
        PayoutAllocation::create(['payout_id' => $p1->id, 'salary_month_id' => $oct->id, 'amount' => 500]);

        // Payout 2: 2026-01-06 → 2000 → all to October (Oct remaining: 4000-500=3500, use 2000)
        $p2 = Payout::create(['paid_at' => '2026-01-06 00:00:00', 'amount' => 2000]);
        PayoutAllocation::create(['payout_id' => $p2->id, 'salary_month_id' => $oct->id, 'amount' => 2000]);

        // Payout 3: 2026-01-19 → 2000 → 1500 closes October, 500 starts November
        // Oct remaining: 4000-500-2000 = 1500
        $p3 = Payout::create(['paid_at' => '2026-01-19 00:00:00', 'amount' => 2000]);
        PayoutAllocation::create(['payout_id' => $p3->id, 'salary_month_id' => $oct->id, 'amount' => 1500]);
        PayoutAllocation::create(['payout_id' => $p3->id, 'salary_month_id' => $nov->id, 'amount' => 500]);

        // Payout 4: 2026-02-13 → 2000 → all to November (Nov remaining: 4000-500=3500, use 2000)
        $p4 = Payout::create([
            'paid_at' => '2026-02-13 00:00:00',
            'amount' => 2000,
            'note' => 'Includes 500 MAD for AI Tokens subscription.',
        ]);
        PayoutAllocation::create(['payout_id' => $p4->id, 'salary_month_id' => $nov->id, 'amount' => 2000]);

        // Payout 5: 2026-03-01 → 2000 → 1500 closes November, 500 starts December
        // Nov remaining: 4000-500-2000 = 1500
        $p5 = Payout::create(['paid_at' => '2026-03-01 00:00:00', 'amount' => 2000]);
        PayoutAllocation::create(['payout_id' => $p5->id, 'salary_month_id' => $nov->id, 'amount' => 1500]);
        PayoutAllocation::create(['payout_id' => $p5->id, 'salary_month_id' => $dec->id, 'amount' => 500]);

        // Final state:
        // October  2025: 500+2000+1500 = 4000 → PAID
        // November 2025: 500+2000+1500 = 4000 → PAID
        // December 2025: 500           = 500  → PARTIAL (3500 remaining)
    }
}
