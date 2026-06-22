@extends('layouts.app')
@section('title', 'Backup')
@section('content')

<div class="page-header">
    <div>
        <h1>Backup &amp; Restore</h1>
        <p>Snapshots, automatic backups, and disaster recovery</p>
    </div>
    <form action="{{ route('backup.export') }}" method="POST">
        @csrf
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
            New Snapshot
        </button>
    </form>
</div>

@php
    $autoCount = $backups->where('kind', 'auto')->count();
    $manualCount = $backups->where('kind', 'manual')->count();
    $totalSize = $backups->sum('size');
    $latest = $backups->first();
@endphp

{{-- Stats strip --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Total Backups</p>
        <p class="font-mono text-2xl font-semibold text-ink dark:text-white">{{ $backups->count() }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Automatic</p>
        <p class="font-mono text-2xl font-semibold text-sky-600 dark:text-sky-400">{{ $autoCount }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Manual</p>
        <p class="font-mono text-2xl font-semibold text-amber-600 dark:text-amber-400">{{ $manualCount }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Disk Used</p>
        <p class="font-mono text-2xl font-semibold text-ink dark:text-white">{{ $totalSize >= 1048576 ? number_format($totalSize / 1048576, 1).' MB' : number_format($totalSize / 1024, 1).' KB' }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Backup list --}}
    <div class="lg:col-span-2 bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-border dark:border-border-dark flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary dark:text-primary-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10a2 2 0 002 2h12a2 2 0 002-2V7M4 7l4-4h8l4 4M4 7h16M8 11h8"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-ink dark:text-white">Snapshots</h2>
                    @if($latest)
                    <p class="text-xs text-ink-soft dark:text-slate-400">Latest: {{ \Carbon\Carbon::createFromTimestamp($latest['last_modified'])->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
            @if($backups->isNotEmpty())
            <span class="text-xs text-ink-soft dark:text-slate-400 hidden sm:block">{{ $backups->count() }} file(s)</span>
            @endif
        </div>

        @if($backups->isEmpty())
        <div class="text-center py-16 px-6">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-3xl opacity-50">
                <svg class="w-8 h-8 text-ink-soft dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10a2 2 0 002 2h12a2 2 0 002-2V7M4 7l4-4h8l4 4M4 7h16M8 11h8"/></svg>
            </div>
            <p class="text-sm font-medium text-ink dark:text-white">No snapshots yet</p>
            <p class="text-xs text-ink-soft dark:text-slate-400 mt-1">Create one manually or enable automatic backups.</p>
        </div>
        @else
        <div class="divide-y divide-border dark:divide-border-dark max-h-[460px] overflow-y-auto">
            @foreach($backups as $backup)
            @php
                preg_match('/(?:backup|auto)-(\d{4})-(\d{2})-(\d{2})-(\d{2})(\d{2})(\d{2})/', $backup['name'], $m);
                $dt = $m
                    ? \Carbon\Carbon::create($m[1], $m[2], $m[3], $m[4], $m[5], $m[6])
                    : \Carbon\Carbon::createFromTimestamp($backup['last_modified']);
                $isAuto = $backup['kind'] === 'auto';
            @endphp
            <div class="flex items-center gap-3 px-6 py-3.5 hover:bg-surface-hover dark:hover:bg-surface-dark-hover transition-colors">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $isAuto ? 'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($isAuto)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        @endif
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-baseline gap-2 flex-wrap">
                        <span class="text-sm font-medium text-ink dark:text-white">{{ $dt->format('j M Y, H:i') }}</span>
                        <span class="font-mono text-[11px] text-ink-soft dark:text-slate-400">{{ number_format($backup['size'] / 1024, 1) }} KB</span>
                        <span class="text-[10px] font-semibold uppercase tracking-wider {{ $isAuto ? 'text-sky-600 dark:text-sky-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $isAuto ? 'Auto' : 'Manual' }}</span>
                    </div>
                    <p class="font-mono text-[11px] text-ink-soft dark:text-slate-400 truncate">{{ $backup['name'] }}</p>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <a href="{{ route('backup.download', $backup['name']) }}" class="p-2 rounded-lg text-ink-soft dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-ink dark:hover:text-white transition-colors" title="Download" aria-label="Download">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                    </a>
                    <form action="{{ route('backup.delete', $backup['name']) }}" method="POST" onsubmit="return confirm('Delete {{ $backup['name'] }}? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 rounded-lg text-ink-soft dark:text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Delete" aria-label="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Right column: automation + restore --}}
    <div class="space-y-6">

        {{-- Automatic backups --}}
        <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-border dark:border-border-dark flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg {{ $settings['backup_enabled'] ? 'bg-emerald/10 text-emerald dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-ink-soft dark:text-slate-400' }} flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-ink dark:text-white">Automation</h2>
                    <p class="text-xs text-ink-soft dark:text-slate-400">
                        @if($settings['backup_enabled'])
                        Active &middot; every {{ $settings['backup_interval_hours'] }}h
                        @else
                        Disabled
                        @endif
                    </p>
                </div>
            </div>
            <div class="p-6">
                <form action="{{ route('backup.settings') }}" method="POST" class="space-y-5" id="autoForm">
                    @csrf

                    <label class="flex items-start gap-3 cursor-pointer p-3 -m-3 rounded-lg hover:bg-surface-hover dark:hover:bg-surface-dark-hover transition-colors">
                        <input type="hidden" name="backup_enabled" value="0">
                        <input type="checkbox" name="backup_enabled" value="1" id="backup_enabled"
                               class="mt-0.5 w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary shrink-0"
                               {{ $settings['backup_enabled'] ? 'checked' : '' }}>
                        <div>
                            <p class="text-sm font-medium text-ink dark:text-white">Enable automatic backups</p>
                            <p class="text-xs text-ink-soft dark:text-slate-400 mt-0.5">Snapshots are saved with an <code>auto-</code> prefix.</p>
                        </div>
                    </label>

                    <div>
                        <label for="backup_interval_hours" class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Interval</label>
                        <select id="backup_interval_hours" name="backup_interval_hours"
                                class="input-field text-sm py-2 w-full @if(!$settings['backup_enabled']) opacity-60 cursor-not-allowed @endif"
                                @if(!$settings['backup_enabled']) disabled @endif>
                            @php
                                $labels = [6 => 'Every 6 hours', 12 => 'Every 12 hours', 24 => 'Daily', 168 => 'Weekly'];
                            @endphp
                            @foreach($labels as $h => $lbl)
                            <option value="{{ $h }}" {{ $settings['backup_interval_hours'] === $h ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($settings['backup_enabled'] && $nextRun)
                    <div class="flex items-center gap-2 text-xs text-ink-soft dark:text-slate-400 px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-border dark:border-border-dark">
                        <svg class="w-3.5 h-3.5 text-emerald dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Next: <span class="font-medium text-ink dark:text-white">{{ $nextRun->format('j M, H:i') }}</span> &middot; {{ $nextRun->diffForHumans() }}</span>
                    </div>
                    @endif

                    <button type="submit" class="btn-primary w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Settings
                    </button>
                </form>
            </div>
        </div>

        {{-- Restore --}}
        <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-border dark:border-border-dark flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-ink dark:text-white">Restore</h2>
                    <p class="text-xs text-ink-soft dark:text-slate-400">Replaces all current data</p>
                </div>
            </div>
            <div class="p-6">
                <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="restoreFile" class="block cursor-pointer">
                        <span class="block text-xs font-semibold tracking-wider uppercase text-ink-soft dark:text-slate-400 mb-2">Backup file (.json)</span>
                        <div class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-dashed border-border dark:border-border-dark hover:border-primary dark:hover:border-primary-400 transition-colors text-sm text-ink-soft dark:text-slate-400">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <span id="restoreFileLabel">Choose a file…</span>
                            <input id="restoreFile" type="file" name="file" accept=".json" required class="hidden">
                        </div>
                    </label>
                    <button type="submit" class="btn-danger w-full mt-4" onclick="return confirm('This will REPLACE ALL current data with the uploaded backup. Are you absolutely sure?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Restore Now
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@section('scripts')
<script>
    // Sync disabled state of interval select with the toggle, and show the
    // file name when chosen — small UX touches that make the form feel right.
    const toggle = document.getElementById('backup_enabled');
    const interval = document.getElementById('backup_interval_hours');
    function syncIntervalState() {
        if (!toggle || !interval) return;
        interval.disabled = !toggle.checked;
        interval.classList.toggle('opacity-60', !toggle.checked);
        interval.classList.toggle('cursor-not-allowed', !toggle.checked);
    }
    toggle?.addEventListener('change', syncIntervalState);
    syncIntervalState();

    const fileInput = document.getElementById('restoreFile');
    const fileLabel = document.getElementById('restoreFileLabel');
    fileInput?.addEventListener('change', (e) => {
        const f = e.target.files?.[0];
        if (f) fileLabel.textContent = f.name;
    });
</script>
@endsection

@endsection
