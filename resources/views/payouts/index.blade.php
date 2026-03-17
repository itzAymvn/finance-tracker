@extends('layouts.app')
@section('title', 'Payouts')
@section('content')

<div class="flex items-end justify-between mb-8">
    <div>
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1">Finance</p>
        <h1 class="font-serif text-3xl text-ink leading-tight">Payouts</h1>
    </div>
    <a href="{{ route('payouts.create') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white bg-ink hover:bg-ink-dim no-underline transition-all shadow-sm">
        + Add Payout
    </a>
</div>

<div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
    {{-- Filters --}}
    <form method="GET" action="{{ route('payouts.index') }}" class="flex flex-wrap items-center gap-3 px-6 py-4 bg-cream border-b border-border">
        <span class="text-xs font-semibold tracking-widest uppercase text-ink-soft">Filters</span>
        <input type="date" name="from" value="{{ request('from') }}" class="text-sm rounded-lg border border-border bg-white px-3 py-1.5 text-ink font-mono focus:outline-none focus:ring-2 focus:ring-amber/20 focus:border-amber" title="From date">
        <span class="text-ink-soft text-xs">–</span>
        <input type="date" name="to" value="{{ request('to') }}" class="text-sm rounded-lg border border-border bg-white px-3 py-1.5 text-ink font-mono focus:outline-none focus:ring-2 focus:ring-amber/20 focus:border-amber" title="To date">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="has_unallocated" value="1" {{ request()->boolean('has_unallocated') ? 'checked' : '' }} class="rounded border-border text-amber focus:ring-amber/20">
            <span class="text-sm text-ink-soft">Unallocated only</span>
        </label>
        <select name="has_attachment" class="text-sm rounded-lg border border-border bg-white px-3 py-1.5 text-ink-soft focus:outline-none focus:ring-2 focus:ring-amber/20 focus:border-amber">
            <option value="">Any attachment</option>
            <option value="1" {{ request('has_attachment') === '1' ? 'selected' : '' }}>With attachment</option>
            <option value="0" {{ request('has_attachment') === '0' ? 'selected' : '' }}>No attachment</option>
        </select>
        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-ink text-white hover:bg-ink-dim transition-colors">Filter</button>
        @if(request()->hasAny(['from','to','has_unallocated','has_attachment']))
            <a href="{{ route('payouts.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-ink-soft hover:text-ink transition-colors">Clear</a>
        @endif
    </form>

    @if($payouts->isEmpty())
    <div class="text-center py-16 px-6">
        <div class="text-4xl mb-3 opacity-30">💸</div>
        <p class="text-sm text-ink-soft mb-4">No payouts yet.</p>
        <a href="{{ route('payouts.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white bg-ink hover:bg-ink-dim no-underline transition-all shadow-sm">
            Add the first one
        </a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b border-border bg-cream">
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-ink-soft">Date</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-ink-soft">Amount</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-ink-soft">Allocated</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-ink-soft">Unallocated</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-ink-soft">Note</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-ink-soft">Attachment</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($payouts as $payout)
                <tr class="border-b border-cream-2 last:border-b-0 hover:bg-cream/60 transition-colors duration-100">
                    <td class="px-4 py-3.5 text-sm font-medium text-ink whitespace-nowrap">
                        {{ $payout->paid_at->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3.5 text-right font-mono text-sm font-medium text-ink">
                        {{ number_format($payout->amount, 2) }}
                    </td>
                    <td class="px-4 py-3.5 text-right font-mono text-sm font-medium text-emerald">
                        {{ number_format($payout->allocated_total, 2) }}
                    </td>
                    <td class="px-4 py-3.5 text-right font-mono text-sm font-medium {{ $payout->unallocated > 0 ? 'text-amber' : 'text-ink-soft' }}">
                        {{ number_format($payout->unallocated, 2) }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-ink-soft max-w-[180px] truncate">
                        {{ Str::limit($payout->note, 50) }}
                    </td>
                    <td class="px-4 py-3.5">
                        @if($payout->hasAttachment())
                        <a href="{{ route('payouts.attachment', $payout) }}"
                           class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-cream-2 text-ink-soft hover:bg-cream-3 hover:text-ink no-underline transition-colors border border-border max-w-[150px]"
                           target="_blank">
                            <span class="shrink-0">📎</span>
                            <span class="truncate">{{ Str::limit($payout->attachment_name, 18) }}</span>
                        </a>
                        @else
                        <span class="text-ink-soft text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <a href="{{ route('payouts.show', $payout) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all">
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 border-t border-border">
        {{ $payouts->links() }}
    </div>
    @endif
</div>

@endsection
