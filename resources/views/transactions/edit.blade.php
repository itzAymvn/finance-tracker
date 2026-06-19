@extends('layouts.app')
@section('title', 'Edit Transaction')
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="page-header">
        <div>
            <h1>Edit Transaction</h1>
            <p>Modify transaction details and tagging</p>
        </div>
        <a href="{{ route('transactions.index', ['page' => request('page')]) }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm">
        <div class="px-6 py-5 border-b border-border dark:border-border-dark bg-surface-hover/50 dark:bg-surface-dark-hover/50 grid grid-cols-2 sm:grid-cols-4 gap-y-4 gap-x-6">
            <div>
                <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1">Date</p>
                <p class="text-sm font-medium text-ink dark:text-white">{{ $transaction->paid_at->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1">Source</p>
                <p class="text-xs uppercase tracking-wider font-medium text-ink-soft dark:text-slate-400">{{ $transaction->source }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1">Amount</p>
                <p class="font-mono text-sm font-semibold {{ $transaction->isCredit() ? 'text-emerald dark:text-emerald-400' : 'text-ruby dark:text-ruby-400' }}">
                    {{ $transaction->isCredit() ? '+' : '' }}{{ number_format($transaction->amount, 2) }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1">Type</p>
                <p class="text-xs uppercase tracking-wider font-medium {{ $transaction->isCredit() ? 'text-emerald dark:text-emerald-400' : 'text-ruby dark:text-ruby-400' }}">
                    {{ $transaction->isCredit() ? 'Credit' : 'Debit' }}
                </p>
            </div>
            <div class="col-span-2 sm:col-span-4">
                <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1">Label</p>
                <p class="text-sm font-medium text-ink dark:text-white leading-snug">{{ $transaction->label }}</p>
            </div>
        </div>

        @if($transaction->is_salary && $transaction->allocations->isNotEmpty())
        <div class="px-6 py-5 border-b border-border dark:border-border-dark bg-emerald-lt/30 dark:bg-emerald-dark-bg/30">
            <p class="text-xs font-semibold tracking-wider uppercase text-emerald dark:text-emerald-400 mb-3">Allocated FIFO across months</p>
            <div class="space-y-1.5">
                @foreach($transaction->allocations as $a)
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-baseline gap-2">
                        <span class="font-mono text-ink dark:text-white">{{ $a->salaryMonth?->month_key }}</span>
                        <span class="text-ink-soft dark:text-slate-400 text-xs">{{ $a->salaryMonth?->label }}</span>
                    </span>
                    <span class="font-mono text-emerald dark:text-emerald-400 font-medium">{{ number_format($a->amount, 2) }}</span>
                </div>
                @endforeach
                @if($transaction->unallocated > 0.005)
                <div class="flex items-center justify-between text-sm pt-2 mt-2 border-t border-emerald/20">
                    <span class="text-amber dark:text-amber-400 text-xs">Unallocated (no eligible month capacity)</span>
                    <span class="font-mono text-amber dark:text-amber-400 font-medium">{{ number_format($transaction->unallocated, 2) }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <form action="{{ route('transactions.update', $transaction) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PATCH')

            @if($transaction->isCredit())
            <label class="flex items-start gap-3 cursor-pointer p-4 rounded-lg border border-border dark:border-border-dark bg-surface-hover/50 dark:bg-surface-dark-hover/50 hover:bg-surface-hover dark:hover:bg-surface-dark-hover transition-colors">
                <input type="checkbox" name="is_salary" value="1"
                       id="is_salary"
                       {{ old('is_salary', $transaction->is_salary) ? 'checked' : '' }}
                       class="mt-0.5 rounded border-border dark:border-border-dark text-primary focus:ring-primary/20">
                <div>
                    <span class="block text-sm font-medium text-ink dark:text-white">This is a salary credit</span>
                    <span class="block text-xs text-ink-soft dark:text-slate-400 mt-0.5">Turning this off removes all allocations. Turning it on splits the credit FIFO across eligible salary months on save.</span>
                </div>
            </label>
            @else
            <p class="text-sm text-ink-soft dark:text-slate-400 italic">This is a debit transaction. Salary tagging only applies to credits.</p>
            @endif

            <div class="flex items-center justify-between gap-3 pt-4 border-t border-border dark:border-border-dark">
                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}"
                      onsubmit="return confirm('Delete this transaction? This also removes its salary allocations.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Delete</button>
                </form>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@endsection
