@extends('layouts.app')
@section('title', 'Transactions')
@section('content')

<div class="page-header">
    <div>
        <h1>Transactions</h1>
        <p>View and manage all your financial transactions</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('transactions.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Transaction
        </a>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Showing</p>
        <p class="font-mono text-xl font-medium text-ink dark:text-white">{{ number_format($summary['count']) }} <span class="text-xs text-ink-soft dark:text-slate-400 font-normal">tx</span></p>
    </div>
    <div class="stat-card relative overflow-hidden">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Credits</p>
        <p class="font-mono text-xl font-medium text-emerald dark:text-emerald-400">+{{ number_format($summary['credits'], 2) }}</p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-emerald"></div>
    </div>
    <div class="stat-card relative overflow-hidden">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Debits</p>
        <p class="font-mono text-xl font-medium text-ruby dark:text-ruby-400">{{ number_format($summary['debits'], 2) }}</p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-ruby"></div>
    </div>
    <div class="stat-card relative overflow-hidden">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Net</p>
        <p class="font-mono text-xl font-medium {{ $summary['net'] >= 0 ? 'text-emerald dark:text-emerald-400' : 'text-ruby dark:text-ruby-400' }}">
            {{ $summary['net'] >= 0 ? '+' : '' }}{{ number_format($summary['net'], 2) }}
        </p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] {{ $summary['net'] >= 0 ? 'bg-emerald' : 'bg-ruby' }}"></div>
    </div>
</div>

<div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm overflow-hidden">
    <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-wrap items-center gap-2 px-6 py-4 border-b border-border dark:border-border-dark bg-surface-hover/50 dark:bg-surface-dark-hover/50">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-soft/70 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search label..."
                   class="input-field text-xs py-1.5 pl-9">
        </div>
        <select name="type" class="input-field w-auto text-xs py-1.5">
            <option value="">All types</option>
            <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Credits only</option>
            <option value="debit"  {{ request('type') === 'debit'  ? 'selected' : '' }}>Debits only</option>
            <option value="salary" {{ request('type') === 'salary' ? 'selected' : '' }}>Salary only</option>
        </select>
        <input type="month" name="month" value="{{ request('month') }}" title="Specific month"
               class="input-field w-auto text-xs py-1.5 font-mono">
        <select name="year" class="input-field w-auto text-xs py-1.5">
            <option value="">Any year</option>
            @foreach ($years as $y)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary text-xs py-1.5 px-3">Apply</button>
        @if(request()->hasAny(['type','month','year','search']))
            <a href="{{ route('transactions.index') }}" class="btn-ghost text-xs py-1.5 px-3">Clear</a>
        @endif
    </form>

    @if($transactions->isEmpty())
    <div class="text-center py-16 px-6">
        <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-2xl opacity-50">&#x1F4C3;</div>
        <p class="text-sm text-ink-soft dark:text-slate-400 mb-4">No transactions match the current filter.</p>
        <a href="{{ route('transactions.create') }}" class="btn-primary">Add one manually</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Label</th>
                    <th class="text-right">Débit</th>
                    <th class="text-right">Crédit</th>
                    <th>Type</th>
                    <th class="text-right"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                <tr>
                    <td>{{ $tx->paid_at->format('d M Y') }}</td>
                    <td class="max-w-[400px] truncate" title="{{ $tx->label }}">{{ $tx->label }}</td>
                    <td class="text-right font-mono {{ $tx->isDebit() ? 'text-ruby dark:text-ruby-400 font-medium' : 'text-ink-soft dark:text-slate-400' }}">
                        {{ $tx->isDebit() ? number_format(abs($tx->amount), 2) : '—' }}
                    </td>
                    <td class="text-right font-mono font-semibold {{ $tx->isCredit() ? 'text-emerald dark:text-emerald-400' : 'text-ink-soft dark:text-slate-400' }}">
                        {{ $tx->isCredit() ? number_format($tx->amount, 2) : '—' }}
                        @if($tx->isCredit() && $tx->allocations->isNotEmpty())
                        <span x-data="{ open: false }" class="relative inline-flex items-center ml-1">
                            <button @click="open = !open" class="text-ink-soft dark:text-slate-400 hover:text-primary transition-colors" title="View allocations">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute z-50 bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-4 rounded-xl bg-white dark:bg-surface-dark-card border border-border dark:border-border-dark shadow-lg text-left text-xs font-normal not-italic leading-relaxed" style="display: none;">
                                <p class="font-semibold text-ink dark:text-white mb-3">Allocations</p>
                                <div class="space-y-1.5">
                                    @foreach($tx->allocations as $a)
                                    <div class="flex items-center justify-between text-ink-soft dark:text-slate-400">
                                        <span>{{ $a->salaryMonth?->label }}</span>
                                        <span class="font-mono text-emerald dark:text-emerald-400 font-medium">{{ number_format($a->amount, 0) }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </span>
                        @endif
                    </td>
                    <td>
                        @if($tx->is_salary)
                            <span class="badge-emerald"><span class="w-1.5 h-1.5 rounded-full bg-emerald dark:bg-emerald-400 mr-1"></span>Salary</span>
                            @if($tx->unallocated > 0.005)
                            <span x-data="{ open: false }" class="relative inline-flex items-center ml-1">
                                <button @click="open = !open" class="text-amber dark:text-amber-400 hover:text-amber/80 transition-colors" title="Unallocated amount">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 bottom-full left-1/2 -translate-x-1/2 mb-2 w-56 p-4 rounded-xl bg-white dark:bg-surface-dark-card border border-border dark:border-border-dark shadow-lg text-xs font-normal not-italic leading-relaxed" style="display: none;">
                                    <p class="text-amber dark:text-amber-400 font-medium mb-2">Unallocated</p>
                                    <p class="text-ink-soft dark:text-slate-400">{{ number_format($tx->unallocated, 0) }} MAD — no eligible month has remaining capacity.</p>
                                </div>
                            </span>
                            @endif
                        @else
                            <span class="text-ink-soft dark:text-slate-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('transactions.edit', $tx) }}" class="btn-ghost text-xs py-1.5 px-3">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 border-t border-border dark:border-border-dark bg-surface-hover/50 dark:bg-surface-dark-hover/50">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

@endsection
