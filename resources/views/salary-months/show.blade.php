@extends('layouts.app')
@section('title', $salaryMonth->label)
@section('content')

<div class="flex items-end justify-between mb-8">
    <div>
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1">Salary Month</p>
        <h1 class="font-serif text-3xl text-ink leading-tight">{{ $salaryMonth->label }}</h1>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('salary-months.edit', $salaryMonth) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all shadow-sm">
            Edit
        </a>
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all shadow-sm">
            ← Dashboard
        </a>
    </div>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-2xl border border-border p-5 shadow-sm relative overflow-hidden">
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Expected</p>
        <p class="font-mono text-xl font-medium text-ink">{{ number_format($salaryMonth->expected_salary, 2) }}</p>
        <p class="text-xs text-ink-soft mt-0.5">{{ $salaryMonth->currency }}</p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-border"></div>
    </div>
    <div class="bg-white rounded-2xl border border-border p-5 shadow-sm relative overflow-hidden">
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Paid</p>
        <p class="font-mono text-xl font-medium text-emerald">{{ number_format($salaryMonth->total_paid, 2) }}</p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-emerald"></div>
    </div>
    <div class="bg-white rounded-2xl border border-border p-5 shadow-sm relative overflow-hidden">
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Remaining</p>
        <p class="font-mono text-xl font-medium {{ $salaryMonth->remaining > 0 ? 'text-ruby' : 'text-ink-soft' }}">{{ number_format($salaryMonth->remaining, 2) }}</p>
        <div class="absolute bottom-0 left-0 right-0 h-[3px] {{ $salaryMonth->remaining > 0 ? 'bg-ruby' : 'bg-emerald' }}"></div>
    </div>
    <div class="bg-white rounded-2xl border border-border p-5 shadow-sm relative overflow-hidden">
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Status</p>
        @php $s = $salaryMonth->status; @endphp
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold tracking-wider uppercase
            {{ $s === 'paid'     ? 'bg-emerald-lt text-emerald' : '' }}
            {{ $s === 'overpaid' ? 'bg-sapphire-lt text-sapphire' : '' }}
            {{ $s === 'partial'  ? 'bg-amber-bg text-amber' : '' }}
            {{ $s === 'unpaid'   ? 'bg-cream-2 text-ink-soft' : '' }}">
            {{ ucfirst($s) }}
        </span>
        <div class="absolute bottom-0 left-0 right-0 h-[3px]
            {{ $s === 'paid'     ? 'bg-emerald' : '' }}
            {{ $s === 'overpaid' ? 'bg-sapphire' : '' }}
            {{ $s === 'partial'  ? 'bg-amber' : '' }}
            {{ $s === 'unpaid'   ? 'bg-border' : '' }}">
        </div>
    </div>
</div>

{{-- Progress --}}
<div class="bg-white rounded-2xl border border-border shadow-sm p-5 mb-4">
    <div class="flex items-center justify-between mb-2">
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft">Progress</p>
        <span class="font-mono text-sm font-medium text-ink-soft">{{ $salaryMonth->progress_percent }}%</span>
    </div>
    <div class="h-2 bg-cream-2 rounded-full overflow-hidden">
        <div class="h-full rounded-full transition-all duration-700
                    {{ $salaryMonth->status === 'paid' || $salaryMonth->status === 'overpaid' ? 'bg-emerald' : ($salaryMonth->progress_percent >= 50 ? 'bg-amber' : 'bg-ink') }}"
             style="width:{{ min($salaryMonth->progress_percent, 100) }}%"></div>
    </div>
</div>

{{-- Notes --}}
@if($salaryMonth->notes)
<div class="bg-white rounded-2xl border border-border shadow-sm p-5 mb-4">
    <p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Notes</p>
    <p class="text-sm text-ink leading-relaxed">{{ $salaryMonth->notes }}</p>
</div>
@endif

{{-- Payout allocations --}}
<div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
    <div class="flex items-center px-6 py-4 bg-cream border-b border-border">
        <h2 class="font-serif text-lg text-ink">Payout Allocations</h2>
    </div>

    @if($salaryMonth->allocations->isEmpty())
    <div class="text-center py-12 px-6">
        <div class="text-3xl mb-3 opacity-30">📭</div>
        <p class="text-sm text-ink-soft">No payouts allocated to this month yet.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b border-border bg-cream">
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-ink-soft">Date</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-ink-soft">Allocated</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-ink-soft">Payout Total</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-ink-soft">Note</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($salaryMonth->allocations->sortByDesc(fn($a) => $a->payout->paid_at) as $allocation)
                <tr class="border-b border-cream-2 last:border-b-0 hover:bg-cream/60 transition-colors duration-100">
                    <td class="px-4 py-3.5 text-sm font-medium text-ink whitespace-nowrap">
                        {{ $allocation->payout->paid_at->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3.5 text-right font-mono text-sm font-semibold text-emerald">
                        {{ number_format($allocation->amount, 2) }}
                        <span class="text-xs text-ink-soft ml-1">{{ $salaryMonth->currency }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-right font-mono text-sm text-ink-soft">
                        {{ number_format($allocation->payout->amount, 2) }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-ink-soft max-w-[200px] truncate">
                        {{ Str::limit($allocation->payout->note, 60) }}
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <a href="{{ route('payouts.show', $allocation->payout) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all">
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
