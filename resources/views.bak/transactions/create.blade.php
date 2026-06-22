@extends('layouts.app')
@section('title', 'New Transaction')
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="page-header">
        <div>
            <h1>New Transaction</h1>
            <p>Record a new credit or debit transaction</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm">
        <form action="{{ route('transactions.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Type</label>
                <div class="grid grid-cols-2 gap-3" id="type-toggle">
                    <button type="button" data-type="credit"
                            class="type-btn flex items-center justify-center gap-2 px-4 py-3 rounded-lg border text-sm font-medium transition-all border-emerald/30 bg-emerald-lt/40 text-emerald dark:bg-emerald-dark-bg/40 dark:text-emerald-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Credit (money in)
                    </button>
                    <button type="button" data-type="debit"
                            class="type-btn flex items-center justify-center gap-2 px-4 py-3 rounded-lg border text-sm font-medium transition-all border-border dark:border-border-dark bg-white dark:bg-surface-dark-card text-ink-soft dark:text-slate-400 hover:bg-surface-hover dark:hover:bg-surface-dark-hover">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        Debit (money out)
                    </button>
                </div>
                <input type="hidden" name="amount_sign" id="amount_sign" value="1">
            </div>

            <div>
                <label for="amount" class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Amount <span class="text-primary">*</span></label>
                <div class="relative">
                    <input id="amount" name="amount" type="number" step="0.01" min="0" required
                           value="{{ old('amount') }}"
                           placeholder="0.00"
                           class="input-field pl-12 py-2.5 text-lg font-mono {{ $errors->has('amount') ? 'has-error' : '' }}">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-mono text-ink-soft dark:text-slate-400" id="amount_prefix">+</span>
                </div>
                @error('amount')
                    <p class="mt-1.5 text-xs text-ruby">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="paid_at" class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Date <span class="text-primary">*</span></label>
                    <input id="paid_at" name="paid_at" type="date" required
                           value="{{ old('paid_at', now()->format('Y-m-d')) }}"
                           class="input-field py-2.5 font-mono {{ $errors->has('paid_at') ? 'has-error' : '' }}">
                    @error('paid_at')
                        <p class="mt-1.5 text-xs text-ruby">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="value_date" class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Value date <span class="text-ink-soft dark:text-slate-400 normal-case font-normal">(optional)</span></label>
                    <input id="value_date" name="value_date" type="date"
                           value="{{ old('value_date') }}"
                           class="input-field py-2.5 font-mono {{ $errors->has('value_date') ? 'has-error' : '' }}">
                    @error('value_date')
                        <p class="mt-1.5 text-xs text-ruby">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="label" class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Label <span class="text-primary">*</span></label>
                <input id="label" name="label" type="text" required maxlength="255"
                       value="{{ old('label') }}"
                       placeholder="e.g. VIREMENT RECU DE ... / PAIEMENT CARTE ..."
                       class="input-field py-2.5 {{ $errors->has('label') ? 'has-error' : '' }}">
                @error('label')
                    <p class="mt-1.5 text-xs text-ruby">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-start gap-3 cursor-pointer p-4 rounded-lg border border-border dark:border-border-dark bg-surface-hover/50 dark:bg-surface-dark-hover/50 hover:bg-surface-hover dark:hover:bg-surface-dark-hover transition-colors">
                <input type="checkbox" name="is_salary" value="1"
                       id="is_salary"
                       {{ old('is_salary') ? 'checked' : '' }}
                       class="mt-0.5 rounded border-border dark:border-border-dark text-primary focus:ring-primary/20">
                <div>
                    <span class="block text-sm font-medium text-ink dark:text-white">This is a salary credit</span>
                    <span class="block text-xs text-ink-soft dark:text-slate-400 mt-0.5">Tag as salary &mdash; the credit will be split FIFO across eligible salary months on save (applies only when type = Credit).</span>
                </div>
            </label>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('transactions.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create Transaction</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const btns = document.querySelectorAll('.type-btn');
    const signInput = document.getElementById('amount_sign');
    const prefix = document.getElementById('amount_prefix');
    const amountInput = document.getElementById('amount');
    const salaryCheckbox = document.getElementById('is_salary');
    const salaryLabel = salaryCheckbox.closest('label');

    function applyType(type) {
        btns.forEach(b => {
            const active = b.dataset.type === type;
            b.className = 'type-btn flex items-center justify-center gap-2 px-4 py-3 rounded-lg border text-sm font-medium transition-all' +
                (active
                    ? (type === 'credit'
                        ? ' border-emerald/30 bg-emerald-lt/40 text-emerald dark:bg-emerald-dark-bg/40 dark:text-emerald-300'
                        : ' border-ruby/30 bg-ruby-lt/40 text-ruby dark:bg-ruby-dark-bg/40 dark:text-ruby-300')
                    : ' border-border dark:border-border-dark bg-white dark:bg-surface-dark-card text-ink-soft dark:text-slate-400 hover:bg-surface-hover dark:hover:bg-surface-dark-hover');
        });

        const sign = type === 'credit' ? 1 : -1;
        signInput.value = sign;
        prefix.textContent = sign > 0 ? '+' : '−';
        prefix.classList.toggle('text-emerald', sign > 0);
        prefix.classList.toggle('text-ruby', sign < 0);

        if (type === 'debit' && salaryCheckbox.checked) {
            salaryCheckbox.checked = false;
        }
        salaryLabel.style.opacity = type === 'credit' ? '1' : '0.5';
        salaryLabel.style.pointerEvents = type === 'credit' ? 'auto' : 'none';
    }

    btns.forEach(b => b.addEventListener('click', () => applyType(b.dataset.type)));

    const oldAmount = {{ old('amount') ? (float) old('amount') : 'null' }};
    applyType(oldAmount !== null ? (oldAmount >= 0 ? 'credit' : 'debit') : 'credit');
</script>
@endsection
