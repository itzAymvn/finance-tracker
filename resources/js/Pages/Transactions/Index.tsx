import { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Plus, Search } from 'lucide-react';
import type { TransactionsIndexProps } from '@/lib/types';
import { formatMoney } from '@/lib/format';
import { useModals } from '@/contexts/ModalContext';
import { AppLayout } from '@/components/AppLayout';
import { TransactionsTable } from '@/components/TransactionsTable';
import { Pagination } from '@/components/Pagination';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

export default function TransactionsIndex() {
    const { transactions, years, summary } = usePage<TransactionsIndexProps>().props;
    const modals = useModals();

    const params = new URLSearchParams(window.location.search);
    const [search, setSearch] = useState(params.get('search') ?? '');
    const [type, setType] = useState(params.get('type') ?? '');
    const [month, setMonth] = useState(params.get('month') ?? '');
    const [year, setYear] = useState(params.get('year') ?? '');

    const hasFilters = search || type || month || year;

    function applyFilters() {
        const p: Record<string, string> = {};
        if (search) p.search = search;
        if (type) p.type = type;
        if (month) p.month = month;
        if (year) p.year = year;
        router.get('/transactions', p, { preserveState: true });
    }

    function clearFilters() {
        setSearch('');
        setType('');
        setMonth('');
        setYear('');
        router.get('/transactions');
    }

    return (
        <>
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">Transactions</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">View and manage all your financial transactions</p>
                </div>
                <div className="flex gap-2">
                    <Button onClick={() => modals.openTransaction()}>
                        <Plus className="w-4 h-4" />
                        New Transaction
                    </Button>
                </div>
            </div>

            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <Card>
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-1.5">Showing</p>
                        <p className="font-mono text-xl font-medium text-foreground dark:text-white">{summary.count} <span className="text-xs text-muted-foreground dark:text-slate-400 font-normal">tx</span></p>
                    </CardContent>
                </Card>
                <Card className="relative overflow-hidden">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-1.5">Credits</p>
                        <p className="font-mono text-xl font-medium text-emerald-600 dark:text-emerald-600-400">+{formatMoney(summary.credits)}</p>
                    </CardContent>
                    <div className="absolute bottom-0 left-0 right-0 h-[3px] bg-emerald"></div>
                </Card>
                <Card className="relative overflow-hidden">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-1.5">Debits</p>
                        <p className="font-mono text-xl font-medium text-red-600 dark:text-red-400">{formatMoney(summary.debits)}</p>
                    </CardContent>
                    <div className="absolute bottom-0 left-0 right-0 h-[3px] bg-red-500"></div>
                </Card>
                <Card className="relative overflow-hidden">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-1.5">Net</p>
                        <p className={`font-mono text-xl font-medium ${summary.net >= 0 ? 'text-emerald-600 dark:text-emerald-600-400' : 'text-red-600 dark:text-red-400'}`}>
                            {summary.net >= 0 ? '+' : ''}{formatMoney(summary.net)}
                        </p>
                    </CardContent>
                    <div className={`absolute bottom-0 left-0 right-0 h-[3px] ${summary.net >= 0 ? 'bg-emerald' : 'bg-red-500'}`}></div>
                </Card>
            </div>

            <div className="bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm overflow-hidden">
                <div className="flex flex-wrap items-center gap-2 px-6 py-4 border-b border-border dark:border-border bg-muted/50 dark:bg-muted/50">
                    <div className="relative flex-1 min-w-[200px]">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/70 pointer-events-none" />
                        <Input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Search label..."
                            className="h-7 text-xs pl-9"
                            onKeyDown={(e) => { if (e.key === 'Enter') applyFilters(); }}
                        />
                    </div>
                    <select
                        value={type}
                        onChange={(e) => setType(e.target.value)}
                        className="w-auto h-7 text-xs"
                    >
                        <option value="">All types</option>
                        <option value="credit">Credits only</option>
                        <option value="debit">Debits only</option>
                        <option value="salary">Salary only</option>
                    </select>
                    <Input
                        type="month"
                        value={month}
                        onChange={(e) => setMonth(e.target.value)}
                        title="Specific month"
                        className="w-auto h-7 text-xs font-mono"
                    />
                    <select
                        value={year}
                        onChange={(e) => setYear(e.target.value)}
                        className="w-auto h-7 text-xs"
                    >
                        <option value="">Any year</option>
                        {years.map((y) => (
                            <option key={y} value={y}>{y}</option>
                        ))}
                    </select>
                    <Button size="sm" onClick={applyFilters}>Apply</Button>
                    {hasFilters && (
                        <Button variant="ghost" size="sm" onClick={clearFilters}>Clear</Button>
                    )}
                </div>

                <TransactionsTable transactions={transactions.data} />

                <Pagination links={transactions.links} />
            </div>
        </>
    );
}

(TransactionsIndex as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
