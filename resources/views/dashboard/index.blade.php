@extends('layouts.app')
@section('title', 'Overview')
@section('content')

	{{-- Page header --}}
	<div class="flex items-end justify-between mb-8">
		<div>
			<p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1">Salary Tracker</p>
			<h1 class="font-serif text-3xl text-ink leading-tight">Overview</h1>
		</div>
		<div class="flex gap-2">
			<a
				class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all duration-150 shadow-sm"
				href="{{ route('salary-months.create') }}">
				+ Month
			</a>
			<a
				class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white bg-ink hover:bg-ink-dim no-underline transition-all duration-150 shadow-sm"
				href="{{ route('payouts.create') }}">
				+ Payout
			</a>
		</div>
	</div>

	{{-- Stats --}}
	<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-10">
		<div
			class="bg-white rounded-2xl border border-border p-5 shadow-sm relative overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
			<p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Total Expected</p>
			<p class="font-mono text-2xl font-medium text-ink mb-3">{{ number_format($totalExpected, 2) }}</p>
			<div class="pt-3 border-t border-border">
				<p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Up to
					{{ \Carbon\Carbon::parse($currentMonthKey . '-01')->translatedFormat('M Y') }}</p>
				<p class="font-mono text-lg font-medium text-ink">{{ number_format($toDateExpected, 2) }}</p>
			</div>
			<div class="absolute bottom-0 left-0 right-0 h-[3px] bg-border"></div>
		</div>
		<div
			class="bg-white rounded-2xl border border-border p-5 shadow-sm relative overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
			<p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Total Paid</p>
			<p class="font-mono text-2xl font-medium text-emerald mb-3">{{ number_format($totalPaid, 2) }}</p>
			<div class="pt-3 border-t border-border">
				<p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Up to
					{{ \Carbon\Carbon::parse($currentMonthKey . '-01')->translatedFormat('M Y') }}</p>
				<p class="font-mono text-lg font-medium text-emerald">{{ number_format($toDatePaid, 2) }}</p>
			</div>
			<div class="absolute bottom-0 left-0 right-0 h-[3px] bg-emerald"></div>
		</div>
		<div
			class="bg-white rounded-2xl border border-border p-5 shadow-sm relative overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
			<p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Total Remaining</p>
			<p class="font-mono text-2xl font-medium {{ $totalRemaining > 0 ? 'text-ruby' : 'text-ink-soft' }} mb-3">
				{{ number_format($totalRemaining, 2) }}</p>
			<div class="pt-3 border-t border-border">
				<p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-2">Up to
					{{ \Carbon\Carbon::parse($currentMonthKey . '-01')->translatedFormat('M Y') }}</p>
				<p class="font-mono text-lg font-medium {{ $toDateRemaining > 0 ? 'text-ruby' : 'text-ink-soft' }}">
					{{ number_format($toDateRemaining, 2) }}</p>
			</div>
			<div class="absolute bottom-0 left-0 right-0 h-[3px] {{ $totalRemaining > 0 ? 'bg-ruby' : 'bg-emerald' }}"></div>
		</div>
	</div>

	{{-- Salary months table --}}
	<div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
		<div
			class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-6 py-4 bg-cream border-b border-border">
			<h2 class="font-serif text-lg text-ink">Salary Months</h2>
			<div class="flex flex-wrap items-center gap-3">
				<form class="flex flex-wrap items-center gap-2" method="GET" action="{{ route('dashboard') }}">
					<select
						class="text-sm rounded-lg border border-border bg-white px-3 py-1.5 text-ink-soft focus:outline-none focus:ring-2 focus:ring-amber/20 focus:border-amber"
						name="status">
						<option value="">All statuses</option>
						<option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
						<option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
						<option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
						<option value="overpaid" {{ request('status') === 'overpaid' ? 'selected' : '' }}>Overpaid</option>
					</select>
					<select
						class="text-sm rounded-lg border border-border bg-white px-3 py-1.5 text-ink-soft focus:outline-none focus:ring-2 focus:ring-amber/20 focus:border-amber"
						name="year">
						<option value="">All years</option>
						@foreach ($years ?? [] as $y)
							<option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
						@endforeach
					</select>
					<input
						class="text-sm rounded-lg border border-border bg-white px-3 py-1.5 text-ink font-mono focus:outline-none focus:ring-2 focus:ring-amber/20 focus:border-amber"
						name="from" type="month" value="{{ request('from') }}" title="From month" placeholder="From">
					<span class="text-ink-soft text-xs">–</span>
					<input
						class="text-sm rounded-lg border border-border bg-white px-3 py-1.5 text-ink font-mono focus:outline-none focus:ring-2 focus:ring-amber/20 focus:border-amber"
						name="to" type="month" value="{{ request('to') }}" title="To month" placeholder="To">
					<button class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-ink text-white hover:bg-ink-dim transition-colors"
						type="submit">Filter</button>
					@if (request()->hasAny(['status', 'year', 'from', 'to']))
						<a class="px-3 py-1.5 rounded-lg text-xs font-medium text-ink-soft hover:text-ink transition-colors"
							href="{{ route('dashboard') }}">Clear</a>
					@endif
				</form>
				<a
					class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold tracking-wider uppercase text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all shadow-sm"
					href="{{ route('salary-months.create') }}">
					+ Add Month
				</a>
			</div>
		</div>

		@if ($months->isEmpty())
			<div class="text-center py-16 px-6">
				<div class="text-4xl mb-3 opacity-30">🗓</div>
				<p class="text-sm text-ink-soft mb-4">No salary months yet.</p>
				<a
					class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white bg-ink hover:bg-ink-dim no-underline transition-all shadow-sm"
					href="{{ route('salary-months.create') }}">
					Create first month
				</a>
			</div>
		@else
			<div class="overflow-x-auto">
				<table class="w-full border-collapse">
					<thead>
						<tr class="border-b border-border bg-cream">
							<th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-ink-soft">Month</th>
							<th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-ink-soft">Expected</th>
							<th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-ink-soft">Paid</th>
							<th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-ink-soft">Remaining</th>
							<th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-ink-soft">Progress</th>
							<th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-ink-soft">Status</th>
							<th class="px-4 py-3"></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($months as $month)
							<tr class="border-b border-cream-2 last:border-b-0 hover:bg-cream/60 transition-colors duration-100">
								<td class="px-4 py-3.5">
									<a class="font-medium text-ink hover:text-amber no-underline transition-colors text-sm"
										href="{{ route('salary-months.show', $month) }}">
										{{ $month->label }}
									</a>
								</td>
								<td class="px-4 py-3.5 text-right">
									<span class="font-mono text-sm font-medium text-ink">{{ number_format($month->expected_salary, 2) }}</span>
									<span class="text-xs text-ink-soft ml-1">{{ $month->currency }}</span>
								</td>
								<td class="px-4 py-3.5 text-right font-mono text-sm font-medium text-emerald">
									{{ number_format($month->total_paid, 2) }}</td>
								<td
									class="px-4 py-3.5 text-right font-mono text-sm font-medium {{ $month->remaining > 0 ? 'text-ruby' : 'text-ink-soft' }}">
									{{ number_format($month->remaining, 2) }}
								</td>
								<td class="px-4 py-3.5" style="min-width:130px">
									<div class="h-1.5 bg-cream-2 rounded-full overflow-hidden mb-1">
										<div
											class="h-full rounded-full transition-all duration-500
                                        {{ $month->status === 'paid' || $month->status === 'overpaid' ? 'bg-emerald' : ($month->progress_percent >= 50 ? 'bg-amber' : 'bg-ink') }}"
											style="width:{{ min($month->progress_percent, 100) }}%"></div>
									</div>
									<span class="font-mono text-xs text-ink-soft">{{ $month->progress_percent }}%</span>
								</td>
								<td class="px-4 py-3.5">
									@php $s = $month->status; @endphp
									<span
										class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold tracking-wider uppercase
                            {{ $s === 'paid' ? 'bg-emerald-lt text-emerald' : '' }}
                            {{ $s === 'overpaid' ? 'bg-sapphire-lt text-sapphire' : '' }}
                            {{ $s === 'partial' ? 'bg-amber-bg text-amber' : '' }}
                            {{ $s === 'unpaid' ? 'bg-cream-2 text-ink-soft' : '' }}">
										{{ ucfirst($s) }}
									</span>
								</td>
								<td class="px-4 py-3.5 text-right whitespace-nowrap">
									<a
										class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all mr-1"
										href="{{ route('salary-months.show', $month) }}">
										View
									</a>
									<a
										class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all"
										href="{{ route('salary-months.edit', $month) }}">
										Edit
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
