<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Transaction;
use App\Services\AllocationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;

class ImportTransactions extends Command
{
    protected $signature = 'transactions:import {file : Path to the .xlsx file}';

    protected $description = 'Import bank transactions from an .xlsx export. Salary credits matching known senders are assigned the salary category and split FIFO across salary months (each month capped at expected_salary, surplus rolls forward).';

    private const SALARY_PATTERNS = [
        'VIREMENT RECU DE MUSTAPHA JAAFARY',
        'VIREMENT RECU DE MUSTAPHA J.',
        'VIREMENT RECU DE MOHAMED BARRAH',
    ];

    private const SKIP_LABELS = ['SOLDE COURANT'];

    public function __construct(private readonly AllocationService $allocationService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $file = (string) $this->argument('file');

        if (! is_file($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getSheetByName('Sheet1') ?? $spreadsheet->getSheet(0);
        $rows = $sheet->getRowIterator();

        $imported = 0;
        $skipped = 0;
        $invalid = 0;
        $tagged = 0;
        $partial = 0;
        $rowNum = 0;

        DB::transaction(function () use ($rows, &$imported, &$skipped, &$invalid, &$tagged, &$partial, &$rowNum) {
            $salaryCategoryId = Category::where('is_salary', true)->value('id');

            foreach ($rows as $row) {
                $rowNum++;
                if ($rowNum === 1) {
                    continue;
                }

                $cells = array_values(iterator_to_array($row->getCellIterator()));

                $dateRaw = $cells[0]?->getValue();
                $valueDateRaw = $cells[1]?->getValue();
                $label = trim((string) ($cells[2]?->getValue() ?? ''));
                $debit = $this->toNullableFloat($cells[3]?->getValue());
                $credit = $this->toNullableFloat($cells[4]?->getValue());

                if ($this->isSkipLabel($label)) {
                    $skipped++;
                    continue;
                }

                if ($dateRaw === null || $dateRaw === '') {
                    $invalid++;
                    continue;
                }

                if ($debit === null && $credit === null) {
                    $invalid++;
                    continue;
                }

                if ($debit !== null && $credit !== null && $debit > 0 && $credit > 0) {
                    $this->warn("Row {$rowNum}: both debit and credit set, skipping.");
                    $invalid++;
                    continue;
                }

                $amount = $debit !== null ? -abs($debit) : abs((float) $credit);

                if (abs($amount) < 0.005) {
                    $invalid++;
                    continue;
                }

                $paidAt = $this->toCarbon($dateRaw);
                $valueDate = $this->toCarbon($valueDateRaw);

                if ($paidAt === null) {
                    $this->warn("Row {$rowNum}: could not parse date '{$dateRaw}', skipping.");
                    $invalid++;
                    continue;
                }

                $isSalary = $amount > 0 && $this->matchesSalaryPattern($label);

                $tx = Transaction::create([
                    'paid_at' => $paidAt,
                    'value_date' => $valueDate,
                    'label' => $label,
                    'amount' => $amount,
                    'source' => 'relevé',
                    'category_id' => $isSalary ? $salaryCategoryId : null,
                    'salary_month_id' => null,
                    'raw' => [
                        'row' => $rowNum,
                        'debit' => $debit,
                        'credit' => $credit,
                    ],
                ]);

                if ($isSalary) {
                    $tagged++;
                    $this->allocationService->reallocate($tx);
                    if ($tx->unallocated > 0.005) {
                        $partial++;
                        $this->warn("Row {$rowNum}: salary credit {$amount} could not be fully allocated (unallocated: {$tx->unallocated}).");
                    }
                }

                $imported++;
            }
        });

        $this->info("Imported:           {$imported}");
        $this->info("Skipped (footer):   {$skipped}");
        $this->info("Invalid:            {$invalid}");
        $this->info("Tagged salary:      {$tagged}".($partial > 0 ? " ({$partial} partially allocated)" : ''));

        return self::SUCCESS;
    }

    private function toNullableFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (! is_numeric($value)) {
            return null;
        }
        $f = (float) $value;

        return $f == 0 ? null : $f;
    }

    private function toCarbon(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return Carbon::instance(SpreadsheetDate::excelToDateTimeObject((float) $value));
            }

            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function matchesSalaryPattern(string $label): bool
    {
        $upper = mb_strtoupper($label);
        foreach (self::SALARY_PATTERNS as $pattern) {
            if (str_contains($upper, mb_strtoupper($pattern))) {
                return true;
            }
        }

        return false;
    }

    private function isSkipLabel(string $label): bool
    {
        $upper = mb_strtoupper(trim($label));
        foreach (self::SKIP_LABELS as $skip) {
            if ($upper === mb_strtoupper($skip)) {
                return true;
            }
        }

        return false;
    }
}
