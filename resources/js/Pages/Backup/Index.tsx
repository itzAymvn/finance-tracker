import { router, usePage } from '@inertiajs/react';
import { Download, Trash2, Clock, HardDrive, Upload, Database } from 'lucide-react';
import type { BackupIndexProps } from '@/lib/types';
import { formatSize, relativeTime, formatDateTime } from '@/lib/format';
import { AppLayout } from '@/components/AppLayout';
import { BackupSettingsForm } from '@/components/BackupSettingsForm';
import { BackupRestoreForm } from '@/components/BackupRestoreForm';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

export default function BackupIndex() {
    const { backups, settings, nextRun } = usePage<BackupIndexProps>().props;

    const autoCount = backups.filter((b) => b.kind === 'auto').length;
    const manualCount = backups.filter((b) => b.kind === 'manual').length;
    const totalSize = backups.reduce((sum, b) => sum + b.size, 0);
    const latest = backups[0];

    function parseBackupDate(name: string): Date {
        const match = name.match(/(?:backup|auto)-(\d{4})-(\d{2})-(\d{2})-(\d{2})(\d{2})(\d{2})/);
        if (match) {
            return new Date(parseInt(match[1]), parseInt(match[2]) - 1, parseInt(match[3]), parseInt(match[4]), parseInt(match[5]), parseInt(match[6]));
        }
        return new Date();
    }

    function handleDelete(name: string) {
        if (!confirm(`Delete ${name}? This cannot be undone.`)) return;
        router.delete(`/backup/${encodeURIComponent(name)}`);
    }

    return (
        <>
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">Backup &amp; Restore</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">Snapshots, automatic backups, and disaster recovery</p>
                </div>
                <form onSubmit={(e) => { e.preventDefault(); router.post('/backup/export'); }}>
                    <Button type="submit">
                        <Download className="w-4 h-4" />
                        New Snapshot
                    </Button>
                </form>
            </div>

            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <Card>
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-2">Total Backups</p>
                        <p className="font-mono text-2xl font-semibold text-foreground dark:text-white">{backups.length}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-2">Automatic</p>
                        <p className="font-mono text-2xl font-semibold text-sky-600 dark:text-sky-400">{autoCount}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-2">Manual</p>
                        <p className="font-mono text-2xl font-semibold text-amber-600-600 dark:text-amber-600-400">{manualCount}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-2">Disk Used</p>
                        <p className="font-mono text-2xl font-semibold text-foreground dark:text-white">{formatSize(totalSize)}</p>
                    </CardContent>
                </Card>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-2 bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm overflow-hidden flex flex-col">
                    <div className="px-6 py-4 border-b border-border dark:border-border flex items-center justify-between gap-3">
                        <div className="flex items-center gap-3">
                            <div className="w-9 h-9 rounded-lg bg-primary/10 text-primary dark:text-primary flex items-center justify-center">
                                <Database className="w-4 h-4" />
                            </div>
                            <div>
                                <h2 className="text-base font-semibold text-foreground dark:text-white">Snapshots</h2>
                                {latest && (
                                    <p className="text-xs text-muted-foreground dark:text-slate-400">Latest: {relativeTime(latest.last_modified)}</p>
                                )}
                            </div>
                        </div>
                        {backups.length > 0 && (
                            <span className="text-xs text-muted-foreground dark:text-slate-400 hidden sm:block">{backups.length} file(s)</span>
                        )}
                    </div>

                    {backups.length === 0 ? (
                        <div className="text-center py-16 px-6">
                            <div className="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-3xl opacity-50">
                                <Database className="w-8 h-8 text-muted-foreground dark:text-slate-500" />
                            </div>
                            <p className="text-sm font-medium text-foreground dark:text-white">No snapshots yet</p>
                            <p className="text-xs text-muted-foreground dark:text-slate-400 mt-1">Create one manually or enable automatic backups.</p>
                        </div>
                    ) : (
                        <div className="divide-y divide-border dark:divide-border-dark max-h-[460px] overflow-y-auto">
                            {backups.map((backup) => {
                                const dt = parseBackupDate(backup.name);
                                const isAuto = backup.kind === 'auto';
                                return (
                                    <div key={backup.name} className="flex items-center gap-3 px-6 py-3.5 hover:bg-muted dark:hover:bg-muted transition-colors">
                                        <div className={`w-9 h-9 rounded-lg flex items-center justify-center shrink-0 ${isAuto ? 'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600-600 dark:text-amber-600-400'}`}>
                                            {isAuto ? <Clock className="w-4 h-4" /> : <HardDrive className="w-4 h-4" />}
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <div className="flex items-baseline gap-2 flex-wrap">
                                                <span className="text-sm font-medium text-foreground dark:text-white">
                                                    {dt.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                                </span>
                                                <span className="font-mono text-[11px] text-muted-foreground dark:text-slate-400">{(backup.size / 1024).toFixed(1)} KB</span>
                                                <span className={`text-[10px] font-semibold uppercase tracking-wider ${isAuto ? 'text-sky-600 dark:text-sky-400' : 'text-amber-600-600 dark:text-amber-600-400'}`}>
                                                    {isAuto ? 'Auto' : 'Manual'}
                                                </span>
                                            </div>
                                            <p className="font-mono text-[11px] text-muted-foreground dark:text-slate-400 truncate">{backup.name}</p>
                                        </div>
                                        <div className="flex items-center gap-1 shrink-0">
                                            <a
                                                href={`/backup/download/${encodeURIComponent(backup.name)}`}
                                                className="p-2 rounded-lg text-muted-foreground dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-foreground dark:hover:text-white transition-colors"
                                                title="Download"
                                            >
                                                <Download className="w-4 h-4" />
                                            </a>
                                            <button
                                                onClick={() => handleDelete(backup.name)}
                                                className="p-2 rounded-lg text-muted-foreground dark:text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 hover:text-rose-600 dark:hover:text-rose-400 transition-colors"
                                                title="Delete"
                                            >
                                                <Trash2 className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>

                <div className="space-y-6">
                    <div className="bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm overflow-hidden">
                        <div className="px-6 py-4 border-b border-border dark:border-border flex items-center gap-3">
                            <div className={`w-9 h-9 rounded-lg ${settings.backup_enabled ? 'bg-emerald/10 text-emerald-600 dark:text-emerald-600-400' : 'bg-slate-100 dark:bg-slate-800 text-muted-foreground dark:text-slate-400'} flex items-center justify-center`}>
                                <Clock className="w-4 h-4" />
                            </div>
                            <div>
                                <h2 className="text-base font-semibold text-foreground dark:text-white">Automation</h2>
                                <p className="text-xs text-muted-foreground dark:text-slate-400">
                                    {settings.backup_enabled
                                        ? `Active · every ${settings.backup_interval_hours}h`
                                        : 'Disabled'}
                                </p>
                            </div>
                        </div>
                        <div className="p-6">
                            <BackupSettingsForm settings={settings} />
                            {settings.backup_enabled && nextRun && (
                                <div className="flex items-center gap-2 text-xs text-muted-foreground dark:text-slate-400 px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-border dark:border-border mt-4">
                                    <svg className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-600-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Next: <span className="font-medium text-foreground dark:text-white">{formatDateTime(nextRun)}</span> · {relativeTime(Math.floor(new Date(nextRun).getTime() / 1000))}</span>
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm overflow-hidden">
                        <div className="px-6 py-4 border-b border-border dark:border-border flex items-center gap-3">
                            <div className="w-9 h-9 rounded-lg bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                                <Upload className="w-4 h-4" />
                            </div>
                            <div>
                                <h2 className="text-base font-semibold text-foreground dark:text-white">Restore</h2>
                                <p className="text-xs text-muted-foreground dark:text-slate-400">Replaces all current data</p>
                            </div>
                        </div>
                        <div className="p-6">
                            <BackupRestoreForm />
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
(BackupIndex as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
