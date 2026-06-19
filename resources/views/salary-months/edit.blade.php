@extends('layouts.app')
@section('title', 'Edit ' . $salaryMonth->label)
@section('content')

<div class="max-w-xl mx-auto">
    <div class="page-header">
        <div>
            <h1>Edit {{ $salaryMonth->label }}</h1>
            <p>Update salary month details</p>
        </div>
        <a href="{{ route('salary-months.show', $salaryMonth) }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-border dark:border-border-dark">
            <h2 class="text-lg font-semibold text-ink dark:text-white">Month Details</h2>
        </div>
        <form action="{{ route('salary-months.update', $salaryMonth) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Month <span class="text-primary">*</span></label>
                    <input type="month" name="month_key" value="{{ old('month_key', $salaryMonth->month_key) }}"
                           class="input-field font-mono {{ $errors->has('month_key') ? 'has-error' : '' }}">
                    @error('month_key')<p class="mt-1.5 text-xs text-ruby">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Expected Salary <span class="text-primary">*</span></label>
                        <input type="number" name="expected_salary" value="{{ old('expected_salary', $salaryMonth->expected_salary) }}"
                               min="0.01" step="0.01" class="input-field font-mono {{ $errors->has('expected_salary') ? 'has-error' : '' }}">
                        @error('expected_salary')<p class="mt-1.5 text-xs text-ruby">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Currency <span class="text-primary">*</span></label>
                        <input type="text" name="currency" value="{{ old('currency', $salaryMonth->currency) }}"
                               maxlength="10" class="input-field font-mono {{ $errors->has('currency') ? 'has-error' : '' }}">
                        @error('currency')<p class="mt-1.5 text-xs text-ruby">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Notes</label>
                    <textarea name="notes" rows="3" class="input-field resize-y {{ $errors->has('notes') ? 'has-error' : '' }}">{{ old('notes', $salaryMonth->notes) }}</textarea>
                    @error('notes')<p class="mt-1.5 text-xs text-ruby">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-primary w-full py-2.5">Update Month</button>
            </div>
        </form>
    </div>

    <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-ruby/20 shadow-sm p-5">
        <p class="text-xs font-semibold tracking-wider uppercase text-ruby/60 mb-3">Danger Zone</p>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-ink dark:text-white">Delete this month</p>
                <p class="text-xs text-ink-soft dark:text-slate-400 mt-0.5">This will also remove all related allocations.</p>
            </div>
            <form action="{{ route('salary-months.destroy', $salaryMonth) }}" method="POST"
                  onsubmit="return confirm('Delete this month and all its allocations?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Delete Month</button>
            </form>
        </div>
    </div>
</div>

@endsection
