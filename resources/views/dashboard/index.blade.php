@extends('layouts.app')
@section('title', 'Overview')
@section('content')

<div class="page-header">
    <div>
        <h1>Overview</h1>
        <p>Salary balance and month-by-month progress</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('transactions.index') }}" class="btn-secondary">View Transactions</a>
        <a href="{{ route('transactions.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Transaction
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-gradient-to-br from-surface-sidebar to-slate-800 rounded-xl text-white p-6 shadow-sm relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-primary/10 blur-3xl pointer-events-none"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-semibold tracking-wider uppercase text-slate-400">Current Balance</p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $currentBalance >= 0 ? 'bg-emerald/20 text-emerald-300' : 'bg-ruby/20 text-ruby-300' }}">{{ $currentBalance >= 0 ? 'Healthy' : 'Negative' }}</span>
            </div>
            <p class="font-mono text-4xl lg:text-5xl font-light tracking-tight mb-2">{{ number_format(abs($currentBalance), 2) }}</p>
            <p class="text-sm text-slate-400">Updated {{ now()->format('j M Y, H:i') }}</p>
        </div>
    </div>

    <div class="stat-card flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400">Salary Progress</p>
            <span class="text-xs font-mono text-ink-soft dark:text-slate-400">{{ $totalPaid >= $totalExpected ? 'On track' : 'Behind' }}</span>
        </div>
        <div class="flex items-baseline gap-1 mb-4">
            <span class="font-mono text-2xl font-semibold text-emerald dark:text-emerald-400">{{ number_format($totalPaid, 0) }}</span>
            <span class="text-ink-soft dark:text-slate-400">/</span>
            <span class="font-mono text-xl text-ink-soft dark:text-slate-400">{{ number_format($totalExpected, 0) }}</span>
        </div>
        <p class="text-xs text-ink-soft dark:text-slate-400 mb-4">{{ number_format($totalRemaining, 2) }} MAD remaining across {{ $months->count() }} month(s)</p>
        @php $pct = $totalExpected > 0 ? (int) round(($totalPaid / $totalExpected) * 100) : 0; @endphp
        <div class="mt-auto">
            <div class="flex items-center justify-between text-xs font-mono text-ink-soft dark:text-slate-400 mb-1.5">
                <span>{{ $pct }}% funded</span>
                <span>{{ 100 - min(100, $pct) }}% to go</span>
            </div>
            <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald to-primary transition-all duration-700" style="width:{{ min(100, $pct) }}%"></div>
            </div>
        </div>
    </div>
</div>

<div class="stat-card flex items-center justify-between gap-4 flex-wrap mb-8">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary dark:text-primary-400 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <div>
            <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400">Through {{ $toDateLabel }}</p>
            <p class="text-sm text-ink-soft dark:text-slate-400">Salary status up to and including the current month</p>
        </div>
    </div>
    <div class="flex items-center gap-6 font-mono text-sm">
        <div class="text-right">
            <p class="text-xs uppercase tracking-wider text-ink-soft dark:text-slate-400">Paid</p>
            <p class="text-emerald dark:text-emerald-400 font-semibold">{{ number_format($toDatePaid, 2) }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs uppercase tracking-wider text-ink-soft dark:text-slate-400">Expected</p>
            <p class="text-ink dark:text-white font-semibold">{{ number_format($toDateExpected, 2) }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs uppercase tracking-wider text-ink-soft dark:text-slate-400">Remaining</p>
            <p class="{{ $toDateRemaining > 0 ? 'text-ruby dark:text-ruby-400' : 'text-ink-soft dark:text-slate-400' }} font-semibold">{{ number_format($toDateRemaining, 2) }}</p>
        </div>
    </div>
</div>

<div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm overflow-hidden">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 px-6 py-4 border-b border-border dark:border-border-dark">
        <div>
            <h2 class="text-lg font-semibold text-ink dark:text-white">Salary Months</h2>
            <p class="text-sm text-ink-soft dark:text-slate-400 mt-0.5">Each month fills FIFO from tagged salary credits</p>
        </div>
        <form class="flex flex-wrap items-center gap-2" method="GET" action="{{ route('dashboard') }}">
            <select name="status" class="input-field w-auto text-xs py-1.5">
                <option value="">All statuses</option>
                <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>Paid</option>
                <option value="partial"  {{ request('status') === 'partial'  ? 'selected' : '' }}>Partial</option>
                <option value="unpaid"   {{ request('status') === 'unpaid'   ? 'selected' : '' }}>Unpaid</option>
                <option value="overpaid" {{ request('status') === 'overpaid' ? 'selected' : '' }}>Overpaid</option>
            </select>
            <select name="year" class="input-field w-auto text-xs py-1.5">
                <option value="">All years</option>
                @foreach ($years ?? [] as $y)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <input name="from" type="month" value="{{ request('from') }}" title="From month" class="input-field w-auto text-xs py-1.5 font-mono">
            <span class="text-ink-soft dark:text-slate-400 text-xs">–</span>
            <input name="to" type="month" value="{{ request('to') }}" title="To month" class="input-field w-auto text-xs py-1.5 font-mono">
            <button type="submit" class="btn-primary text-xs py-1.5 px-3">Apply</button>
            @if (request()->hasAny(['status', 'year', 'from', 'to']))
                <a href="{{ route('dashboard') }}" class="btn-ghost text-xs py-1.5 px-3">Clear</a>
            @endif
        </form>
    </div>

    @if ($months->isEmpty())
        <div class="text-center py-16 px-6">
            <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-2xl opacity-50">&#x1F4C5;</div>
            <p class="text-sm text-ink-soft dark:text-slate-400 mb-4">No salary months yet.</p>
            <a href="{{ route('salary-months.create') }}" class="btn-primary">Create first month</a>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Expected</th>
                    <th class="text-right">Paid</th>
                    <th class="w-56">Progress</th>
                    <th class="text-center">Status</th>
                    <th class="text-right"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($months as $month)
                    @php $s = $month->status; $cs = $month->cumulative_status; @endphp
                    <tr>
                        <td>
                            <a href="{{ route('salary-months.show', $month) }}" class="font-medium text-ink dark:text-white hover:text-primary no-underline">{{ $month->label }}</a>
                        </td>
                        <td class="text-right">
                            <span class="font-mono font-medium text-ink dark:text-white">{{ number_format($month->expected_salary, 0) }}</span>
                            <span class="text-xs text-ink-soft dark:text-slate-400 ml-1 uppercase">{{ $month->currency }}</span>
                        </td>
                        <td class="text-right font-mono font-semibold {{ $month->total_paid > 0 ? 'text-emerald dark:text-emerald-400' : 'text-ink-soft dark:text-slate-400' }}">{{ number_format($month->total_paid, 0) }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 {{ $s === 'paid' || $s === 'overpaid' ? 'bg-emerald' : ($month->progress_percent >= 50 ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600') }}" style="width:{{ min($month->progress_percent, 100) }}%"></div>
                                </div>
                                <span class="font-mono text-xs text-ink-soft dark:text-slate-400 w-8 text-right">{{ $month->progress_percent }}%</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="{{ $s === 'paid' ? 'badge-emerald' : '' }}{{ $s === 'overpaid' ? 'badge-sapphire' : '' }}{{ $s === 'partial' ? 'badge-amber' : '' }}{{ $s === 'unpaid' ? 'badge-slate' : '' }}">{{ ucfirst($s) }}</span>
                                @if ($cs !== $s)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium border {{ $cs === 'paid' ? 'border-emerald/30 text-emerald dark:text-emerald-400' : '' }}{{ $cs === 'overpaid' ? 'border-sapphire/30 text-sapphire' : '' }}{{ $cs === 'partial' ? 'border-primary/30 text-primary' : '' }}{{ $cs === 'unpaid' ? 'border-border dark:border-border-dark text-ink-soft dark:text-slate-400' : '' }}"
                                      title="Cumulative through {{ $month->month_key }}: {{ number_format($month->cumulative_paid, 2) }} / {{ number_format($month->cumulative_due, 2) }} (FIFO rollover)">cum: {{ ucfirst($cs) }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('salary-months.show', $month) }}" class="btn-ghost text-xs py-1.5 px-3">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
