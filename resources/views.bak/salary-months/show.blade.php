@extends('layouts.app')
@section('title', $salaryMonth->label)
@section('content')

<div class="page-header">
    <div>
        <h1>{{ $salaryMonth->label }}</h1>
        <p>Salary month details and progress</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('salary-months.edit', $salaryMonth) }}" class="btn-secondary">Edit</a>
        <a href="{{ route('dashboard') }}" class="btn-ghost">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Dashboard
        </a>
    </div>
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="stat-card relative overflow-hidden">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Expected</p>
        <p class="font-mono text-xl font-medium text-ink dark:text-white">{{ number_format($salaryMonth->expected_salary, 2) }}</p>
        <p class="text-xs text-ink-soft dark:text-slate-400 mt-0.5">{{ $salaryMonth->currency }}</p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-border dark:bg-border-dark"></div>
    </div>
    <div class="stat-card relative overflow-hidden">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Paid (FIFO)</p>
        <p class="font-mono text-xl font-medium text-emerald dark:text-emerald-400">{{ number_format($salaryMonth->total_paid, 2) }}</p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-emerald"></div>
    </div>
    <div class="stat-card relative overflow-hidden">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Remaining</p>
        <p class="font-mono text-xl font-medium {{ $salaryMonth->remaining > 0 ? 'text-ruby dark:text-ruby-400' : 'text-ink-soft dark:text-slate-400' }}">{{ number_format($salaryMonth->remaining, 2) }}</p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] {{ $salaryMonth->remaining > 0 ? 'bg-ruby' : 'bg-emerald' }}"></div>
    </div>
    <div class="stat-card relative overflow-hidden">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Status</p>
        @php $s = $salaryMonth->status; @endphp
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold tracking-wider uppercase
            {{ $s === 'paid'     ? 'bg-emerald-lt text-emerald dark:bg-emerald-dark-bg dark:text-emerald-300' : '' }}
            {{ $s === 'overpaid' ? 'bg-sapphire-lt text-sapphire dark:bg-sapphire-dark-bg dark:text-blue-300' : '' }}
            {{ $s === 'partial'  ? 'bg-amber-lt text-amber dark:bg-amber-dark-bg dark:text-amber-300' : '' }}
            {{ $s === 'unpaid'   ? 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' : '' }}">
            {{ ucfirst($s) }}
        </span>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] {{ $s === 'paid' ? 'bg-emerald' : '' }} {{ $s === 'overpaid' ? 'bg-sapphire' : '' }} {{ $s === 'partial' ? 'bg-amber' : '' }} {{ $s === 'unpaid' ? 'bg-border dark:bg-border-dark' : '' }}"></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400">Cumulative (FIFO)</p>
            <span class="font-mono text-sm font-medium text-ink-soft dark:text-slate-400">
                {{ number_format($salaryMonth->cumulative_paid, 2) }} / {{ number_format($salaryMonth->cumulative_due, 2) }}
                &middot; {{ $salaryMonth->cumulative_progress_percent }}%
            </span>
        </div>
        <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-700 bg-sapphire dark:bg-blue-500"
                 style="width:{{ min($salaryMonth->cumulative_progress_percent, 100) }}%"></div>
        </div>
        <p class="text-xs text-ink-soft dark:text-slate-400 mt-2">
            @php $cs = $salaryMonth->cumulative_status; @endphp
            Cumulative status through {{ $salaryMonth->month_key }}: <span class="font-semibold">{{ ucfirst($cs) }}</span>
        </p>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400">In-month Progress</p>
            <span class="font-mono text-sm font-medium text-ink-soft dark:text-slate-400">{{ $salaryMonth->progress_percent }}%</span>
        </div>
        <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-700
                        {{ $salaryMonth->status === 'paid' || $salaryMonth->status === 'overpaid' ? 'bg-emerald dark:bg-emerald-500' : ($salaryMonth->progress_percent >= 50 ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600') }}"
                 style="width:{{ min($salaryMonth->progress_percent, 100) }}%"></div>
        </div>
    </div>
</div>

@if($salaryMonth->notes)
<div class="stat-card mb-6">
    <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Notes</p>
    <p class="text-sm text-ink dark:text-white leading-relaxed">{{ $salaryMonth->notes }}</p>
</div>
@endif

<div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-border dark:border-border-dark">
        <h2 class="text-lg font-semibold text-ink dark:text-white">Salary Credits Allocated Here</h2>
        <a href="{{ route('transactions.index', ['type' => 'salary']) }}" class="text-sm text-ink-soft dark:text-slate-400 hover:text-primary transition-colors">All salary &rarr;</a>
    </div>

    @php
        $allocations = $salaryMonth->salaryAllocations()->with('transaction')->orderByDesc(
            \App\Models\Transaction::select('paid_at')->whereColumn('id', 'salary_allocations.transaction_id')->limit(1)
        )->get();
    @endphp

    @if($allocations->isEmpty())
    <div class="text-center py-12 px-6">
        <div class="text-2xl mb-3 opacity-30">&#x1F4ED;</div>
        <p class="text-sm text-ink-soft dark:text-slate-400">No salary allocations to this month.</p>
        <p class="text-xs text-ink-soft dark:text-slate-400 mt-1">
            Tag transactions via the <a href="{{ route('transactions.index') }}" class="text-primary hover:underline">Transactions</a> page.
        </p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Label</th>
                    <th class="text-right">Allocated</th>
                    <th class="text-right">Tx Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($allocations as $allocation)
                <tr>
                    <td>{{ $allocation->transaction->paid_at->format('d M Y') }}</td>
                    <td class="max-w-[400px] truncate" title="{{ $allocation->transaction->label }}">{{ $allocation->transaction->label }}</td>
                    <td class="text-right font-mono font-semibold text-emerald dark:text-emerald-400">
                        {{ number_format($allocation->amount, 2) }}
                        <span class="text-xs text-ink-soft dark:text-slate-400 ml-1">{{ $salaryMonth->currency }}</span>
                    </td>
                    <td class="text-right font-mono text-ink-soft dark:text-slate-400">{{ number_format($allocation->transaction->amount, 2) }}</td>
                    <td class="text-right">
                        <a href="{{ route('transactions.edit', $allocation->transaction) }}" class="btn-ghost text-xs py-1.5 px-3">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
