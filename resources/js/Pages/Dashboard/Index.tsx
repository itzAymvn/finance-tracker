import { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import type { DashboardProps } from '@/lib/types';
import { formatMoney, formatMoneyInteger } from '@/lib/format';
import { useModals } from '@/contexts/ModalContext';
import { AppLayout } from '@/components/AppLayout';
import { SalaryMonthsTable } from '@/components/SalaryMonthsTable';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

export default function DashboardIndex() {
    const { months, totalExpected, totalPaid, totalRemaining, toDateExpected, toDatePaid, toDateRemaining, toDateLabel, years, currentBalance } = usePage<DashboardProps>().props;
    const modals = useModals();

    const [status, setStatus] = useState('');
    const [year, setYear] = useState('');
    const [from, setFrom] = useState('');
    const [to, setTo] = useState('');

    const hasFilters = status || year || from || to;

    function applyFilters() {
        const params: Record<string, string> = {};
        if (status) params.status = status;
        if (year) params.year = year;
        if (from) params.from = from;
        if (to) params.to = to;
        router.get('/', params, { preserveState: true });
    }

    function clearFilters() {
        setStatus('');
        setYear('');
        setFrom('');
        setTo('');
        router.get('/');
    }

    const totalPct = totalExpected > 0 ? Math.round((totalPaid / totalExpected) * 100) : 0;
    const balanceHealthy = currentBalance >= 0;

    return (
        <>
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">Overview</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">Salary balance and month-by-month progress</p>
                </div>
                <div className="flex gap-2">
                    <Button variant="outline" render={<Link href="/transactions" />}>View Transactions</Button>
                    <Button onClick={() => modals.openTransaction()}>
                        <Plus className="w-4 h-4" />
                        New Transaction
                    </Button>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <Card className="lg:col-span-2">
                    <CardContent>
                        <div className="flex items-center justify-between mb-4">
                            <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground">Current Balance</p>
                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${balanceHealthy ? 'bg-emerald-600/10 text-emerald-600' : 'bg-red-500/10 text-red-600'}`}>
                                {balanceHealthy ? 'Healthy' : 'Negative'}
                            </span>
                        </div>
                        <p className="font-mono text-4xl lg:text-5xl font-light tracking-tight mb-2">
                            {formatMoney(Math.abs(currentBalance))}
                        </p>
                        <p className="text-sm text-muted-foreground">Updated {new Date().toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent className="flex flex-col">
                        <div className="flex items-center justify-between mb-4">
                            <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400">Salary Progress</p>
                            <span className="text-xs font-mono text-muted-foreground dark:text-slate-400">{totalPaid >= totalExpected ? 'On track' : 'Behind'}</span>
                        </div>
                        <div className="flex items-baseline gap-1 mb-4">
                            <span className="font-mono text-2xl font-semibold text-emerald-600 dark:text-emerald-600-400">{formatMoneyInteger(totalPaid)}</span>
                            <span className="text-muted-foreground dark:text-slate-400">/</span>
                            <span className="font-mono text-xl text-muted-foreground dark:text-slate-400">{formatMoneyInteger(totalExpected)}</span>
                        </div>
                        <p className="text-xs text-muted-foreground dark:text-slate-400 mb-4">{formatMoney(totalRemaining)} MAD remaining across {months.length} month(s)</p>
                        <div className="mt-auto">
                            <div className="flex items-center justify-between text-xs font-mono text-muted-foreground dark:text-slate-400 mb-1.5">
                                <span>{totalPct}% funded</span>
                                <span>{100 - Math.min(100, totalPct)}% to go</span>
                            </div>
                            <div className="h-2 bg-muted rounded-full overflow-hidden">
                                <div
                                    className="h-full rounded-full bg-primary transition-all duration-700"
                                    style={{ width: `${Math.min(100, totalPct)}%` }}
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card className="mb-8">
                <CardContent className="flex items-center justify-between gap-4 flex-wrap">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-lg bg-primary/10 text-primary dark:text-primary flex items-center justify-center shrink-0">
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div>
                            <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400">Through {toDateLabel}</p>
                            <p className="text-sm text-muted-foreground dark:text-slate-400">Salary status up to and including the current month</p>
                        </div>
                    </div>
                    <div className="flex items-center gap-6 font-mono text-sm">
                        <div className="text-right">
                            <p className="text-xs uppercase tracking-wider text-muted-foreground dark:text-slate-400">Paid</p>
                            <p className="text-emerald-600 dark:text-emerald-600-400 font-semibold">{formatMoney(toDatePaid)}</p>
                        </div>
                        <div className="text-right">
                            <p className="text-xs uppercase tracking-wider text-muted-foreground dark:text-slate-400">Expected</p>
                            <p className="text-foreground dark:text-white font-semibold">{formatMoney(toDateExpected)}</p>
                        </div>
                        <div className="text-right">
                            <p className="text-xs uppercase tracking-wider text-muted-foreground dark:text-slate-400">Remaining</p>
                            <p className={`${toDateRemaining > 0 ? 'text-red-600 dark:text-red-400' : 'text-muted-foreground dark:text-slate-400'} font-semibold`}>{formatMoney(toDateRemaining)}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div className="bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm overflow-hidden">
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 px-6 py-4 border-b border-border dark:border-border">
                    <div>
                        <h2 className="text-lg font-semibold text-foreground dark:text-white">Salary Months</h2>
                        <p className="text-sm text-muted-foreground dark:text-slate-400 mt-0.5">Each month fills FIFO from tagged salary credits</p>
                    </div>
                    <div className="flex flex-wrap items-center gap-2">
                        <select
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                            className="w-auto h-7 text-xs"
                        >
                            <option value="">All statuses</option>
                            <option value="paid">Paid</option>
                            <option value="partial">Partial</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="overpaid">Overpaid</option>
                        </select>
                        <select
                            value={year}
                            onChange={(e) => setYear(e.target.value)}
                            className="w-auto h-7 text-xs"
                        >
                            <option value="">All years</option>
                            {(years ?? []).map((y) => (
                                <option key={y} value={y}>{y}</option>
                            ))}
                        </select>
                        <Input
                            type="month"
                            value={from}
                            onChange={(e) => setFrom(e.target.value)}
                            title="From month"
                            className="w-auto h-7 text-xs font-mono"
                        />
                        <span className="text-muted-foreground dark:text-slate-400 text-xs">–</span>
                        <Input
                            type="month"
                            value={to}
                            onChange={(e) => setTo(e.target.value)}
                            title="To month"
                            className="w-auto h-7 text-xs font-mono"
                        />
                        <Button size="sm" onClick={applyFilters}>Apply</Button>
                        {hasFilters && (
                            <Button variant="ghost" size="sm" onClick={clearFilters}>Clear</Button>
                        )}
                    </div>
                </div>

                <SalaryMonthsTable months={months} />
            </div>
        </>
    );
}

(DashboardIndex as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
