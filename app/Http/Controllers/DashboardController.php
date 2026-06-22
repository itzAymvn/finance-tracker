<?php

namespace App\Http\Controllers;

use App\Models\SalaryMonth;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

        $years = SalaryMonth::selectRaw("substr(month_key, 1, 4) as y")
            ->distinct()
            ->orderBy('y', 'desc')
            ->pluck('y');

        $toDateLabel = Carbon::parse($currentMonthKey.'-01')->translatedFormat('M Y');

        $currentBalance = (float) Transaction::sum('amount');

        return Inertia::render('Dashboard/Index', [
            'months' => $months->map(fn ($m) => array_merge($m->toArray(), [
                'label' => $m->label,
                'total_paid' => $m->total_paid,
                'remaining' => $m->remaining,
                'status' => $m->status,
                'progress_percent' => $m->progress_percent,
                'cumulative_paid' => $m->cumulative_paid,
                'cumulative_due' => $m->cumulative_due,
                'cumulative_remaining' => $m->cumulative_remaining,
                'cumulative_status' => $m->cumulative_status,
                'cumulative_progress_percent' => $m->cumulative_progress_percent,
            ]))->values()->toArray(),
            'totalExpected' => $totalExpected,
            'totalPaid' => $totalPaid,
            'totalRemaining' => $totalRemaining,
            'currentMonthKey' => $currentMonthKey,
            'toDateExpected' => $toDateExpected,
            'toDatePaid' => $toDatePaid,
            'toDateRemaining' => $toDateRemaining,
            'toDateLabel' => $toDateLabel,
            'years' => $years,
            'currentBalance' => $currentBalance,
        ]);
    }
}
