@extends('layouts.app')
@section('title', 'Payout — ' . $payout->paid_at->format('d M Y'))
@section('content')

<div class="flex items-end justify-between mb-8">
    <div>
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1">Payout Detail</p>
        <h1 class="font-serif text-3xl text-ink leading-tight">{{ $payout->paid_at->format('d M Y') }}</h1>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('payouts.edit', $payout) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all shadow-sm">
            Edit
        </a>
        <a href="{{ route('payouts.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all shadow-sm">
            ← Back
        </a>
    </div>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-2xl border border-border p-5 shadow-sm relative overflow-hidden">
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Total Amount</p>
        <p class="font-mono text-xl font-medium text-ink">{{ number_format($payout->amount, 2) }}</p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-border"></div>
    </div>
    <div class="bg-white rounded-2xl border border-border p-5 shadow-sm relative overflow-hidden">
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Allocated</p>
        <p class="font-mono text-xl font-medium text-emerald">{{ number_format($payout->allocated_total, 2) }}</p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-emerald"></div>
    </div>
    <div class="bg-white rounded-2xl border border-border p-5 shadow-sm relative overflow-hidden">
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Unallocated</p>
        <p class="font-mono text-xl font-medium {{ $payout->unallocated > 0 ? 'text-amber' : 'text-ink-soft' }}">
            {{ number_format($payout->unallocated, 2) }}
        </p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] {{ $payout->unallocated > 0 ? 'bg-amber' : 'bg-border' }}"></div>
    </div>
    <div class="bg-white rounded-2xl border border-border p-5 shadow-sm relative overflow-hidden">
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Date & Time</p>
        <p class="font-mono text-sm font-medium text-ink leading-snug">{{ $payout->paid_at->format('d M Y') }}<br><span class="text-ink-soft">{{ $payout->paid_at->format('H:i') }}</span></p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-sapphire"></div>
    </div>
</div>

{{-- Note --}}
@if($payout->note)
<div class="bg-white rounded-2xl border border-border shadow-sm p-5 mb-4">
    <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Note</p>
    <p class="text-sm text-ink leading-relaxed">{{ $payout->note }}</p>
</div>
@endif

{{-- Attachment --}}
@if($payout->hasAttachment())
<div class="bg-white rounded-2xl border border-border shadow-sm p-5 mb-4">
    <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-3">Attachment</p>
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-cream-2 flex items-center justify-center text-xl shrink-0">📎</div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-ink truncate">{{ $payout->attachment_name }}</p>
            <p class="text-xs text-ink-soft">{{ $payout->attachment_mime }}</p>
        </div>
        <a href="{{ route('payouts.attachment', $payout) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white bg-ink hover:bg-ink-dim no-underline transition-all shadow-sm shrink-0"
           target="_blank">
            Download
        </a>
    </div>
</div>
@endif

{{-- Allocation breakdown --}}
<div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden mb-6">
    <div class="flex items-center px-6 py-4 bg-cream border-b border-border">
        <h2 class="font-serif text-lg text-ink">Allocation Breakdown</h2>
    </div>
    <div class="p-6">
        @if($payout->allocations->isEmpty())
        <p class="text-sm text-ink-soft">No allocations yet.</p>
        @else
        <div class="space-y-0">
            @foreach($payout->allocations->sortBy('salaryMonth.month_key') as $allocation)
            <div class="flex items-center gap-3 py-3 border-b border-cream-2 last:border-b-0">
                <span class="font-mono text-sm font-semibold text-ink w-24 shrink-0">{{ number_format($allocation->amount, 2) }}</span>
                <span class="text-amber font-bold text-xs shrink-0">→</span>
                <a href="{{ route('salary-months.show', $allocation->salaryMonth) }}"
                   class="text-sm text-ink hover:text-amber no-underline transition-colors font-medium">
                    {{ $allocation->salaryMonth->label }}
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Danger zone --}}
<div class="bg-white rounded-2xl border border-ruby/20 shadow-sm p-5">
    <p class="text-xs font-semibold tracking-widest uppercase text-ruby/60 mb-3">Danger Zone</p>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-ink">Delete this payout</p>
            <p class="text-xs text-ink-soft mt-0.5">This will also remove all related allocations.</p>
        </div>
        <form action="{{ route('payouts.destroy', $payout) }}" method="POST"
              onsubmit="return confirm('Delete this payout and all its allocations?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-ruby border border-ruby/25 hover:bg-ruby-lt transition-all">
                Delete Payout
            </button>
        </form>
    </div>
</div>

@endsection
