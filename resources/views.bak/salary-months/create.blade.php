@extends('layouts.app')
@section('title', 'Add Salary Month')
@section('content')

<div class="max-w-xl mx-auto">
    <div class="page-header">
        <div>
            <h1>Add Month</h1>
            <p>Create a new salary month or a period of months</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm overflow-hidden">
        <div class="flex border-b border-border dark:border-border-dark px-3 pt-3 gap-1" id="tab-bar">
            <button class="tab-btn px-5 py-2.5 rounded-t-lg text-sm font-medium transition-all bg-white dark:bg-surface-dark-card border border-b-white dark:border-b-surface-dark-card text-ink dark:text-white -mb-px shadow-sm"
                    data-tab="single">
                Single Month
            </button>
            <button class="tab-btn px-5 py-2.5 rounded-t-lg text-sm font-medium transition-all text-ink-soft dark:text-slate-400 hover:text-ink dark:hover:text-white hover:bg-surface-hover dark:hover:bg-surface-dark-hover -mb-px"
                    data-tab="period">
                Period
            </button>
        </div>

        <div class="tab-pane p-6 space-y-5" id="pane-single">
            <form action="{{ route('salary-months.store') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Month <span class="text-primary">*</span></label>
                        <input class="input-field font-mono {{ $errors->has('month_key') ? 'has-error' : '' }}"
                               name="month_key" type="month" value="{{ old('month_key', now()->format('Y-m')) }}">
                        @error('month_key')<p class="mt-1 text-xs text-ruby">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Expected Salary <span class="text-primary">*</span></label>
                            <input class="input-field font-mono {{ $errors->has('expected_salary') ? 'has-error' : '' }}"
                                   name="expected_salary" type="number" value="{{ old('expected_salary') }}" placeholder="4000.00" min="0.01" step="0.01">
                            @error('expected_salary')<p class="mt-1 text-xs text-ruby">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Currency <span class="text-primary">*</span></label>
                            <input class="input-field font-mono {{ $errors->has('currency') ? 'has-error' : '' }}"
                                   name="currency" type="text" value="{{ old('currency', 'MAD') }}" maxlength="10">
                            @error('currency')<p class="mt-1 text-xs text-ruby">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Notes</label>
                        <textarea class="input-field resize-y {{ $errors->has('notes') ? 'has-error' : '' }}"
                                  name="notes" rows="2" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-xs text-ruby">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary w-full py-2.5">Save Month</button>
                </div>
            </form>
        </div>

        <div class="tab-pane p-6 space-y-5 hidden" id="pane-period">
            <p class="text-sm text-ink-soft dark:text-slate-400 bg-amber-lt dark:bg-amber-dark-bg border border-amber/20 rounded-lg px-4 py-3">
                Creates one salary month for every month in the range. Months that already exist are skipped.
            </p>
            <form action="{{ route('salary-months.storePeriod') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">From <span class="text-primary">*</span></label>
                            <input class="input-field font-mono {{ $errors->has('from_month') ? 'has-error' : '' }}"
                                   name="from_month" type="month" value="{{ old('from_month') }}">
                            @error('from_month')<p class="mt-1 text-xs text-ruby">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">To <span class="text-primary">*</span></label>
                            <input class="input-field font-mono {{ $errors->has('to_month') ? 'has-error' : '' }}"
                                   name="to_month" type="month" value="{{ old('to_month') }}">
                            @error('to_month')<p class="mt-1 text-xs text-ruby">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Expected Salary <span class="text-primary">*</span></label>
                            <input class="input-field font-mono {{ $errors->has('expected_salary') ? 'has-error' : '' }}"
                                   name="expected_salary" type="number" value="{{ old('expected_salary') }}" placeholder="4000.00" min="0.01" step="0.01">
                            <p class="mt-1.5 text-xs text-ink-soft dark:text-slate-400">Applied to every month in range.</p>
                            @error('expected_salary')<p class="mt-1 text-xs text-ruby">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Currency <span class="text-primary">*</span></label>
                            <input class="input-field font-mono {{ $errors->has('currency') ? 'has-error' : '' }}"
                                   name="currency" type="text" value="{{ old('currency', 'MAD') }}" maxlength="10">
                            @error('currency')<p class="mt-1 text-xs text-ruby">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-1.5">Notes</label>
                        <textarea class="input-field resize-y {{ $errors->has('notes') ? 'has-error' : '' }}"
                                  name="notes" rows="2" placeholder="Optional — applied to every month in the range.">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-xs text-ruby">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary w-full py-2.5">Create Period</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    function switchTab(tabName) {
        tabBtns.forEach(btn => {
            const isActive = btn.dataset.tab === tabName;
            btn.classList.toggle('bg-white', isActive);
            btn.classList.toggle('dark:bg-surface-dark-card', isActive);
            btn.classList.toggle('text-ink', isActive);
            btn.classList.toggle('dark:text-white', isActive);
            btn.classList.toggle('shadow-sm', isActive);
            btn.classList.toggle('-mb-px', isActive);
            btn.classList.toggle('border', isActive);
            btn.classList.toggle('border-b-white', isActive);
            btn.classList.toggle('dark:border-b-surface-dark-card', isActive);
            btn.classList.toggle('text-ink-soft', !isActive);
            btn.classList.toggle('dark:text-slate-400', !isActive);
            btn.classList.toggle('hover:text-ink', !isActive);
            btn.classList.toggle('dark:hover:text-white', !isActive);
            btn.classList.toggle('hover:bg-surface-hover', !isActive);
            btn.classList.toggle('dark:hover:bg-surface-dark-hover', !isActive);
        });
        tabPanes.forEach(pane => pane.classList.toggle('hidden', !pane.id.endsWith(tabName)));
    }
    tabBtns.forEach(btn => btn.addEventListener('click', () => switchTab(btn.dataset.tab)));
    @if ($errors->has('from_month') || $errors->has('to_month'))
        document.addEventListener('DOMContentLoaded', () => switchTab('period'));
    @endif
</script>
@endsection
