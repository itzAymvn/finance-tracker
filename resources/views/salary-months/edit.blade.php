@extends('layouts.app')
@section('title', 'Edit ' . $salaryMonth->label)
@section('content')

<div class="max-w-xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1">Salary Months</p>
            <h1 class="font-serif text-3xl text-ink leading-tight">Edit {{ $salaryMonth->label }}</h1>
        </div>
        <a href="{{ route('salary-months.show', $salaryMonth) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all shadow-sm">
            ← Back
        </a>
    </div>

    {{-- Edit form --}}
    <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden mb-4">
        <div class="px-6 py-4 bg-cream border-b border-border">
            <h2 class="font-serif text-lg text-ink">Month Details</h2>
        </div>
        <form action="{{ route('salary-months.update', $salaryMonth) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
                        Month <span class="text-amber">*</span>
                    </label>
                    <input type="month"
                           name="month_key"
                           value="{{ old('month_key', $salaryMonth->month_key) }}"
                           class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                  {{ $errors->has('month_key') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}">
                    @error('month_key')
                    <p class="mt-1.5 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
                            Expected Salary <span class="text-amber">*</span>
                        </label>
                        <input type="number"
                               name="expected_salary"
                               value="{{ old('expected_salary', $salaryMonth->expected_salary) }}"
                               min="0.01" step="0.01"
                               class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                      {{ $errors->has('expected_salary') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}">
                        @error('expected_salary')
                        <p class="mt-1.5 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
                            Currency <span class="text-amber">*</span>
                        </label>
                        <input type="text"
                               name="currency"
                               value="{{ old('currency', $salaryMonth->currency) }}"
                               maxlength="10"
                               class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                      {{ $errors->has('currency') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}">
                        @error('currency')
                        <p class="mt-1.5 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink outline-none resize-y transition-all
                                     {{ $errors->has('notes') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}">{{ old('notes', $salaryMonth->notes) }}</textarea>
                    @error('notes')
                    <p class="mt-1.5 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-ink hover:bg-ink-dim transition-all shadow-sm">
                    Update Month
                </button>
            </div>
        </form>
    </div>

    {{-- Danger zone --}}
    <div class="bg-white rounded-2xl border border-ruby/20 shadow-sm p-5">
        <p class="text-xs font-semibold tracking-widest uppercase text-ruby/60 mb-3">Danger Zone</p>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-ink">Delete this month</p>
                <p class="text-xs text-ink-soft mt-0.5">This will also remove all related allocations.</p>
            </div>
            <form action="{{ route('salary-months.destroy', $salaryMonth) }}" method="POST"
                  onsubmit="return confirm('Delete this month and all its allocations?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-ruby border border-ruby/25 hover:bg-ruby-lt transition-all">
                    Delete Month
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
