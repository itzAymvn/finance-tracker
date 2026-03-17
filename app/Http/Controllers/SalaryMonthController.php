<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalaryMonthPeriodRequest;
use App\Http\Requests\StoreSalaryMonthRequest;
use App\Http\Requests\UpdateSalaryMonthRequest;
use App\Models\SalaryMonth;

class SalaryMonthController extends Controller
{
    public function create()
    {
        return view('salary-months.create');
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

            // Advance by one month
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
        $salaryMonth->load(['allocations.payout']);

        return view('salary-months.show', compact('salaryMonth'));
    }

    public function edit(SalaryMonth $salaryMonth)
    {
        return view('salary-months.edit', compact('salaryMonth'));
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
