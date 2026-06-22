import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, Edit } from 'lucide-react';
import type { SalaryMonthShowProps } from '@/lib/types';
import { formatMoney, formatDate, statusBadgeClass } from '@/lib/format';
import { useModals } from '@/contexts/ModalContext';
import { AppLayout } from '@/components/AppLayout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';

export default function SalaryMonthsShow() {
    const { salaryMonth, allocations } = usePage<SalaryMonthShowProps>().props;
    const modals = useModals();

    const s = salaryMonth.status;
    const statusBadge = statusBadgeClass(s);
    const statusAccentBar = s === 'paid' ? 'bg-emerald' : s === 'overpaid' ? 'bg-blue-500' : s === 'partial' ? 'bg-amber' : 'bg-border dark:bg-border-dark';

    const inMonthBarColor = s === 'paid' || s === 'overpaid'
        ? 'bg-emerald dark:bg-emerald-500'
        : salaryMonth.progress_percent >= 50
            ? 'bg-primary'
            : 'bg-slate-300 dark:bg-slate-600';

    return (
        <>
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">{salaryMonth.label}</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">Salary month details and progress</p>
                </div>
                <div className="flex gap-2">
                    <Button onClick={() => modals.openSalaryMonth(salaryMonth)}>
                        <Edit className="w-4 h-4" />
                        Edit
                    </Button>
                    <Button variant="ghost" render={<Link href="/dashboard" />}>
                        <ArrowLeft className="w-4 h-4" />
                        Dashboard
                    </Button>
                </div>
            </div>

            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <Card className="relative overflow-hidden">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-2">Expected</p>
                        <p className="font-mono text-xl font-medium text-foreground dark:text-white">{formatMoney(salaryMonth.expected_salary, salaryMonth.currency)}</p>
                        <p className="text-xs text-muted-foreground dark:text-slate-400 mt-0.5">{salaryMonth.currency}</p>
                    </CardContent>
                    <div className="absolute bottom-0 left-0 right-0 h-[3px] bg-border dark:bg-border-dark"></div>
                </Card>
                <Card className="relative overflow-hidden">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-2">Paid (FIFO)</p>
                        <p className="font-mono text-xl font-medium text-emerald-600 dark:text-emerald-600-400">{formatMoney(salaryMonth.total_paid)}</p>
                    </CardContent>
                    <div className="absolute bottom-0 left-0 right-0 h-[3px] bg-emerald"></div>
                </Card>
                <Card className="relative overflow-hidden">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-2">Remaining</p>
                        <p className={`font-mono text-xl font-medium ${salaryMonth.remaining > 0 ? 'text-red-600 dark:text-red-400' : 'text-muted-foreground dark:text-slate-400'}`}>{formatMoney(salaryMonth.remaining)}</p>
                    </CardContent>
                    <div className={`absolute bottom-0 left-0 right-0 h-[3px] ${salaryMonth.remaining > 0 ? 'bg-red-500' : 'bg-emerald'}`}></div>
                </Card>
                <Card className="relative overflow-hidden">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-2">Status</p>
                        <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold tracking-wider uppercase ${statusBadge}`}>
                            {s.charAt(0).toUpperCase() + s.slice(1)}
                        </span>
                    </CardContent>
                    <div className={`absolute bottom-0 left-0 right-0 h-[3px] ${statusAccentBar}`}></div>
                </Card>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                <Card>
                    <CardContent>
                        <div className="flex items-center justify-between mb-2">
                            <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400">Cumulative (FIFO)</p>
                            <span className="font-mono text-sm font-medium text-muted-foreground dark:text-slate-400">
                                {salaryMonth.cumulative_paid.toFixed(2)} / {salaryMonth.cumulative_due.toFixed(2)}
                                {' · '}{salaryMonth.cumulative_progress_percent}%
                            </span>
                        </div>
                        <div className="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div
                                className="h-full rounded-full transition-all duration-700 bg-blue-500 dark:bg-blue-500"
                                style={{ width: `${Math.min(salaryMonth.cumulative_progress_percent, 100)}%` }}
                            />
                        </div>
                        <p className="text-xs text-muted-foreground dark:text-slate-400 mt-2">
                            Cumulative status through {salaryMonth.month_key}: <span className="font-semibold">{salaryMonth.cumulative_status.charAt(0).toUpperCase() + salaryMonth.cumulative_status.slice(1)}</span>
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent>
                        <div className="flex items-center justify-between mb-2">
                            <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400">In-month Progress</p>
                            <span className="font-mono text-sm font-medium text-muted-foreground dark:text-slate-400">{salaryMonth.progress_percent}%</span>
                        </div>
                        <div className="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div
                                className={`h-full rounded-full transition-all duration-700 ${inMonthBarColor}`}
                                style={{ width: `${Math.min(salaryMonth.progress_percent, 100)}%` }}
                            />
                        </div>
                    </CardContent>
                </Card>
            </div>

            {salaryMonth.notes && (
                <Card className="mb-6">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-2">Notes</p>
                        <p className="text-sm text-foreground dark:text-white leading-relaxed">{salaryMonth.notes}</p>
                    </CardContent>
                </Card>
            )}

            <div className="bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm overflow-hidden">
                <div className="flex items-center justify-between px-6 py-4 border-b border-border dark:border-border">
                    <h2 className="text-lg font-semibold text-foreground dark:text-white">Salary Credits Allocated Here</h2>
                    <Link href="/transactions?type=salary" className="text-sm text-muted-foreground dark:text-slate-400 hover:text-primary transition-colors">All salary →</Link>
                </div>

                {allocations.length === 0 ? (
                    <div className="text-center py-12 px-6">
                        <div className="text-2xl mb-3 opacity-30">📭</div>
                        <p className="text-sm text-muted-foreground dark:text-slate-400">No salary allocations to this month.</p>
                        <p className="text-xs text-muted-foreground dark:text-slate-400 mt-1">
                            Tag transactions via the <Link href="/transactions" className="text-primary hover:underline">Transactions</Link> page.
                        </p>
                    </div>
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="px-5 py-3.5 text-xs font-semibold tracking-wider uppercase text-muted-foreground bg-muted">Date</TableHead>
                                <TableHead className="px-5 py-3.5 text-xs font-semibold tracking-wider uppercase text-muted-foreground bg-muted">Label</TableHead>
                                <TableHead className="px-5 py-3.5 text-xs font-semibold tracking-wider uppercase text-muted-foreground bg-muted text-right">Allocated</TableHead>
                                <TableHead className="px-5 py-3.5 text-xs font-semibold tracking-wider uppercase text-muted-foreground bg-muted text-right">Tx Total</TableHead>
                                <TableHead className="px-5 py-3.5 text-xs font-semibold tracking-wider uppercase text-muted-foreground bg-muted"></TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {allocations.map((a) => (
                                <TableRow key={a.id} className="border-b-border/60 hover:bg-muted/60">
                                    <TableCell className="px-5 py-4 text-sm">{formatDate(a.transaction.paid_at)}</TableCell>
                                    <TableCell className="px-5 py-4 text-sm max-w-[400px] truncate" title={a.transaction.label}>{a.transaction.label}</TableCell>
                                    <TableCell className="px-5 py-4 text-sm text-right font-mono font-semibold text-emerald-600 dark:text-emerald-600-400">
                                        {formatMoney(a.amount, salaryMonth.currency)}
                                        <span className="text-xs text-muted-foreground dark:text-slate-400 ml-1">{salaryMonth.currency}</span>
                                    </TableCell>
                                    <TableCell className="px-5 py-4 text-sm text-right font-mono text-muted-foreground dark:text-slate-400">{formatMoney(a.transaction.amount)}</TableCell>
                                    <TableCell className="px-5 py-4 text-sm text-right">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => modals.openTransaction(a.transaction as unknown as import('@/lib/types').Transaction)}
                                        >
                                            Edit
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}
            </div>
        </>
    );
}

(SalaryMonthsShow as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
