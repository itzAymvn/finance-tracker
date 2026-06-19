<?php

namespace App\Http\Controllers;

use App\Models\SalaryMonth;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = SalaryMonth::orderBy('month_key', 'desc');

        if ($year = $request->query('year')) {
            $query->where('month_key', 'like', $year.'-%');
        }

        if ($from = $request->query('from')) {
            $query->where('month_key', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->where('month_key', '<=', $to);
        }

        $months = $query->get();

        if ($status = $request->query('status')) {
            $months = $months->filter(fn ($m) => $m->status === $status)->values();
        }

        $totalExpected = $months->sum(fn ($m) => (float) $m->expected_salary);
        $totalPaid = $months->sum(fn ($m) => $m->total_paid);
        $totalRemaining = $months->sum(fn ($m) => $m->remaining);

        $now = now();
        $currentMonthKey = ($now->day === $now->daysInMonth)
            ? $now->format('Y-m')
            : $now->copy()->subMonth()->format('Y-m');
        $monthsToDate = $months->filter(fn ($m) => $m->month_key <= $currentMonthKey);
        $toDateExpected = $monthsToDate->sum(fn ($m) => (float) $m->expected_salary);
        $toDatePaid = $monthsToDate->sum(fn ($m) => $m->total_paid);
        $toDateRemaining = $monthsToDate->sum(fn ($m) => $m->remaining);

        $years = SalaryMonth::selectRaw('SUBSTRING(month_key, 1, 4) as y')
            ->distinct()
            ->orderBy('y', 'desc')
            ->pluck('y');

        $toDateLabel = Carbon::parse($currentMonthKey.'-01')->translatedFormat('M Y');

        $currentBalance = (float) Transaction::sum('amount');

        return view('dashboard.index', compact(
            'months', 'totalExpected', 'totalPaid', 'totalRemaining',
            'currentMonthKey', 'toDateExpected', 'toDatePaid', 'toDateRemaining',
            'toDateLabel', 'years', 'currentBalance'
        ));
    }
}
