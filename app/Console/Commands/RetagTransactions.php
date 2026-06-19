<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Services\AllocationService;
use Illuminate\Console\Command;

class RetagTransactions extends Command
{
    protected $signature = 'transactions:retag';

    protected $description = 'Re-evaluate every credit transaction against the salary sender patterns and re-tag/reallocate as needed. Useful after editing patterns in ImportTransactions or when the XLSX used abbreviated labels.';

    public function __construct(private readonly AllocationService $allocationService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        // Mirror ImportTransactions::SALARY_PATTERNS. Kept in sync manually so
        // the canonical list lives in one place.
        $reflection = new \ReflectionClass(ImportTransactions::class);
        $patterns = $reflection->getConstant('SALARY_PATTERNS');

        $tagged = 0;
        $untagged = 0;
        $unchanged = 0;
        $credits = Transaction::credits()->orderBy('paid_at')->get();

        foreach ($credits as $tx) {
            $shouldBeSalary = $this->matchesAny($tx->label, $patterns);

            if ($shouldBeSalary === $tx->is_salary) {
                $unchanged++;
                continue;
            }

            $tx->is_salary = $shouldBeSalary;
            $tx->save();

            $this->allocationService->reallocate($tx);

            if ($shouldBeSalary) {
                $tagged++;
                $this->line("  + tagged {$tx->paid_at->format('Y-m-d')} {$tx->amount}  {$tx->label}");
            } else {
                $untagged++;
                $this->line("  - untagged {$tx->paid_at->format('Y-m-d')} {$tx->amount}  {$tx->label}");
            }
        }

        $this->info("Credits scanned:    {$credits->count()}");
        $this->info("Newly tagged:       {$tagged}");
        $this->info("Newly untagged:     {$untagged}");
        $this->info("Unchanged:          {$unchanged}");

        return self::SUCCESS;
    }

    private function matchesAny(string $label, array $patterns): bool
    {
        $upper = mb_strtoupper($label);
        foreach ($patterns as $pattern) {
            if (str_contains($upper, mb_strtoupper($pattern))) {
                return true;
            }
        }

        return false;
    }
}
