@extends('layouts.app')
@section('title', 'Edit Payout')
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1">Payouts</p>
            <h1 class="font-serif text-3xl text-ink leading-tight">Edit Payout</h1>
        </div>
        <a href="{{ route('payouts.show', $payout) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all shadow-sm">
            ← Back
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
        <form action="{{ route('payouts.update', $payout) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
                            Date <span class="text-amber">*</span>
                        </label>
                        <input type="datetime-local"
                               name="paid_at"
                               value="{{ old('paid_at', $payout->paid_at->format('Y-m-d\TH:i')) }}"
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
                               value="{{ old('amount', $payout->amount) }}"
                               min="0.01" step="0.01"
                               class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                      {{ $errors->has('amount') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}">
                        @error('amount')
                        <p class="mt-1.5 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">Note</label>
                    <textarea name="note" rows="2"
                              class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink outline-none resize-y transition-all
                                     {{ $errors->has('note') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}">{{ old('note', $payout->note) }}</textarea>
                    @error('note')
                    <p class="mt-1.5 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">Proof / Attachment</label>
                    @if($payout->hasAttachment())
                    <div class="flex items-center gap-2.5 px-4 py-3 bg-cream-2 rounded-lg border border-border mb-2">
                        <span>📎</span>
                        <a href="{{ route('payouts.attachment', $payout) }}" target="_blank"
                           class="text-sm text-ink hover:text-amber no-underline transition-colors truncate flex-1">
                            {{ $payout->attachment_name }}
                        </a>
                        <span class="text-xs text-ink-soft shrink-0">Upload to replace</span>
                    </div>
                    @endif
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
                            <span><strong>Auto</strong> — re-run auto allocation</span>
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

                <div id="manual-allocation" class="{{ old('allocation_mode') === 'manual' ? '' : 'hidden' }}">
                    <div id="allocation-lines" class="space-y-2 mb-3">
                        @php
                            $existingAllocations = old('allocations', $payout->allocations->map(fn($a) => [
                                'salary_month_id' => $a->salary_month_id,
                                'amount' => $a->amount,
                            ])->toArray());
                        @endphp
                        @foreach($existingAllocations as $i => $alloc)
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
                                   placeholder="Amount" min="0.01" step="0.01"
                                   class="w-28 bg-white border border-border rounded-lg px-3 py-2 text-sm text-ink font-mono outline-none focus:border-amber focus:ring-2 focus:ring-amber/15 transition-all">
                            <button type="button"
                                    class="remove-row w-8 h-9 flex items-center justify-center rounded-lg border border-ruby/20 text-ruby hover:bg-ruby-lt transition-all text-sm shrink-0">
                                ×
                            </button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-row"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold tracking-wider uppercase text-ink-soft border border-border bg-white hover:bg-cream-2 hover:text-ink transition-all">
                        + Add Line
                    </button>
                </div>
            </div>

            <div class="px-6 pb-6">
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-ink hover:bg-ink-dim transition-all shadow-sm">
                    Update Payout
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
const months = @json($months->map(fn($m) => ['id' => $m->id, 'label' => $m->label, 'remaining' => $m->remaining]));

document.querySelectorAll('input[name="allocation_mode"]').forEach(radio => {
    radio.addEventListener('change', function () {
        document.getElementById('manual-allocation').classList.toggle('hidden', this.value !== 'manual');
    });
});

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
