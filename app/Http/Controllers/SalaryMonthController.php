<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalaryMonthPeriodRequest;
use App\Http\Requests\StoreSalaryMonthRequest;
use App\Http\Requests\UpdateSalaryMonthRequest;
use App\Models\SalaryMonth;
use Inertia\Inertia;

class SalaryMonthController extends Controller
{
    public function create()
    {
        return Inertia::render('SalaryMonths/Create');
    }

    public function store(StoreSalaryMonthRequest $request)
    {
        SalaryMonth::create($request->validated());

        return redirect()->route('dashboard')->with('success', 'Salary month created.');
    }

    public function storePeriod(StoreSalaryMonthPeriodRequest $request)
    {
        $data = $request->validated();
        $current = $data['from_month'];
        $end = $data['to_month'];
        $created = 0;
        $skipped = 0;

        while ($current <= $end) {
            $exists = SalaryMonth::where('month_key', $current)->exists();

            if ($exists) {
                $skipped++;
            } else {
                SalaryMonth::create([
                    'month_key' => $current,
                    'expected_salary' => $data['expected_salary'],
                    'currency' => $data['currency'],
                    'notes' => $data['notes'] ?? null,
                ]);
                $created++;
            }

            [$year, $month] = explode('-', $current);
            $month++;
            if ($month > 12) {
                $month = 1;
                $year++;
            }
            $current = sprintf('%04d-%02d', $year, $month);
        }

        $message = "{$created} month(s) created.";
        if ($skipped > 0) {
            $message .= " {$skipped} skipped (already existed).";
        }

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function show(SalaryMonth $salaryMonth)
    {
        $salaryMonth->load(['salaryTransactions' => function ($q) {
            $q->orderBy('paid_at', 'desc');
        }]);

        $allocations = $salaryMonth->salaryAllocations()
            ->with('transaction')
            ->orderByDesc(\App\Models\Transaction::select('paid_at')->whereColumn('id', 'salary_allocations.transaction_id'))
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'transaction_id' => $a->transaction_id,
                'salary_month_id' => $a->salary_month_id,
                'amount' => $a->amount,
                'transaction' => $a->transaction ? [
                    'id' => $a->transaction->id,
                    'paid_at' => $a->transaction->paid_at->toIso8601String(),
                    'label' => $a->transaction->label,
                    'amount' => $a->transaction->amount,
                ] : null,
            ])->toArray();

        return Inertia::render('SalaryMonths/Show', [
            'salaryMonth' => array_merge($salaryMonth->toArray(), [
                'label' => $salaryMonth->label,
                'total_paid' => $salaryMonth->total_paid,
                'remaining' => $salaryMonth->remaining,
                'status' => $salaryMonth->status,
                'progress_percent' => $salaryMonth->progress_percent,
                'cumulative_paid' => $salaryMonth->cumulative_paid,
                'cumulative_due' => $salaryMonth->cumulative_due,
                'cumulative_remaining' => $salaryMonth->cumulative_remaining,
                'cumulative_status' => $salaryMonth->cumulative_status,
                'cumulative_progress_percent' => $salaryMonth->cumulative_progress_percent,
            ]),
            'allocations' => $allocations,
        ]);
    }

    public function edit(SalaryMonth $salaryMonth)
    {
        return Inertia::render('SalaryMonths/Edit', ['salaryMonth' => array_merge($salaryMonth->toArray(), ['label' => $salaryMonth->label])]);
    }

    public function update(UpdateSalaryMonthRequest $request, SalaryMonth $salaryMonth)
    {
        $salaryMonth->update($request->validated());

        return redirect()->route('salary-months.show', $salaryMonth)->with('success', 'Salary month updated.');
    }

    public function destroy(SalaryMonth $salaryMonth)
    {
        $salaryMonth->delete();

        return redirect()->route('dashboard')->with('success', 'Salary month deleted.');
    }
}
