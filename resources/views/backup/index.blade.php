@extends('layouts.app')
@section('title', 'Backup')
@section('content')

<div class="page-header">
    <div>
        <h1>Backup &amp; Restore</h1>
        <p>Export your data or restore from a previous backup</p>
    </div>
    <form action="{{ route('backup.export') }}" method="POST">
        @csrf
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
            Create Backup
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Backup history --}}
    <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-border dark:border-border-dark">
            <h2 class="text-lg font-semibold text-ink dark:text-white">Saved Backups</h2>
        </div>

        @if($backups->isEmpty())
        <div class="text-center py-12 px-6">
            <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-2xl opacity-50">&#x1F4BE;</div>
            <p class="text-sm text-ink-soft dark:text-slate-400">No backups yet.</p>
        </div>
        @else
        <div class="divide-y divide-border dark:divide-border-dark">
            @foreach($backups as $backup)
            <div class="flex items-center justify-between px-6 py-3.5">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-ink dark:text-white truncate">{{ $backup['name'] }}</p>
                    <p class="font-mono text-xs text-ink-soft dark:text-slate-400 mt-0.5">
                        {{ number_format($backup['size'] / 1024, 1) }} KB &middot;
                        @php
                            preg_match('/backup-(\d{4})-(\d{2})-(\d{2})-(\d{2})(\d{2})(\d{2})/', $backup['name'], $m);
                            $dt = $m ? "{$m[1]}-{$m[2]}-{$m[3]} {$m[4]}:{$m[5]}:{$m[6]}" : \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->format('Y-m-d H:i');
                        @endphp
                        {{ $dt }}
                    </p>
                </div>
                <a href="{{ route('backup.download', $backup['name']) }}" class="btn-ghost text-xs py-1.5 px-3 shrink-0 ml-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                    Download
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Restore --}}
    <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-border dark:border-border-dark">
            <h2 class="text-lg font-semibold text-ink dark:text-white">Restore from File</h2>
        </div>
        <div class="p-6">
            <p class="text-sm text-ink-soft dark:text-slate-400 mb-4">Upload a previously exported <code>.json</code> backup file. This will replace all current data.</p>
            <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <input type="file" name="file" accept=".json" required
                           class="input-field text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-primary file:text-white file:cursor-pointer">
                    <button type="submit" class="btn-danger w-full" onclick="return confirm('This will replace all existing data. Are you sure?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Restore from File
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
