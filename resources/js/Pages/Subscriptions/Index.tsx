import { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Plus, Pause, Play, XCircle, Pencil, Trash2, Repeat, ArrowUpCircle, ArrowDownCircle } from 'lucide-react';
import type { SubscriptionsIndexProps, Subscription, SubscriptionFrequency, SubscriptionStatus } from '@/lib/types';
import { formatMoney, formatDate, formatCountdown } from '@/lib/format';
import { useCountdown } from '@/lib/hooks';
import { getCategoryIcon } from '@/lib/icons';
import { useModals } from '@/contexts/ModalContext';
import { AppLayout } from '@/components/AppLayout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';

const frequencyLabels: Record<SubscriptionFrequency, string> = {
    weekly: 'Weekly',
    biweekly: 'Biweekly',
    monthly: 'Monthly',
    quarterly: 'Quarterly',
    yearly: 'Yearly',
};

function statusConfig(status: SubscriptionStatus) {
    switch (status) {
        case 'active':
            return { label: 'Active', className: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' };
        case 'paused':
            return { label: 'Paused', className: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' };
        case 'cancelled':
            return { label: 'Cancelled', className: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' };
    }
}

function NextDue({ iso, fallback }: { iso: string | null; fallback: string }) {
    const remaining = useCountdown(iso);
    if (remaining === null) return <>{fallback}</>;
    const overdue = remaining < 0;
    return (
        <span className={`font-mono tabular-nums ${overdue ? 'text-amber-600 dark:text-amber-400' : ''}`}>
            {overdue ? 'overdue ' : 'in '}{formatCountdown(remaining)}
        </span>
    );
}

export default function SubscriptionsIndex() {
    const { subscriptions, summary } = usePage<SubscriptionsIndexProps>().props;
    const modals = useModals();

    const [confirmAction, setConfirmAction] = useState<{ sub: Subscription; action: 'pause' | 'resume' | 'cancel' | 'delete' } | null>(null);

    function handleConfirm() {
        if (!confirmAction) return;
        const { sub, action } = confirmAction;

        const callbacks = {
            pause: () => router.post(`/subscriptions/${sub.id}/pause`, {}, { onFinish: () => setConfirmAction(null) }),
            resume: () => router.post(`/subscriptions/${sub.id}/resume`, {}, { onFinish: () => setConfirmAction(null) }),
            cancel: () => router.post(`/subscriptions/${sub.id}/cancel`, {}, { onFinish: () => setConfirmAction(null) }),
            delete: () => router.delete(`/subscriptions/${sub.id}`, { onFinish: () => setConfirmAction(null) }),
        };

        callbacks[action]();
    }

    function confirmTitle() {
        if (!confirmAction) return '';
        const { action, sub } = confirmAction;
        switch (action) {
            case 'pause': return `Pause "${sub.label}"?`;
            case 'resume': return `Resume "${sub.label}"?`;
            case 'cancel': return `Cancel "${sub.label}"?`;
            case 'delete': return `Delete "${sub.label}"?`;
        }
    }

    function confirmDescription() {
        if (!confirmAction) return '';
        const { action } = confirmAction;
        switch (action) {
            case 'pause': return 'No transactions will be generated while paused. You can resume later.';
            case 'resume': return 'Transactions will resume from where they left off. Missed periods are skipped.';
            case 'cancel': return 'This permanently stops transaction generation. You can delete the subscription afterward.';
            case 'delete': return 'This removes the subscription record. Previously generated transactions are kept.';
        }
    }

    return (
        <>
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">Subscriptions</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">Manage recurring transactions</p>
                </div>
                <Button onClick={() => modals.openSubscription()}>
                    <Plus className="w-4 h-4" />
                    New Subscription
                </Button>
            </div>

            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <Card>
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground mb-1.5">Active</p>
                        <p className="font-mono text-xl font-medium text-emerald-600">{summary.active}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground mb-1.5">Paused</p>
                        <p className="font-mono text-xl font-medium text-amber-600">{summary.paused}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground mb-1.5">Cancelled</p>
                        <p className="font-mono text-xl font-medium text-red-600">{summary.cancelled}</p>
                    </CardContent>
                </Card>
                <Card className="relative overflow-hidden">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground mb-1.5">Est. Monthly</p>
                        <p className="font-mono text-xl font-medium text-foreground">{formatMoney(summary.monthly_cost)}</p>
                    </CardContent>
                    <div className="absolute bottom-0 left-0 right-0 h-[3px] bg-primary"></div>
                </Card>
            </div>

            <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
                {subscriptions.length === 0 ? (
                    <div className="px-6 py-16 text-center">
                        <Repeat className="w-10 h-10 text-muted-foreground/40 mx-auto mb-3" />
                        <p className="text-sm text-muted-foreground">No subscriptions yet</p>
                        <p className="text-xs text-muted-foreground/70 mt-1">Create one to start generating recurring transactions</p>
                    </div>
                ) : (
                    <div className="divide-y divide-border">
                        {subscriptions.map((sub) => {
                            const status = statusConfig(sub.status);
                            const credit = parseFloat(sub.amount) >= 0;
                            const IconComp = sub.category ? getCategoryIcon(sub.category.icon) : null;

                            return (
                                <div key={sub.id} className="px-6 py-4 flex items-center gap-4 hover:bg-muted/30 transition-colors">
                                    <div className={`flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center ${credit ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-red-100 dark:bg-red-900/30'}`}>
                                        {credit ? (
                                            <ArrowUpCircle className="w-5 h-5 text-emerald-600" />
                                        ) : (
                                            <ArrowDownCircle className="w-5 h-5 text-red-600" />
                                        )}
                                    </div>

                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2">
                                            <p className="text-sm font-medium text-foreground truncate">{sub.label}</p>
                                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold tracking-wider uppercase ${status.className}`}>
                                                {status.label}
                                            </span>
                                        </div>
                                        <div className="flex items-center gap-2 mt-0.5">
                                            <span className="text-xs text-muted-foreground">{frequencyLabels[sub.frequency]}</span>
                                            {sub.category && (
                                                <>
                                                    <span className="text-xs text-muted-foreground/40">·</span>
                                                    <span className="flex items-center gap-1 text-xs text-muted-foreground">
                                                        {IconComp && <IconComp className="w-3 h-3" />}
                                                        {sub.category.name}
                                                    </span>
                                                </>
                                            )}
                                            {sub.next_due_at && (
                                                <>
                                                    <span className="text-xs text-muted-foreground/40">·</span>
                                                    <span className="text-xs text-muted-foreground">
                                                        Next <NextDue iso={sub.next_due_at} fallback={formatDate(sub.next_due_at, 'short')} />
                                                    </span>
                                                </>
                                            )}
                                        </div>
                                    </div>

                                    <div className="text-right flex-shrink-0">
                                        <p className={`font-mono text-sm font-semibold ${credit ? 'text-emerald-600' : 'text-red-600'}`}>
                                            {credit ? '+' : ''}{formatMoney(sub.amount)}
                                        </p>
                                        <p className="text-[10px] text-muted-foreground mt-0.5">
                                            {sub.transactions_count} tx{sub.transactions_count !== 1 ? '' : ''} generated
                                        </p>
                                    </div>

                                    <div className="flex items-center gap-1 flex-shrink-0">
                                        {sub.status === 'active' && (
                                            <>
                                                <Button
                                                    variant="ghost"
                                                    size="icon-sm"
                                                    title="Edit"
                                                    onClick={() => modals.openSubscription(sub)}
                                                >
                                                    <Pencil className="w-3.5 h-3.5" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon-sm"
                                                    title="Pause"
                                                    onClick={() => setConfirmAction({ sub, action: 'pause' })}
                                                >
                                                    <Pause className="w-3.5 h-3.5" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon-sm"
                                                    title="Cancel"
                                                    onClick={() => setConfirmAction({ sub, action: 'cancel' })}
                                                >
                                                    <XCircle className="w-3.5 h-3.5 text-muted-foreground" />
                                                </Button>
                                            </>
                                        )}
                                        {sub.status === 'paused' && (
                                            <>
                                                <Button
                                                    variant="ghost"
                                                    size="icon-sm"
                                                    title="Edit"
                                                    onClick={() => modals.openSubscription(sub)}
                                                >
                                                    <Pencil className="w-3.5 h-3.5" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon-sm"
                                                    title="Resume"
                                                    onClick={() => setConfirmAction({ sub, action: 'resume' })}
                                                >
                                                    <Play className="w-3.5 h-3.5 text-emerald-600" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon-sm"
                                                    title="Cancel"
                                                    onClick={() => setConfirmAction({ sub, action: 'cancel' })}
                                                >
                                                    <XCircle className="w-3.5 h-3.5 text-muted-foreground" />
                                                </Button>
                                            </>
                                        )}
                                        {sub.status === 'cancelled' && (
                                            <Button
                                                variant="ghost"
                                                size="icon-sm"
                                                title="Delete"
                                                onClick={() => setConfirmAction({ sub, action: 'delete' })}
                                            >
                                                <Trash2 className="w-3.5 h-3.5 text-red-500" />
                                            </Button>
                                        )}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>

            <AlertDialog open={!!confirmAction} onOpenChange={(open) => !open && setConfirmAction(null)}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>{confirmTitle()}</AlertDialogTitle>
                        <AlertDialogDescription>{confirmDescription()}</AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <Button
                            variant={confirmAction?.action === 'delete' ? 'destructive' : 'default'}
                            onClick={handleConfirm}
                        >
                            {confirmAction?.action === 'pause' && 'Pause'}
                            {confirmAction?.action === 'resume' && 'Resume'}
                            {confirmAction?.action === 'cancel' && 'Cancel Subscription'}
                            {confirmAction?.action === 'delete' && 'Delete'}
                        </Button>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
(SubscriptionsIndex as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
