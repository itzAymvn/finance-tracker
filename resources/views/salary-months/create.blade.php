@extends('layouts.app')
@section('title', 'Add Salary Month')
@section('content')

	<div class="max-w-xl mx-auto">
		<div class="flex items-center justify-between mb-8">
			<div>
				<p class="text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1">Salary Months</p>
				<h1 class="font-serif text-3xl text-ink leading-tight">Add Month</h1>
			</div>
			<a
				class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-ink-soft bg-white border border-border hover:bg-cream-2 hover:text-ink no-underline transition-all shadow-sm"
				href="{{ route('dashboard') }}">
				← Back
			</a>
		</div>

		{{-- Tabs --}}
		<div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
			<div class="flex border-b border-border bg-cream px-2 pt-2 gap-1" id="tab-bar">
				<button
					class="tab-btn px-5 py-2.5 rounded-t-lg text-sm font-semibold transition-all duration-150 border border-transparent
                           active-tab bg-white border-b-white text-ink shadow-sm -mb-px"
					data-tab="single">
					Single Month
				</button>
				<button
					class="tab-btn px-5 py-2.5 rounded-t-lg text-sm font-semibold transition-all duration-150 border border-transparent
                           text-ink-soft hover:text-ink hover:bg-white/60 -mb-px"
					data-tab="period">
					Period
				</button>
			</div>

			{{-- Single month --}}
			<div class="tab-pane p-6 space-y-5" id="pane-single">
				<form action="{{ route('salary-months.store') }}" method="POST">
					@csrf

					<div class="space-y-5">
						<div>
							<label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
								Month <span class="text-amber">*</span>
							</label>
							<input
								class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                      {{ $errors->has('month_key') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}"
								name="month_key" type="month" value="{{ old('month_key', now()->format('Y-m')) }}">
							@error('month_key')
								<p class="mt-1 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
							@enderror
						</div>

						<div class="grid grid-cols-3 gap-3">
							<div class="col-span-2">
								<label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
									Expected Salary <span class="text-amber">*</span>
								</label>
								<input
									class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                          {{ $errors->has('expected_salary') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}"
									name="expected_salary" type="number" value="{{ old('expected_salary') }}" placeholder="4000.00" min="0.01"
									step="0.01">
								@error('expected_salary')
									<p class="mt-1 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
								@enderror
							</div>
							<div>
								<label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
									Currency <span class="text-amber">*</span>
								</label>
								<input
									class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                          {{ $errors->has('currency') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}"
									name="currency" type="text" value="{{ old('currency', 'MAD') }}" maxlength="10">
								@error('currency')
									<p class="mt-1 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
								@enderror
							</div>
						</div>

						<div>
							<label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">Notes</label>
							<textarea
							 class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink outline-none resize-y transition-all
                                         {{ $errors->has('notes') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}"
							 name="notes" rows="2" placeholder="Optional notes...">{{ old('notes') }}</textarea>
							@error('notes')
								<p class="mt-1 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
							@enderror
						</div>

						<button
							class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-ink hover:bg-ink-dim transition-all shadow-sm"
							type="submit">
							Save Month
						</button>
					</div>
				</form>
			</div>

			{{-- Period --}}
			<div class="tab-pane p-6 space-y-5 hidden" id="pane-period">
				<p class="text-sm text-ink-soft bg-amber-bg border border-amber/20 rounded-lg px-4 py-3">
					Creates one salary month for every month in the range. Months that already exist are skipped.
				</p>

				<form action="{{ route('salary-months.storePeriod') }}" method="POST">
					@csrf

					<div class="space-y-5">
						<div class="grid grid-cols-2 gap-3">
							<div>
								<label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
									From <span class="text-amber">*</span>
								</label>
								<input
									class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                          {{ $errors->has('from_month') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}"
									name="from_month" type="month" value="{{ old('from_month') }}">
								@error('from_month')
									<p class="mt-1 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
								@enderror
							</div>
							<div>
								<label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
									To <span class="text-amber">*</span>
								</label>
								<input
									class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                          {{ $errors->has('to_month') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}"
									name="to_month" type="month" value="{{ old('to_month') }}">
								@error('to_month')
									<p class="mt-1 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
								@enderror
							</div>
						</div>

						<div class="grid grid-cols-3 gap-3">
							<div class="col-span-2">
								<label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
									Expected Salary <span class="text-amber">*</span>
								</label>
								<input
									class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                          {{ $errors->has('expected_salary') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}"
									name="expected_salary" type="number" value="{{ old('expected_salary') }}" placeholder="4000.00" min="0.01"
									step="0.01">
								<p class="mt-1.5 text-xs text-ink-soft">Applied to every month in range.</p>
								@error('expected_salary')
									<p class="mt-1 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
								@enderror
							</div>
							<div>
								<label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">
									Currency <span class="text-amber">*</span>
								</label>
								<input
									class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono outline-none transition-all
                                          {{ $errors->has('currency') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}"
									name="currency" type="text" value="{{ old('currency', 'MAD') }}" maxlength="10">
								@error('currency')
									<p class="mt-1 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
								@enderror
							</div>
						</div>

						<div>
							<label class="block text-xs font-semibold tracking-widest uppercase text-ink-soft mb-1.5">Notes</label>
							<textarea
							 class="w-full bg-white border rounded-lg px-3.5 py-2.5 text-sm text-ink outline-none resize-y transition-all
                                         {{ $errors->has('notes') ? 'border-ruby focus:ring-2 focus:ring-ruby/20' : 'border-border focus:border-amber focus:ring-2 focus:ring-amber/15' }}"
							 name="notes" rows="2" placeholder="Optional — applied to every month in the range.">{{ old('notes') }}</textarea>
							@error('notes')
								<p class="mt-1 text-xs text-ruby flex items-center gap-1"><span>⚠</span> {{ $message }}</p>
							@enderror
						</div>

						<button
							class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-ink hover:bg-ink-dim transition-all shadow-sm"
							type="submit">
							Create Period
						</button>
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
				btn.classList.toggle('active-tab', isActive);
				btn.classList.toggle('bg-white', isActive);
				btn.classList.toggle('border-border', isActive);
				btn.classList.toggle('text-ink', isActive);
				btn.classList.toggle('shadow-sm', isActive);
				btn.classList.toggle('-mb-px', isActive);
				btn.classList.toggle('text-ink-soft', !isActive);
				btn.classList.toggle('hover:text-ink', !isActive);
				btn.classList.toggle('hover:bg-white/60', !isActive);
			});
			tabPanes.forEach(pane => {
				pane.classList.toggle('hidden', !pane.id.endsWith(tabName));
			});
		}

		tabBtns.forEach(btn => {
			btn.addEventListener('click', () => switchTab(btn.dataset.tab));
		});

		// Re-open period tab on validation errors from it
		@if ($errors->has('from_month') || $errors->has('to_month'))
			document.addEventListener('DOMContentLoaded', () => switchTab('period'));
		@endif
	</script>
@endsection
