@extends('layouts.app')
@section('title', 'Add Payout')
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1">Payouts</p>
            <h1 class="font-serif text-3xl text-ink leading-tight">Add Payout</h1>
        </div>
        <a href="{{ route('payouts.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all shadow-sm">
            ← Back
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
        <form action="{{ route('payouts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-6 space-y-5">
                {{-- Date & Amount --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
                            Date <span class="text-amber">*</span>
                        </label>
                        <input type="datetime-local"
                               name="paid_at"
                               value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}"
                               class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                      {{ $errors->has('paid_at') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}">
                        @error('paid_at')
                        <p class="mt-1.5 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
                            Amount <span class="text-amber">*</span>
                        </label>
                        <input type="number"
                               name="amount"
                               value="{{ old('amount') }}"
                               placeholder="2000.00"
                               min="0.01" step="0.01"
                               class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                      {{ $errors->has('amount') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}">
                        @error('amount')
                        <p class="mt-1.5 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Note --}}
                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">Note</label>
                    <textarea name="note"
                              rows="2"
                              placeholder="Optional note..."
                              class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink outline-none resize-y transition-all
                                     {{ $errors->has('note') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}">{{ old('note') }}</textarea>
                    @error('note')
                    <p class="mt-1.5 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Attachment --}}
                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">Proof / Attachment</label>
                    <div class="relative border-2 border-dashed border-border rounded-xl p-5 text-center cursor-pointer hover:border-amber hover:bg-amber-bg/40 transition-all duration-150 group">
                        <input type="file"
                               name="attachment"
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="text-2xl mb-1.5 opacity-40 group-hover:opacity-60 transition-opacity">📎</div>
                        <p class="text-xs text-ink-soft">
                            <strong class="text-amber">Click to upload</strong> or drag & drop
                        </p>
                        <p class="text-xs text-ink-soft/60 mt-0.5">JPG, PNG, PDF, DOC — max 5 MB</p>
                    </div>
                    @error('attachment')
                    <p class="mt-1.5 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Allocation section --}}
            <div class="border-t border-border px-6 py-5">
                <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-4">Allocation Mode</p>

                <div class="flex gap-2 flex-wrap mb-5">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="allocation_mode" id="mode_auto" value="auto"
                               {{ old('allocation_mode', 'auto') === 'auto' ? 'checked' : '' }}
                               class="peer sr-only">
                        <span class="flex items-center gap-2 px-4 py-2.5 rounded-lg border text-sm font-medium transition-all
                                     border-border text-ink-soft bg-white
                                     peer-checked:bg-ink peer-checked:text-cream peer-checked:border-ink
                                     hover:border-ink-soft hover:text-ink">
                            <span class="text-base">⚡</span>
                            <span><strong>Auto</strong> — oldest months first</span>
                        </span>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="allocation_mode" id="mode_manual" value="manual"
                               {{ old('allocation_mode') === 'manual' ? 'checked' : '' }}
                               class="peer sr-only">
                        <span class="flex items-center gap-2 px-4 py-2.5 rounded-lg border text-sm font-medium transition-all
                                     border-border text-ink-soft bg-white
                                     peer-checked:bg-ink peer-checked:text-cream peer-checked:border-ink
                                     hover:border-ink-soft hover:text-ink">
                            <span class="text-base">✏️</span>
                            <span><strong>Manual</strong> — specify per month</span>
                        </span>
                    </label>
                </div>

                {{-- Auto allocation preview --}}
                <div id="auto-preview" class="{{ old('allocation_mode') === 'manual' ? 'hidden' : '' }}">
                    <div id="auto-preview-empty" class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-cream-2/60 border border-border text-xs text-ink-soft">
                        <span class="opacity-50">⚡</span>
                        Enter an amount above to preview the allocation.
                    </div>
                    <div id="auto-preview-rows" class="hidden rounded-xl border border-border overflow-hidden">
                        <div class="px-4 py-2.5 bg-cream border-b border-border flex items-center justify-between">
                            <span class="text-xs font-semibold tracking-widest uppercase text-ink-soft">Preview</span>
                            <span id="auto-preview-unallocated-wrap" class="hidden">
                                <span class="text-xs font-semibold text-amber bg-amber-bg border border-amber/20 rounded-full px-2.5 py-0.5">
                                    <span id="auto-preview-unallocated"></span> unallocated
                                </span>
                            </span>
                        </div>
                        <div id="auto-preview-list" class="divide-y divide-cream-2"></div>
                    </div>
                </div>

                {{-- Manual allocation lines --}}
                <div id="manual-allocation" class="{{ old('allocation_mode') === 'manual' ? '' : 'hidden' }}">
                    <div id="allocation-lines" class="space-y-2 mb-3">
                        @if(old('allocations'))
                            @foreach(old('allocations') as $i => $alloc)
                            <div class="allocation-row flex items-center gap-2">
                                <select name="allocations[{{ $i }}][salary_month_id]"
                                        class="flex-1 bg-white border border-border rounded-lg px-3 py-2 text-sm text-ink outline-none focus:border-amber focus:ring-2 focus:ring-amber/15 transition-all appearance-none">
                                    <option value="">— Select month —</option>
                                    @foreach($months as $month)
                                    <option value="{{ $month->id }}" {{ (string)($alloc['salary_month_id'] ?? '') === (string)$month->id ? 'selected' : '' }}>
                                        {{ $month->label }} (remaining: {{ number_format($month->remaining, 2) }})
                                    </option>
                                    @endforeach
                                </select>
                                <input type="number" name="allocations[{{ $i }}][amount]"
                                       value="{{ $alloc['amount'] ?? '' }}"
                                       placeholder="Amount"
                                       min="0.01" step="0.01"
                                       class="w-28 bg-white border border-border rounded-lg px-3 py-2 text-sm text-ink font-mono outline-none focus:border-amber focus:ring-2 focus:ring-amber/15 transition-all">
                                <button type="button"
                                        class="remove-row w-8 h-9 flex items-center justify-center rounded-lg border border-ruby/20 text-ruby hover:bg-ruby-lt transition-all text-sm shrink-0">
                                    ×
                                </button>
                            </div>
                            @endforeach
                        @else
                        <div class="allocation-row flex items-center gap-2">
                            <select name="allocations[0][salary_month_id]"
                                    class="flex-1 bg-white border border-border rounded-lg px-3 py-2 text-sm text-ink outline-none focus:border-amber focus:ring-2 focus:ring-amber/15 transition-all appearance-none">
                                <option value="">— Select month —</option>
                                @foreach($months as $month)
                                <option value="{{ $month->id }}">{{ $month->label }} (remaining: {{ number_format($month->remaining, 2) }})</option>
                                @endforeach
                            </select>
                            <input type="number" name="allocations[0][amount]"
                                   placeholder="Amount"
                                   min="0.01" step="0.01"
                                   class="w-28 bg-white border border-border rounded-lg px-3 py-2 text-sm text-ink font-mono outline-none focus:border-amber focus:ring-2 focus:ring-amber/15 transition-all">
                            <button type="button"
                                    class="remove-row w-8 h-9 flex items-center justify-center rounded-lg border border-ruby/20 text-ruby hover:bg-ruby-lt transition-all text-sm shrink-0">
                                ×
                            </button>
                        </div>
                        @endif
                    </div>
                    <button type="button" id="add-row"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold tracking-wider uppercase text-ink-soft border border-border bg-white hover:bg-cream-2 hover:text-ink transition-all">
                        + Add Line
                    </button>
                </div>
            </div>

            {{-- Submit --}}
            <div class="px-6 pb-6">
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-ink hover:bg-ink-dim transition-all shadow-sm">
                    Save Payout
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
const months = @json($months->map(fn($m) => ['id' => $m->id, 'label' => $m->label, 'remaining' => $m->remaining]));

// ── Auto-allocation preview ──────────────────────────────────────────
const amountInput       = document.querySelector('input[name="amount"]');
const previewEmpty      = document.getElementById('auto-preview-empty');
const previewRows       = document.getElementById('auto-preview-rows');
const previewList       = document.getElementById('auto-preview-list');
const previewUnallocWrap = document.getElementById('auto-preview-unallocated-wrap');
const previewUnalloc    = document.getElementById('auto-preview-unallocated');

function fmt(n) { return parseFloat(n).toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

function simulateAutoAlloc(amount) {
    const lines = [];
    let remaining = amount;
    for (const m of months) {
        if (remaining <= 0) break;
        if (m.remaining <= 0) continue;
        const alloc = Math.min(remaining, m.remaining);
        lines.push({ label: m.label, alloc, monthRemaining: m.remaining });
        remaining -= alloc;
    }
    return { lines, unallocated: remaining };
}

function renderPreview() {
    const amount = parseFloat(amountInput.value);
    const isAuto = document.querySelector('input[name="allocation_mode"]:checked')?.value === 'auto';

    if (!isAuto) return;

    if (!amount || amount <= 0 || months.filter(m => m.remaining > 0).length === 0) {
        previewEmpty.classList.remove('hidden');
        previewRows.classList.add('hidden');
        // Show a hint if no unpaid months exist
        if (months.filter(m => m.remaining > 0).length === 0) {
            previewEmpty.innerHTML = '<span class="opacity-50">✓</span> All months are fully paid — payout will be unallocated.';
        } else {
            previewEmpty.innerHTML = '<span class="opacity-50">⚡</span> Enter an amount above to preview the allocation.';
        }
        return;
    }

    const { lines, unallocated } = simulateAutoAlloc(amount);

    if (lines.length === 0) {
        previewEmpty.classList.remove('hidden');
        previewRows.classList.add('hidden');
        previewEmpty.innerHTML = '<span class="opacity-50">✓</span> All months are fully paid — payout will be unallocated.';
        return;
    }

    previewEmpty.classList.add('hidden');
    previewRows.classList.remove('hidden');

    previewList.innerHTML = lines.map(({ label, alloc, monthRemaining }) => {
        const pct = Math.min((alloc / monthRemaining) * 100, 100);
        const fullyCovered = alloc >= monthRemaining;
        return `
        <div class="flex items-center gap-3 px-4 py-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-medium text-ink truncate">${label}</span>
                    <span class="font-mono text-sm font-semibold ${fullyCovered ? 'text-emerald' : 'text-amber'} ml-3 shrink-0">
                        ${fmt(alloc)}
                    </span>
                </div>
                <div class="h-1.5 bg-cream-2 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300 ${fullyCovered ? 'bg-emerald' : 'bg-amber'}"
                         style="width:${pct}%"></div>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-xs text-ink-soft">of ${fmt(monthRemaining)} remaining</span>
                    ${fullyCovered
                        ? '<span class="text-xs font-semibold text-emerald">Fully covered ✓</span>'
                        : `<span class="text-xs text-ink-soft">${fmt(monthRemaining - alloc)} still owed</span>`
                    }
                </div>
            </div>
        </div>`;
    }).join('');

    if (unallocated > 0.001) {
        previewUnallocWrap.classList.remove('hidden');
        previewUnalloc.textContent = fmt(unallocated);
    } else {
        previewUnallocWrap.classList.add('hidden');
    }
}

amountInput.addEventListener('input', renderPreview);

// ── Mode toggle ──────────────────────────────────────────────────────
document.querySelectorAll('input[name="allocation_mode"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const isManual = this.value === 'manual';
        document.getElementById('manual-allocation').classList.toggle('hidden', !isManual);
        document.getElementById('auto-preview').classList.toggle('hidden', isManual);
        if (!isManual) renderPreview();
    });
});

// Run on load if auto is already selected
renderPreview();

// ── Manual rows ──────────────────────────────────────────────────────
let rowIndex = document.querySelectorAll('.allocation-row').length;

document.getElementById('add-row').addEventListener('click', function () {
    const options = months.map(m =>
        `<option value="${m.id}">${m.label} (remaining: ${parseFloat(m.remaining).toFixed(2)})</option>`
    ).join('');

    const div = document.createElement('div');
    div.className = 'allocation-row flex items-center gap-2';
    div.innerHTML = `
        <select name="allocations[${rowIndex}][salary_month_id]"
                class="flex-1 bg-white border border-border rounded-lg px-3 py-2 text-sm text-ink outline-none focus:border-amber focus:ring-2 focus:ring-amber/15 transition-all appearance-none">
            <option value="">— Select month —</option>${options}
        </select>
        <input type="number" name="allocations[${rowIndex}][amount]"
               placeholder="Amount" min="0.01" step="0.01"
               class="w-28 bg-white border border-border rounded-lg px-3 py-2 text-sm text-ink font-mono outline-none focus:border-amber focus:ring-2 focus:ring-amber/15 transition-all">
        <button type="button" class="remove-row w-8 h-9 flex items-center justify-center rounded-lg border border-ruby/20 text-ruby hover:bg-ruby-lt transition-all text-sm shrink-0">×</button>`;
    document.getElementById('allocation-lines').appendChild(div);
    rowIndex++;
});

document.getElementById('allocation-lines').addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        const rows = this.querySelectorAll('.allocation-row');
        if (rows.length > 1) e.target.closest('.allocation-row').remove();
    }
});
</script>
@endsection
