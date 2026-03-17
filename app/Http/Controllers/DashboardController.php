<?php

namespace App\Http\Controllers;

use App\Models\SalaryMonth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = SalaryMonth::orderBy('month_key', 'desc')
            ->with('allocations');

        // Filter by year
        if ($year = $request->query('year')) {
            $query->where('month_key', 'like', $year.'-%');
        }

        // Filter by month range
        if ($from = $request->query('from')) {
            $query->where('month_key', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->where('month_key', '<=', $to);
        }

        $months = $query->get();

        // Filter by status (computed attribute)
        if ($status = $request->query('status')) {
            $months = $months->filter(fn ($m) => $m->status === $status)->values();
        }

        $totalExpected = $months->sum(fn ($m) => (float) $m->expected_salary);
        $totalPaid = $months->sum(fn ($m) => $m->total_paid);
        $totalRemaining = $months->sum(fn ($m) => $m->remaining);

        $currentMonthKey = now()->format('Y-m');
        $monthsToDate = $months->filter(fn ($m) => $m->month_key <= $currentMonthKey);
        $toDateExpected = $monthsToDate->sum(fn ($m) => (float) $m->expected_salary);
        $toDatePaid = $monthsToDate->sum(fn ($m) => $m->total_paid);
        $toDateRemaining = $monthsToDate->sum(fn ($m) => $m->remaining);

        $years = SalaryMonth::selectRaw('SUBSTRING(month_key, 1, 4) as y')
            ->distinct()
            ->orderBy('y', 'desc')
            ->pluck('y');

        return view('dashboard.index', compact(
            'months', 'totalExpected', 'totalPaid', 'totalRemaining',
            'currentMonthKey', 'toDateExpected', 'toDatePaid', 'toDateRemaining',
            'years'
        ));
    }
}
